<?php
/**
 * @author Ivan Dvorkovoy
 * @copyright Copyright (c) 2023 Ivan Dvorkovoy
 * @license @link https://github.com/ivdvorkovoy/ivdcode.telegrambot/blob/master/LICENSE GPL-3.0 License
 */

namespace IVDCode\TelegramBot\Services;

use Bitrix\Main\Type\DateTime;
use IVDCode\TelegramBot\Tables\QueueMessageTable;
use IVDCode\TelegramBot\Tables\QueueTable;

/**
 * Class QueueService
 */
class QueueService
{

    /**
     * @param string $userIds
     * @param string $message
     * @param DateTime $dateSend
     * @return void
     */
    public function addToQueue(string $userIds, string $message = '', DateTime $dateSend): void
    {
        $arFields = array(
            'USERS' => $userIds,
            'MESSAGE' => $message,
            'DATE_SEND' => $dateSend
        );
        QueueTable::add($arFields);
    }

    /**
     * @return void
     */
    public static function processQueue(): void
    {
        $start = microtime(true);
        $arQueue = QueueTable::getList()->fetchAll();

        if (!$arQueue) {
            return;
        }
        $countMsg = 0;
        foreach ($arQueue as $queue) {
            $userIds = json_decode($queue['USERS'], true);
            foreach ($userIds as $userId) {
                $subscription = new SubscriptionService();
                if ($subscription->checkStatusByUser($userId)) {
                    $subscriber = $subscription->getFieldsByUserID($userId);
                    $arFields = array(
                        'CHAT_ID' => $subscriber['UF_TG_CHAT_ID'],
                        'MESSAGE' => $queue['MESSAGE'],
                        'DATE_SEND' => $queue['DATE_SEND']
                    );
                    QueueMessageTable::add($arFields);
                    $countMsg++;
                }
            }
            QueueTable::delete($queue['ID']);
        }
        if ($countMsg > 0) {
            $time = microtime(true) - $start;
            LogService::log('Очередь подготовлена к отправке. Подготовлено сообщений: ' . $countMsg . ' за ' . round($time, 4) . 'сек', 'success');
        }
    }

    /**
     * @return void
     */
    public static function getQueue(): void
    {
        $start = microtime(true);
        $nowDate = new DateTime();

        $getList = [
            'select' => ['*'],
            'filter' => ['<DATE_SEND' => $nowDate],
            'limit' => 30
        ];

        $countMsg = 0;
        while ($queues = QueueMessageTable::getList($getList)->fetchAll()) {
            foreach ($queues as $queue) {
                $getList['filter']['>ID'] = $queue['ID'];
                if (MessageService::sendMessage($queue['CHAT_ID'], $queue['MESSAGE'])) {
                    QueueMessageTable::delete($queue['ID']);
                    $countMsg++;
                }

            }
            sleep(1);
        }

        if ($countMsg > 0) {
            $time = microtime(true) - $start;
            LogService::log('Рассылка завершена. Отправлено сообщений: ' . $countMsg . ' за ' . round($time, 4) . 'сек', 'success');
        }
    }
}
