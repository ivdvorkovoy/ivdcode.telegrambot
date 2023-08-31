<?php
/**
 * @author Ivan Dvorkovoy
 * @copyright Copyright (c) 2023 Ivan Dvorkovoy
 * @license @link https://github.com/ivdvorkovoy/ivdcode.telegrambot/blob/master/LICENSE GPL-3.0 License
 */

namespace IVDCode\TelegramBot\Services;

use Bitrix\Main\ORM\Data\DataManager;

/**
 * Class SubscriptionService
 */
class SubscriptionService
{
    public ?string $stack = null;

    function __construct()
    {
        self::setBaseParameters();
    }

    /**
     * @return void
     */
    private function setBaseParameters(): void
    {
        $this->stack = HighloadService::getDataHL("subscribers");
    }

    /**
     * @param string $userId
     * @return string
     */
    public function hash(string $userId): string
    {
        return md5($userId);
    }

    /**
     * @param string $userId
     * @return void
     */
    public function setHash(string $userId): void
    {
        $hash = $this->hash($userId);
        if (!$this->checkHash($hash)) {
            $data = $this->stack;
            $data::add(["UF_USER_ID" => $userId, "UF_HASH" => $hash]);
        }
    }

    /**
     * @param string $userID
     * @return array
     */
    public function getFieldsByUserID(string $userID): array
    {
        $result = $this->stack::getList(['filter' => ["UF_USER_ID" => $userID]])->fetch();

        if (is_array($result)){
            return $result;
        }

        return [];
    }

    /**
     * @param string $userID
     * @return array
     */
    public function getByUserID(string $userID): array
    {
        $result = $this->stack::getList(['filter' => ["UF_USER_ID" => $userID]])->fetch();

        if (is_array($result)){
            return $result;
        }

        return [];
    }

    /**
     * @param string $hash
     * @return string
     */
    public function getIdByHash(string $hash): string
    {
        $result = $this->stack::getList(['filter' => ["UF_HASH" => $hash]])->fetch();

        if (is_array($result)){
            return $result["ID"];
        }

        return '';
    }

    /**
     * @param string $tgUserId
     * @return string|null
     */
    public function getIdByTgUserId(string $tgUserId): ?string
    {
        $result = $this->stack::getList(['filter' => ["UF_TG_USER_ID" => $tgUserId]])->fetch();

        if ($result) {
            return $result["ID"];
        }
        return null;
    }

    /**
     * @param string $id
     * @param string $tgUserId
     * @param string $tgChatId
     * @return mixed
     */
    public function activate(string $id, string $tgUserId, string $tgChatId)
    {
        $result = $this->stack::update($id, [
            "UF_TG_USER_ID" => $tgUserId,
            "UF_TG_CHAT_ID" => $tgChatId,
            "UF_STATUS" => "active"
        ]);

        return $result->isSuccess();
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function deactivate(string $id)
    {
        $result = $this->stack::update($id, [
            "UF_STATUS" => "inactive"
        ]);

        return $result->isSuccess();
    }

    /**
     * @param string $tgUserId
     * @return bool
     */
    public function checkByTgUserId(string $tgUserId): bool
    {
        if ($this->stack::getList(['filter' => ["UF_TG_USER_ID" => $tgUserId]])->fetch()){
            return true;
        }

        return false;
    }

    /**
     * @param string $hash
     * @return bool
     */
    public function checkHash(string $hash): bool
    {
        if ($this->stack::getList(['filter' => ["UF_HASH" => $hash]])->fetch()){
            return true;
        }

        return false;
    }

    /**
     * @param string $id
     * @return bool
     */
    public function checkStatus(string $id): bool
    {
        $result = $this->stack::getList(['select' => ['UF_STATUS'], 'filter' => ["ID" => $id]])->fetch();

        if (is_array($result)) {
            return $result['UF_STATUS'] == 'active';
        }

        return false;
    }

    /**
     * @param string $userId
     * @return bool
     */
    public function checkStatusByUser(string $userId): bool
    {
        $sub = $this->getByUserID($userId);

        return $sub['UF_STATUS'] == 'active';
    }
}