#### Simple PHP library to forward logs (info, debug, alerts, errors) to Telegram

### Installing:

```
composer require podvoyskiy/tg-logger
```

### Usage:

```
<?php

use Podvoyskiy\TgLogger\TelegramLogger;

class Telegram extends TelegramLogger
{
    protected const TOKEN = ''; //your telegram token

    protected array $chatsIds = [
        self::EXAMPLE_SUBSCRIBER => '111111111' //telegram id subscriber
    ];
    
    public const EXAMPLE_SUBSCRIBER = 'example_subscriber';
    
    //if you need a limit on same messages (for example 30 min). required apcu extension
    //protected const TTL = 30 * 60; 
    
    //set values if you need to send messages only at certain times
    //protected const WORKING_HOURS_RANGE = [9, 18];
    
    //set to 0 if you don't need backtrace in message. default depth : 1
    //protected const BACKTRACE_DEPTH = 0;
    
    //List here the classes that should be excluded from backtrace
    //protected const EXCLUDED_CLASSES_FROM_BACKTRACE = [SomeClass::class];
}

Telegram::send(Telegram::EXAMPLE_SUBSCRIBER, 'Your message', LogLevel::INFO);

Telegram::sendDoc(Telegram::EXAMPLE_SUBSCRIBER, $pathToFile);
 ```