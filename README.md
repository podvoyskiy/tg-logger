#### Simple PHP library to forward logs (info, debug, alerts, errors) to Telegram

### Installing:

```
composer require podvoyskiy/tg-logger
```

### Usage:

```
<?php

use Podvoyskiy\TgLogger\TelegramLogger;

class TgLogger extends TelegramLogger
{
    protected const TOKEN = ''; //your telegram token

    protected array $chatsIds = [
        self::EXAMPLE_SUBSCRIBER => '111111111' //telegram id subscriber
    ];
    
    public const EXAMPLE_SUBSCRIBER = 'example_subscriber';
    
    //if you need a limit on same messages (for example 30 min). required apcu extension
    //protected const TTL = 30 * 60; 
    
    //set to empty if you want to always send messages. default [9, 18]
    //protected const WORKING_HOURS_RANGE = [];
    
    //set to 0 if you don't need backtrace in message. default depth : 2
    //protected const BACKTRACE_DEPTH = 0;
}

TgLogger::send(TgLogger::EXAMPLE_SUBSCRIBER, 'Your message');

TgLogger::sendDoc(TgLogger::EXAMPLE_SUBSCRIBER, $pathToFile);
 ```