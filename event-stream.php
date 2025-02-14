<?php
// Include Composer autoload if needed (remove if using a PHP framework that autoloads)
// Make sure to install "orhanerday/open-ai" via Composer.
require __DIR__ . '/vendor/autoload.php';

use Orhanerday\OpenAi\OpenAi;

const ROLE = "role";
const CONTENT = "content";
const USER = "user";
const SYS = "system";
const ASSISTANT = "assistant";

// Use your OpenAI API key (this key is only for ChatGPT calls; tool APIs below are free and keyless)
$open_ai_key = '';
$open_ai = new OpenAi($open_ai_key);

// Open the SQLite database
error_log("Opening SQLite database");
$db = new SQLite3('db.sqlite');

$chat_history_id = isset($_GET['chat_history_id']) ? $_GET['chat_history_id'] : 0;
$id = isset($_GET['id']) ? $_GET['id'] : 0;

// Retrieve the entire chat history (ordered ascending)
error_log("Retrieving chat history");
$results = $db->query('SELECT * FROM main.chat_history ORDER BY id ASC');
$history = [];
$history[] = [ROLE => SYS, CONTENT => "You are a helpful assistant."];

// Append past conversation turns
error_log("Appending past conversation turns");
while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    $history[] = [ROLE => USER, CONTENT => $row['human']];
    $history[] = [ROLE => ASSISTANT, CONTENT => $row['ai']];
}

// Retrieve the current user message using chat_history_id
error_log("Retrieving current user message");
$stmt = $db->prepare('SELECT human FROM main.chat_history WHERE id = :id');
$stmt->bindValue(':id', $chat_history_id, SQLITE3_INTEGER);
$result = $stmt->execute();
$msgRow = $result->fetchArray(SQLITE3_ASSOC);
$msg = $msgRow ? $msgRow['human'] : "";

// Check if the user message triggers a weather tool call
$lowerMsg = strtolower($msg);
if (strpos($lowerMsg, 'weather') !== false) {
    error_log("Weather tool call detected");
    // Try to extract location (e.g., "weather in London"); if not, use a default location
    if (preg_match('/weather\s+in\s+([a-zA-Z\s]+)/i', $msg, $matches)) {
        $location = trim($matches[1]);
    } else {
        $location = "New York";
    }
    $weatherInfo = getWeather($location);
    // Append the weather tool result as a system message so ChatGPT can use it as context
    $history[] = [ROLE => SYS, CONTENT => "Weather tool result for {$location}: " . $weatherInfo];
}

// Check if the user message triggers a stock tool call
if (strpos($lowerMsg, 'stock') !== false || strpos($lowerMsg, 'price') !== false) {
    error_log("Stock tool call detected");
    // Extract the stock symbol (e.g., "stock AAPL"); if not found, use a default symbol
    if (preg_match('/stock\s+([a-zA-Z]+)/i', $msg, $matches)) {
        $symbol = strtoupper(trim($matches[1]));
    } else {
        $symbol = "AAPL";
    }
    $stockInfo = getStock($symbol);
    $history[] = [ROLE => SYS, CONTENT => "Stock tool result for {$symbol}: " . $stockInfo];
}

// Finally, add the current user message to the conversation history
$history[] = [ROLE => USER, CONTENT => $msg];

// Set up options for the ChatGPT API call
$opts = [
    'model' => 'gpt-3.5-turbo',
    'messages' => $history,
    'temperature' => 1.0,
    'max_tokens' => 100,
    'frequency_penalty' => 0,
    'presence_penalty' => 0,
    'stream' => true
];

// Set headers to stream the response
header('Content-type: text/event-stream');
header('Cache-Control: no-cache');

$txt = "";
error_log("Calling OpenAI API");
$complete = $open_ai->chat($opts, function ($curl_info, $data) use (&$txt) {
    $decoded = json_decode($data, true);
    if ($decoded && isset($decoded['error'])) {
        error_log(json_encode($decoded['error']['message']));
    } else {
        // Output each streamed data chunk
        echo $data;
        $results = explode('data: ', $data);
        foreach ($results as $result) {
            if ($result !== '[DONE]' && $result !== '') {
                $arr = json_decode($result, true);
                if (isset($arr["choices"][0]["delta"]["content"])) {
                    $txt .= $arr["choices"][0]["delta"]["content"];
                }
            }
        }
    }
    echo PHP_EOL;
    ob_flush();
    flush();
    return strlen($data);
});

// Update the chat history record with the assistant's reply
error_log("Updating chat history with assistant's reply");
$stmt = $db->prepare('UPDATE main.chat_history SET ai = :ai WHERE id = :id');
$stmt->bindValue(':id', $chat_history_id, SQLITE3_INTEGER);
$stmt->bindValue(':ai', $txt, SQLITE3_TEXT);
$stmt->execute();

$db->close();

/**
 * Retrieves weather information using the free wttr.in service.
 * @param string $location
 * @return string Weather summary.
 */
function getWeather($location) {
    error_log("Retrieving weather information for {$location}");
    $url = "http://wttr.in/" . urlencode($location) . "?format=3";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response !== false ? $response : "Unable to retrieve weather data.";
}

/**
 * Retrieves stock information using the free Stooq API.
 * @param string $symbol
 * @return string Stock summary, including current price if available.
 */
function getStock($symbol) {
    error_log("Retrieving stock information for {$symbol}");
    // The Stooq API returns CSV data for a given symbol
    $url = "https://stooq.com/q/l/?s=" . urlencode($symbol) . "&f=sd2t2ohlcv&h&e=csv";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $csvData = curl_exec($ch);
    curl_close($ch);
    if ($csvData === false) {
        return "Unable to retrieve stock data.";
    }
    // Parse the CSV (first line: headers; second line: values)
    $lines = explode("\n", trim($csvData));
    if (count($lines) < 2) {
        return "No stock data available.";
    }
    $headers = str_getcsv($lines[0]);
    $data = str_getcsv($lines[1]);
    if (count($headers) !== count($data)) {
        return "Invalid stock data format.";
    }
    $stockInfo = array_combine($headers, $data);
    return isset($stockInfo['Close']) ? "Current price: " . $stockInfo['Close'] : "Stock data not found.";
}
?>