<?php
/**
 * @author Ivan Dvorkovoy
 * @copyright Copyright (c) 2023 Ivan Dvorkovoy
 * @license @link https://github.com/ivdvorkovoy/ivdcode.telegrambot/blob/master/LICENSE GPL-3.0 License
 */

namespace IVDCode\TelegramBot\Settings;

use Bitrix\Main\Config\Option;

/**
 *  Class Setting
 */
class Setting
{
    public static array $settings = [
        "general" => [
            "module" => "ivdcode.telegrambot",
            "hl" => [
                "subscribers" => "TelegramUsers"
            ],
            "base_url" => "https://api.telegram.org/bot",
            "method" => [
                "sendMessage" => "/sendMessage",
                "setWebhook" => "/setWebhook?url=",
                "deleteWebhook" => "/deleteWebhook",
                "webhookInfo" => "/getWebhookInfo",
            ],
        ]
    ];

    public static function getModuleCode(): string
    {
        return static::$settings["general"]["module"];
    }

    public static function getCodeHL(string $code): string
    {
        return static::$settings["general"]["hl"][$code];
    }

    public static function getMethod(string $code): string
    {
        return static::$settings["general"]["method"][$code];
    }

    public static function getApiToken(): ?string
    {
        return Option::get(static::getModuleCode(), "api_token");
    }

    public static function setApiToken(string $token = ""): void
    {
        Option::set(static::getModuleCode(), "api_token", $token);
    }

    public static function getBotUrl(): ?string
    {
        return Option::get(static::getModuleCode(), "bot_url");
    }

    public static function setBotUrl(string $botUrl): void
    {
        Option::set(static::getModuleCode(), "bot_url", $botUrl);
    }

    public static function getBaseUrl(): string
    {
        return static::$settings["general"]["base_url"] . static::getApiToken();
    }

    public static function getBotStartUrl(): string
    {
        return static::getBotUrl() . "?start";
    }

    public static function getCallbackUrl(): string
    {
        return "https://" . $_SERVER['HTTP_HOST'] . "/api/ivdcode_telegram.php";
    }

}