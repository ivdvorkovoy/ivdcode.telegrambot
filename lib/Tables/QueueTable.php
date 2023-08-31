<?php
/**
 * @author Ivan Dvorkovoy
 * @copyright Copyright (c) 2023 Ivan Dvorkovoy
 * @license @link https://github.com/ivdvorkovoy/ivdcode.telegrambot/blob/master/LICENSE GPL-3.0 License
 */

namespace IVDCode\TelegramBot\Tables;

use Bitrix\Main\Application;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\DataManager;

Loc::loadMessages(__FILE__);

/**
 * Class QueueTable
 */

class QueueTable extends DataManager
{
    public const ID = 'ID';
    public const USERS = 'USERS';
    public const MESSAGE = 'MESSAGE';
    public const DATE_SEND = 'DATE_SEND';
    public static function createTable(): void
    {
        $sql = 'CREATE TABLE IF NOT EXISTS ' . static::getTableName() . ' (' .
            self::ID . ' int(11) unsigned NOT NULL AUTO_INCREMENT, ' .
            self::USERS . ' TEXT NULL, ' .
            self::MESSAGE . ' TEXT, ' .
            self::DATE_SEND . ' datetime NULL,' .
            ' PRIMARY KEY (' . self::ID . ')); ';

        $connection = Application::getConnection();
        $connection->query($sql);
    }

    public static function getTableName(): string
    {
        return 'ivdcode_tbot_queue';
    }

    public static function deleteTable(): void
    {
        $sql = 'DROP TABLE IF EXISTS ' . self::getTableName();

        $connection = Application::getConnection();
        $connection->query($sql);
    }

    public static function truncateTable(): void
    {
        $sql = 'TRUNCATE TABLE ' . self::getTableName();

        $connection = Application::getConnection();
        $connection->query($sql);
    }

    public static function getMap(): array
    {
        return [
            new Entity\IntegerField(
                self::ID, [
                    'primary' => true,
                    'autocomplete' => true,
                    'title' => Loc::getMessage('ENTITY_' . self::ID . '_FIELD')
                ]
            ),
            new Entity\TextField(
                self::USERS, [
                    'required' => false,
                    'title' => Loc::getMessage('ENTITY_' . self::USERS . '_FIELD')
                ]
            ),
            new Entity\StringField(
                self::MESSAGE, [
                    'required' => false,
                    'title' => Loc::getMessage('ENTITY_' . self::MESSAGE . '_FIELD')
                ]
            ),
            new Entity\DatetimeField(
                self::DATE_SEND, [
                    'required' => false,
                    'title' => Loc::getMessage('ENTITY_' . self::DATE_SEND . '_FIELD'),
                ]
            ),
        ];
    }
}
