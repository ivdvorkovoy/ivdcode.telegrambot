<?php
/**
 * @author Ivan Dvorkovoy
 * @copyright Copyright (c) 2023 Ivan Dvorkovoy
 * @license @link https://github.com/ivdvorkovoy/ivdcode.telegrambot/blob/master/LICENSE GPL-3.0 License
 */

namespace IVDCode\TelegramBot\Agents;

use IVDCode\TelegramBot\Services\LogService;
use IVDCode\TelegramBot\Services\QueueService;
use Exception;

/**
 * Class QueueAgent
 */
class QueueAgent
{
    /**
     * @return void
     */
    public static function getMessageQueue(): string
    {
        try {
            QueueService::getQueue();
        } catch (Exception $e) {
            LogService::log($e->getMessage(), 'error');
        }
        return '\\' . __METHOD__ . '();';
    }

    /**
     * @return string
     */
    public static function processMessageQueue(): string
    {
        try {
            QueueService::processQueue();
        } catch (Exception $e) {
            LogService::log($e->getMessage(), 'error');
        }
        return '\\' . __METHOD__ . '();';
    }

}
