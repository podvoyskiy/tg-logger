<?php

namespace Podvoyskiy\TgLogger\storage;

class StorageApcu implements Storage
{
    public function add(string $message, int $ttl): void
    {
        apcu_add('tg_logger_' . sha1($message), 1, $ttl);
    }

    public function exists(string $message): bool
    {
        return apcu_exists('tg_logger_' . sha1($message));
    }

    public function enable(): bool
    {
        return function_exists('apcu_enabled') && apcu_enabled();
    }
}