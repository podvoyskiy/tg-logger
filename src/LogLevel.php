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
            self::DEBUG => "~\[DEBUG\]~\n",
            self::INFO => "\[INFO\]\n",
            self::WARNING => "_\[WARNING\]_\n",
            self::ERROR => "*\[ERROR\]*\n",
            self::CRITICAL => "||\[CRITICAL\]||\n",
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