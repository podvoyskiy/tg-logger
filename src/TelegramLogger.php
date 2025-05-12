<?php

namespace Podvoyskiy\TgLogger;

use Exception;

class TelegramLogger
{
    private const URI = 'https://api.telegram.org/bot%s/%s';

    private const METHOD_SEND_MESSAGE = 'sendMessage';
    private const METHOD_SEND_DOCUMENT = 'sendDocument';
    private const METHOD_GET_ME = 'getMe';

    /**
     * @desc need override
     */
    protected const TOKEN = '';

    /**
     * @desc need override
     */
    protected array $chatsIds = [];

    /**
     * @desc override if you need a limit on same messages (in sec.)
     */
    protected const TTL = 0;

    /**
     * @desc set to empty if you want to always send messages
     */
    protected const WORKING_HOURS_RANGE = [9, 18];

    /**
     * @desc Maximum depth of backtrace calls included in messages. If set to 0, no backtrace will be shown.
     */
    protected const BACKTRACE_DEPTH = 1;

    private static ?TelegramLogger $instance = null; //singleton
    private ?string $instanceError;

    protected function __construct()
    {
        $this->instanceError = $this->_instanceError();
    }

    public static function send(string|array $subscribers, string|array|object $message, LogLevel $level = LogLevel::INFO): void
    {
        if (!self::_init()) return;
        if (!is_string($message)) $message = json_encode($message, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if (static::TTL > 0 && apcu_exists('tg_logger_' . sha1($message))) return; //the same message has already been sent
        if (str_contains($message, '>') || str_contains($message, '<')) $message = htmlspecialchars($message);

        $message = $level->toString() . self::_addBackTrace() . "\n$message";

        if (strlen($message) > 4096) {
            self::sendDoc($subscribers, self::_messageToFile($message), true);
            return;
        }

        if (!is_array($subscribers)) $subscribers = [$subscribers];
        foreach ($subscribers as $subscriber) {
            $chatId = self::$instance->chatsIds[$subscriber] ?? null;
            if (!$chatId) continue;
            self::_request(self::METHOD_SEND_MESSAGE, ['parse_mode'=> 'html', 'chat_id' => $chatId, 'text' => $message]);
        }

        if (static::TTL > 0) apcu_add('tg_logger_' . sha1($message), 1, static::TTL);
    }

    public static function sendDoc(string|array $subscribers, string $pathToFile, bool $deleteFileAfterSend = false): void
    {
        if (!is_file($pathToFile) || !self::_init()) return;
        $curlFile = curl_file_create($pathToFile, mime_content_type($pathToFile), basename($pathToFile));

        if (!is_array($subscribers)) $subscribers = [$subscribers];
        foreach ($subscribers as $subscriber) {
            $chatId = self::$instance->chatsIds[$subscriber] ?? null;
            if (!$chatId) continue;
            self::_request(self::METHOD_SEND_DOCUMENT, ['chat_id' => $chatId, 'caption' => __DIR__, 'document' => $curlFile]);
        }

        if ($deleteFileAfterSend) unlink($pathToFile);
    }

    private static function _request(string $method, ?array $params = null): array
    {
        try {
            $url = sprintf(self::URI, static::TOKEN, $method);
            $curl = curl_init($url);
            curl_setopt_array($curl, [
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_POST => 1,
                CURLOPT_RETURNTRANSFER => 1,
            ]);
            if ($params) curl_setopt($curl, CURLOPT_POSTFIELDS, $params);

            $response = curl_exec($curl);
            curl_close($curl);
            if (empty($response)) {
                LogLevel::ERROR->toOutput('empty response curl');
                return [];
            }
            $response = json_decode($response, true);
            if (isset($response['ok']) && $response['ok'] === false) {
                LogLevel::ERROR->toOutput($response['description']);
            }
            return $response;
        } catch (Exception $ex) {
            LogLevel::ERROR->toOutput($ex->getMessage());
            return [];
        }
    }

    private static function _messageToFile(string $message): string
    {
        $pathToFile = tempnam(sys_get_temp_dir(), 'tmp_tg_logger_file_');
        file_put_contents($pathToFile, $message);
        return $pathToFile;
    }

    private static function _init(): bool
    {
        if (self::$instance === null) self::$instance = new static();
        if (!is_null(self::$instance->instanceError)) {
            LogLevel::ERROR->toOutput(self::$instance->instanceError);
            return false;
        }
        if (!empty(static::WORKING_HOURS_RANGE) && (date('G') < static::WORKING_HOURS_RANGE[0] || date('G') > static::WORKING_HOURS_RANGE[1])) return false;
        return true;
    }

    private function _instanceError(): ?string
    {
        if (!preg_match('/^\d+:\w+$/', static::TOKEN)) return 'incorrect token telegram';

        if (empty(self::_request(self::METHOD_GET_ME)['ok'])) return 'invalid token telegram';

        if (empty($this->chatsIds)) return 'list subscribers is empty';

        if (!in_array(count(static::WORKING_HOURS_RANGE), [0, 2])
            || count(array_filter(static::WORKING_HOURS_RANGE, 'is_int')) !== count(static::WORKING_HOURS_RANGE)) {
            return 'incorrect const WORKING_HOURS_RANGE';
        }

        if (!is_int(static::BACKTRACE_DEPTH)) return 'incorrect const BACKTRACE_DEPTH';

        if (static::TTL > 0 && (!function_exists('apcu_enabled') || apcu_enabled() === false)) return 'apcu extension not supported';

        return null;
    }

    private static function _addBackTrace(): string
    {
        if (static::BACKTRACE_DEPTH <= 0) return '';
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, static::BACKTRACE_DEPTH + 2);
        $backtrace = array_filter($backtrace, function ($trace) { return !str_contains($trace['file'], 'TelegramLogger'); });
        if (count($backtrace) > static::BACKTRACE_DEPTH) array_splice($backtrace, -(count($backtrace) - static::BACKTRACE_DEPTH));

        $formattedTrace = array_map(function ($trace) {
            return ($trace['file'] ?? '?') . ':' . ($trace['line'] ?? '?') . ' → ' . ($trace['function'] ?? '?');
        }, $backtrace);

        return "\n<code>" . implode("\n", $formattedTrace) . "</code>\n";
    }

    public static function debug(string|array $subscribers, string|array|object $message): void
    {
        self::send($subscribers, $message, LogLevel::DEBUG);
    }

    public static function warning(string|array $subscribers, string|array|object $message): void
    {
        self::send($subscribers, $message, LogLevel::WARNING);
    }

    public static function error(string|array $subscribers, string|array|object $message): void
    {
        self::send($subscribers, $message, LogLevel::ERROR);
    }

    public static function critical(string|array $subscribers, string|array|object $message): void
    {
        self::send($subscribers, $message, LogLevel::CRITICAL);
    }
}