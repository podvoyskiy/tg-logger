<?php

namespace Podvoyskiy\TgLogger;

class ProxyConfig
{
    public function __construct(
        public readonly string $host,
        public readonly int $port,
        public readonly ?string $user = null,
        public readonly ?string $pass = null,
    ) {}

    public function isValid(): bool
    {
        return !empty($this->host) && $this->port > 0;
    }

    public function hasAuth(): bool
    {
        return $this->user !== null && $this->pass !== null;
    }
}