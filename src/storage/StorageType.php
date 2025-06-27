<?php

namespace Podvoyskiy\TgLogger\storage;

enum StorageType
{
    case APCU;
    case REDIS;
}