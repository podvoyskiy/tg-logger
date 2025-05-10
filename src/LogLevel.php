<?php

namespace Podvoyskiy\TgLogger;

enum LogLevel {
    case DEBUG;
    case INFO;
    case WARNING;
    case ERROR;
    case CRITICAL;

    public function toString() : string
    {
        return match($this) {
            self::DEBUG => "<s>[DEBUG]</s>\n",
            self::INFO => "[INFO]\n",
            self::WARNING => "<i>[WARNING]</i>\n",
            self::ERROR => "<b>[ERROR]</b>\n",
            self::CRITICAL => "<span class='tg-spoiler'>[CRITICAL]</span>\n",
        };
    }

    public function toOutput(string $text) : void
    {
        $color = match($this) {
            self::DEBUG => 35,
            self::INFO => 36,
            self::WARNING => 33,
            self::ERROR, self::CRITICAL => 31,
        };

        echo date('Y-m-d H:i:s') . " \033[{$color}m$text \033[0m\n";
    }
}