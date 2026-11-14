<?php

namespace Atlas\Core;

class Config
{
    private static array $config = [];

    public static function load(): void
    {
        if (empty(self::$config)) {
            self::$config = require __DIR__ . '/../../config/config.php';
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::load();

        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $part) {
            if (!is_array($value) || !array_key_exists($part, $value)) {
                return $default;
            }

            $value = $value[$part];
        }

        return $value;
    }
}