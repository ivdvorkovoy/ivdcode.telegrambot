<?php
/**
 * @author Ivan Dvorkovoy
 * @copyright Copyright (c) 2023 Ivan Dvorkovoy
 * @license @link https://github.com/ivdvorkovoy/ivdcode.telegrambot/blob/master/LICENSE GPL-3.0 License
 */

namespace IVDCode\TelegramBot\Services;

use CEventLog;

/**
 * Class LogService
 */
class LogService
{
    /**
     * @param string $message
     * @param string $type
     * @param string $auditType
     * @return void
     */
    public static function log(string $message, string $type = '', string $auditType = 'MESSAGE_QUEUE'): void
    {
        switch ($type){
            case 'error':
                $severity = 'WARNING';
                $typeMsg = 'Ошибка: ';
                break;
            case 'success':
                $severity = 'INFO';
                $typeMsg = 'Успешно: ';
                break;
            default:
                $severity = 'SECURITY';
                $typeMsg = '';
        }

        CEventLog::Add(array(
            'SEVERITY' => $severity,
            'AUDIT_TYPE_ID' => $auditType,
            'MODULE_ID' => 'ivdcode.telegrambot',
            'ITEM_ID' => 1,
            'SITE_ID' => 's1',
            'USER_ID' => 1,
            'DESCRIPTION' => $typeMsg . $message,
        ));
    }
}
