<?php

namespace Podvoyskiy\TgLogger\storage;

use Podvoyskiy\TgLogger\LogLevel;

class StorageRedis extends Storage
{
    private \Redis $redis;

    public function add(string $message, int $ttl): void
    {
        $this->redis->setex('tg_logger_' . sha1($message), $ttl, 1);
    }

    public function exists(string $message): bool
    {
        return $this->redis->exists('tg_logger_' . sha1($message));
    }

    public function enable(): bool
    {
        if (!extension_loaded('redis') || !class_exists('Redis')) return false;
        try {
            $this->redis = new \Redis();
            return $this->redis->connect('127.0.0.1', 6379, 2) && $this->redis->ping() === true;
        } catch (\Exception $e) {
            LogLevel::ERROR->toOutput($e->getMessage());
            return false;
        }
    }
}