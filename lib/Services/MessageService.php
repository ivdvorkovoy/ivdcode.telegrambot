<?php
/**
 * @author Ivan Dvorkovoy
 * @copyright Copyright (c) 2023 Ivan Dvorkovoy
 * @license @link https://github.com/ivdvorkovoy/ivdcode.telegrambot/blob/master/LICENSE GPL-3.0 License
 */

namespace IVDCode\TelegramBot\Services;

use IVDCode\TelegramBot\Settings\Setting;

/**
 * Class MessageService
 */
class MessageService
{
    /**
     * @param string $chatId
     * @param string $message
     * @return void
     */
    public static function sendMessage(string $chatId, string $message): bool
    {
        $request = new RequestService();

        $result = $request->send_request(Setting::getMethod('sendMessage'), [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
        ]);

        return $result['ok'] ?? true;
    }
}
