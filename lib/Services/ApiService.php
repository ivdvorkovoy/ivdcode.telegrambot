<?php
/**
 * @author Ivan Dvorkovoy
 * @copyright Copyright (c) 2023 Ivan Dvorkovoy
 * @license @link https://github.com/ivdvorkovoy/ivdcode.telegrambot/blob/master/LICENSE GPL-3.0 License
 */

namespace IVDCode\TelegramBot\Services;

use Bitrix\Main\Diag\Debug;
use IVDCode\TelegramBot\Settings\Setting;

/**
 * Class ApiService
 */
class ApiService
{
    private $response;
    private string $tgUserId;
    private string $tgFirstName;
    private string $tgChatId;
    private string $tgMessage;

    public function __construct(string $response)
    {
        $this->response = json_decode($response);
        $this->tgUserId = $this->response->message->from->id;
        $this->tgFirstName = $this->response->message->from->first_name;
        $this->tgChatId = $this->response->message->chat->id;
        $this->tgMessage = $this->response->message->text;
    }

    /**
     * @param string $command
     * @return bool
     */
    public function checkCommand(string $command): bool
    {
        return strpos($this->tgMessage, $command);
    }

    /**
     * @return void
     */
    public function processingCommand(): void
    {
        $request = new RequestService();
        $message = 'К сожалению эта команда не может быть обработана.';

        if ($this->checkCommand('start')) {
            try {
                $message = $this->getStartMessage();
            } catch (\Exception $e) {
                \Bitrix\Main\Diag\Debug::writeToFile('Возникла ошибка: ' . $e->getMessage(), __METHOD__, '__dump_telegram_api_income.txt');
            }
        }

        if ($this->checkCommand('stop')) {
            try {
                $message = $this->getStopMessage();
            } catch (\Exception $e) {
                \Bitrix\Main\Diag\Debug::writeToFile('Возникла ошибка: ' . $e->getMessage(), __METHOD__, '__dump_telegram_api_income.txt');
            }
        }

        $result = $request->send_request(Setting::getMethod('sendMessage'), [
            'chat_id' => $this->tgChatId,
            'text' => $message,
            'parse_mode' => 'HTML',
        ]);
    }

    /**
     * @return string
     */
    public function getStartMessage(): string
    {
        $checkHash = false;
        $subId = '';

        $subscription = new SubscriptionService();
        $checkTgUser = $subscription->checkByTgUserId($this->tgUserId);


        if (strlen($this->tgMessage) <= 6 && !$checkTgUser) {
            return "Здравствуйте, <strong>{$this->tgFirstName}</strong>! Используйте пригласительную ссылку, чтобы подписаться на рассылку!";
        }

        if (strlen($this->tgMessage) <= 6 && $checkTgUser) {
            $subId = $subscription->getIdByTgUserId($this->tgUserId);
        }

        if (strlen($this->tgMessage) > 6) {
            $command = "/\/start /";
            $hash = preg_replace($command, '', $this->tgMessage);
            $checkHash = $subscription->checkHash($hash);
            $subId = $subscription->getIdByHash($hash);
        }

        if (!$checkTgUser && !$checkHash) {
            return "Здравствуйте, <strong>{$this->tgFirstName}</strong>! К сожалению пригласительная ссылка недействительна!";
        }

        $isSubActive = $subscription->checkStatus($subId);

        if ((!$checkTgUser && $checkHash) || ($checkTgUser && !$isSubActive)) {
            $subscription->activate($subId, $this->tgUserId, $this->tgChatId);
            return "Здравствуйте, <strong>{$this->tgFirstName}</strong>! Вы успешно подписались на рассылку!";
        }

        if ($checkTgUser) {
            return "Здравствуйте, <strong>{$this->tgFirstName}</strong>! Вы уже подписаны на рассылку!";
        }

        return 'К сожалению эта команда не может быть обработана.';
    }

    /**
     * @return string
     */
    public function getStopMessage(): string
    {
        $subscription = new SubscriptionService();
        $checkTgUser = $subscription->checkByTgUserId($this->tgUserId);
        $subId = $subscription->getIdByTgUserId($this->tgUserId);
        $isSubActive = false;

        if ($subId !== null) {
            $isSubActive = $subscription->checkStatus($subId);
        }

        if (!$checkTgUser) {
            return "Здравствуйте, <strong>{$this->tgFirstName}</strong>! Невозможно отписаться, так как Вы не ещё были подписаны на рассылку!";
        }

        if ($isSubActive) {
            $subscription->deactivate($subId);
            return "Здравствуйте, <strong>{$this->tgFirstName}</strong>! Вы успешно отписались от рассылки!";
        }

        return "Здравствуйте, <strong>{$this->tgFirstName}</strong>! Вы уже отписались от рассылки!";
    }
}