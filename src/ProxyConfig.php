<?php

namespace Podvoyskiy\TgLogger;

class ProxyConfig
{
    public const TYPE_SOCKS5 = CURLPROXY_SOCKS5;
    public const TYPE_SOCKS5_HOSTNAME = CURLPROXY_SOCKS5_HOSTNAME;
    public const TYPE_HTTP = CURLPROXY_HTTP;

    public function __construct(
        public readonly string $host,
        public readonly int $port,
        public readonly int $type = self::TYPE_SOCKS5_HOSTNAME,
        public readonly ?string $user = null,
        public readonly ?string $pass = null,
    ) {}

    public function isValid(): bool
    {
        if (empty($this->host) || $this->port <= 0) return false;

        return in_array($this->type, [CURLPROXY_SOCKS5, CURLPROXY_SOCKS5_HOSTNAME, CURLPROXY_HTTP]);
    }

    public function hasAuth(): bool
    {
        return $this->user !== null && $this->pass !== null;
    }
}