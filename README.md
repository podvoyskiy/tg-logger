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
    
    //if you need set cache storage for same messages. required redis/apcu extension (StorageType::REDIS|StorageType::APCU)
    //protected const CURRENT_STORAGE = StorageType::REDIS; 
    
    //if you need global setting limit on same messages (for example 30 min)
    //protected const TTL = 30 * 60; 
    
    //set values if you need to send messages only at certain times
    //protected const WORKING_HOURS_RANGE = [9, 18];
    
    //set to 0 if you don't need backtrace in message. default depth : 1
    //protected const BACKTRACE_DEPTH = 0;
    
    //List here the classes that should be excluded from backtrace
    //protected const EXCLUDED_CLASSES_FROM_BACKTRACE = [SomeClass::class];
}

Telegram::send(Telegram::EXAMPLE_SUBSCRIBER, 'Your message', LogLevel::INFO);

Telegram::warning(Telegram::EXAMPLE_SUBSCRIBER, 'Your warning', 60); //60 - ttl for this message

Telegram::sendDoc(Telegram::EXAMPLE_SUBSCRIBER, $pathToFile);
 ```