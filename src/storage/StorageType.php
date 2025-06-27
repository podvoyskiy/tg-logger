<?php

namespace Podvoyskiy\TgLogger\storage;

enum StorageType: string
{
    case APCU = 'apcu';
    case REDIS = 'redis';
}