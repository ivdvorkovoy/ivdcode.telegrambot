<?php
/**
 * @author Ivan Dvorkovoy
 * @copyright Copyright (c) 2023 Ivan Dvorkovoy
 * @license @link https://github.com/ivdvorkovoy/ivdcode.telegrambot/blob/master/LICENSE GPL-3.0 License
 */

namespace IVDCode\TelegramBot\Services;

use IVDCode\TelegramBot\Settings\Setting;

/**
 * Class WebhookService
 */

class WebhookService
{
    /**
     * @return array
     */
    public static function getWebhookStatus(): array
    {
        $status = [
            'color' => 'red',
            'description' => 'Бот не подключен'
        ];

        $url = Setting::getMethod('webhookInfo');

        $request = new RequestService();

        $result = $request->send_request($url);

        if (!$result['ok']) {
            return $status;
        }

        $webhookUrl = $result['result']['url'];
        $currentUrl = Setting::getCallbackUrl();

        if (strlen($webhookUrl) <= 0) {
            return $status;
        }

        if ($webhookUrl !== $currentUrl) {
            $status['color'] = 'blue';
            $status['description'] = 'Бот подключен на другой url: ' . $webhookUrl;
        } else {
            $status['color'] = 'green';
            $status['description'] = 'Бот подключен';
        }

        return $status;

    }

    /**
     * @return bool
     */
    public static function setWebhook(): bool
    {
        $url = Setting::getMethod('setWebhook') . Setting::getCallbackUrl();

        $request = new RequestService();

        $result = $request->send_request($url);

        if ($result['ok']) {
            return $result['result'];
        }

        return $result['ok'];
    }

    /**
     * @return bool
     */
    public static function deleteWebhook(): bool
    {
        $url = Setting::getMethod('deleteWebhook');

        $request = new RequestService();

        $result = $request->send_request($url);

        if ($result['ok']) {
            return $result['result'];
        }

        return $result['ok'];
    }
}