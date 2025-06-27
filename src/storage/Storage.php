<?php

namespace Podvoyskiy\TgLogger\storage;

use Podvoyskiy\TgLogger\LogLevel;

abstract class Storage
{
    public static function create(StorageType $storageType): StorageRedis|StorageApcu|null
    {
        $storage = match ($storageType) {
            StorageType::REDIS => new StorageRedis(),
            StorageType::APCU => new StorageApcu(),
        };
        if (!$storage->enable()) {
            LogLevel::WARNING->toOutput('Storage ' . $storageType->name . ' not supported');
            return null;
        }
        return $storage;
    }

    abstract protected function enable() : bool;

    abstract public function add(string $message, int $ttl) : void;

    abstract public function exists(string $message): bool;
}