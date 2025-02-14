# ChatGPT Clone

ChatGPT clone is a simple web application powered by the OpenAI library and built with PHP. It allows users to chat with an AI language model that responds in real-time. Chat history is saved using cookies, and the project requires the use of an API key and enabled SQLite3.

## Table of Contents
- [Prerequisites](#prerequisites)
- [Get Started](#get-started)
  - [Enable sqlite3](#enable-sqlite3)
  - [Clone this repository](#clone-this-repository)
  - [Navigate to the project directory](#navigate-to-the-project-directory)
  - [Install OrhanErday/OpenAI](#install-orhanerdayopenai)
  - [Set your OpenAI API key](#set-your-openai-api-key)
  - [Start the PHP built-in web server](#start-the-php-built-in-web-server)
  - [Open your web browser](#open-your-web-browser)
- [Live Demo Video](#live-demo-video)
- [Important Notice](#important-notice)
- [Donation](#donation)
- [Join our discord server](#join-our-discord-server)
- [GPT-4](#gpt-4)
- [Using Docker](#using-docker)
  - [Method I](#method-i)
  - [Method II](#method-ii)
- [Chat History](#chat-history)
- [Tools Explanation](#tools-explanation)
- [Credits](#credits)

## Prerequisites
Before running this project, you should have the following:

* PHP 7.4 or later with SQLite3 enabled
* Composer
* An OpenAI API key (which should be set to the $open_ai_key variable in event-stream.php)

## Get Started

### Enable sqlite3

* Open the php.ini file. This file is usually located in the PHP installation directory.
* Find the following line: ;extension=php_sqlite3.dll
* Remove the semicolon at the beginning of the line to uncomment it.
* Save the file.
* Restart the web server.

### Clone this repository to your local machine
```sh
git clone https://github.com/orhanerday/ChatGPT.git
```

### Navigate to the project directory
```sh
cd ChatGPT
```

### Install OrhanErday/OpenAI
```sh
composer require orhanerday/open-ai
```

### Set your OpenAI API key as the `$open_ai_key` variable in `event-stream.php`
```php
$open_ai_key = ""; 
```

### Start the PHP built-in web server
```sh
php -S localhost:8000 -t .
```

### Open your web browser and go to http://localhost:8000

You should now see the ChatGPT clone interface, where you can chat with the OpenAI language model.

<hr>

<div align="center">

![ezgif-1-92e240a6d3](https://user-images.githubusercontent.com/22305274/220125119-ccbdb855-bdb9-476f-8f5f-f5d5530f0a24.gif)

</div>

This project is a ChatGPT clone that allows users to chat with an AI language model trained by OpenAI. It's powered by the github.com/orhanerday/OpenAI php library, which provides an easy-to-use interface for communicating with the OpenAI API.

![Image](https://user-images.githubusercontent.com/22305274/219878523-6d8be435-35df-4cce-b2cd-52334f9e7f12.png)

### Live Demo Video
<br>

https://user-images.githubusercontent.com/22305274/219877050-e5237734-4635-46f8-bf49-71a26356e0db.mp4

# Important Notice
This project was created to highlight the [Stream Example](https://github.com/orhanerday/open-ai#stream-example) feature of [OpenAI GPT-3 Api Client in PHP by Orhan Erday](https://github.com/orhanerday/open-ai), please don't have too high expectations about the project.

## Donation

<a href="https://www.buymeacoffee.com/orhane" target="_blank"><img src="https://www.buymeacoffee.com/assets/img/custom_images/orange_img.png" alt="Buy Me A Coffee" style="height: 41px !important;width: 174px !important;box-shadow: 0px 3px 2px 0px rgba(190, 190, 190, 0.5) !important;-webkit-box-shadow: 0px 3px 2px 0px rgba(190, 190, 190, 0.5) !important;" ></a>

## Join our discord server

![Discord Banner 2](https://discordapp.com/api/guilds/1047074572488417330/widget.png?style=banner2)

[Click here to join the Discord server](https://discord.gg/xpGUD528XJ)

## GPT-4
Change model at `event-stream.php`
```php
....
$chat = $open_ai->chat([
    'model' => 'gpt-4',

....
```

## Using Docker
<hr>

> #### Method I

#### Clone this repository to your local machine
```sh
git clone https://github.com/orhanerday/ChatGPT.git
```
#### Navigate to the project directory
```sh
cd ChatGPT
```
#### Build the image
```shell
docker build -t chatgpt .
```
#### Run the app
```shell
docker run -p 8000:8000 -e OPENAI_API_KEY=sk-o7hL4nCDcjw chatgpt
```
#### Open your web browser and go

http://localhost:8000
<hr>

> #### Method II

### *Or* you can use docker hub without cloning or building;  

#### Pull the image from Docker Hub

```shell
docker pull orhan55555/chatgpt
```

#### Run the app
```shell
docker run -p 8000:8000 -e OPENAI_API_KEY=sk-o7hL4nCDcjw orhan55555/chatgpt
```
#### Open your web browser and go

http://localhost:8000
<hr>

## Chat History
This project saves chat history using cookies by default. If you want to change this to use authentication instead, you can modify the code in index.php to save chat history in a database or other storage mechanism.

## Tools Explanation
This project includes additional tools to enhance the ChatGPT clone functionality:

### Weather Tool
The weather tool retrieves weather information using the free wttr.in service. If a user message contains the word "weather", the tool extracts the location (e.g., "weather in London") and retrieves the weather information for that location. If no location is specified, it defaults to "New York".

### Stock Tool
The stock tool retrieves stock information using the free Stooq API. If a user message contains the word "stock" or "price", the tool extracts the stock symbol (e.g., "stock AAPL") and retrieves the stock information for that symbol. If no symbol is specified, it defaults to "AAPL".

## Credits
This project is powered by the github.com/orhanerday/OpenAI php library, which provides an easy-to-use interface for communicating with the OpenAI API.