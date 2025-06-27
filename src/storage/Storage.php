<?php

namespace Podvoyskiy\TgLogger\storage;

interface Storage
{
    public function enable() : bool;

    public function add(string $message, int $ttl) : void;

    public function exists(string $message): bool;
}