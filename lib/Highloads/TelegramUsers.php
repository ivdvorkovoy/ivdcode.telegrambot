<?php
/**
 * @author Ivan Dvorkovoy
 * @copyright Copyright (c) 2023 Ivan Dvorkovoy
 * @license @link https://github.com/ivdvorkovoy/ivdcode.telegrambot/blob/master/LICENSE GPL-3.0 License
 */

namespace IVDCode\TelegramBot\Highloads;

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use CUserTypeEntity;
use IVDCode\TelegramBot\Services\LogService;

class TelegramUsers
{
    private static string $code = 'TelegramUsers';
    private static string $tableName = 'telegram_users';

    /**
     * @return void
     */
    public static function create(): void
    {
        Loader::IncludeModule('highloadblock');

        if (static::getHlBlockId()) {
            return;
        }

        $arLangs = Array(
            'ru' => 'Telegram Users',
            'en' => 'Telegram Users'
        );

        $result = HL\HighloadBlockTable::add(array(
            'NAME' => static::$code,
            'TABLE_NAME' => static::$tableName,
        ));

        if (!$result->isSuccess()) {
            $errors = $result->getErrorMessages();
            LogService::log($errors, 'error', 'CREATE_HIGHLOADBLOCK');
        }

        $id = $result->getId();
        foreach($arLangs as $lang_key => $lang_val){
            HL\HighloadBlockLangTable::add(array(
                'ID' => $id,
                'LID' => $lang_key,
                'NAME' => $lang_val
            ));
        }
        $arFields = self::getFields('HLBLOCK_' . $id);

        foreach($arFields as $arField){
            $obUserField  = new CUserTypeEntity;
            $obUserField->Add($arField);
        }
    }

    /**
     * @return void
     */
    public static function delete(): void
    {
        if ($hlBlockID = static::getHlBlockId()) {
            HL\HighloadBlockTable::delete($hlBlockID);
        }
    }

    /**
     * @return string|null
     */
    private static function getHlBlockId(): ?string
    {
        $getList = [
            'select' => ['ID'],
            'filter' => ['=NAME' => static::$code]
        ];

        $hlBlock = HL\HighloadBlockTable::getList($getList)->fetch();

        if (is_array($hlBlock) && !empty($hlBlock)) {
            return $hlBlock['ID'];
        } else {
            return false;
        }
    }

    /**
     * @param $UFObject
     * @return array[]
     */
    private static function getFields($UFObject): array
    {
        return Array(
            'UF_USER_ID'=>Array(
                'ENTITY_ID' => $UFObject,
                'FIELD_NAME' => 'UF_USER_ID',
                'USER_TYPE_ID' => 'string',
                'MANDATORY' => '',
                "EDIT_FORM_LABEL" => Array('ru'=>'', 'en'=>''),
                "LIST_COLUMN_LABEL" => Array('ru'=>'', 'en'=>''),
                "LIST_FILTER_LABEL" => Array('ru'=>'', 'en'=>''),
                "ERROR_MESSAGE" => Array('ru'=>'', 'en'=>''),
                "HELP_MESSAGE" => Array('ru'=>'', 'en'=>''),
            ),
            'UF_TG_USER_ID'=>Array(
                'ENTITY_ID' => $UFObject,
                'FIELD_NAME' => 'UF_TG_USER_ID',
                'USER_TYPE_ID' => 'string',
                'MANDATORY' => '',
                "EDIT_FORM_LABEL" => Array('ru'=>'', 'en'=>''),
                "LIST_COLUMN_LABEL" => Array('ru'=>'', 'en'=>''),
                "LIST_FILTER_LABEL" => Array('ru'=>'', 'en'=>''),
                "ERROR_MESSAGE" => Array('ru'=>'', 'en'=>''),
                "HELP_MESSAGE" => Array('ru'=>'', 'en'=>''),
            ),
            'UF_TG_CHAT_ID'=>Array(
                'ENTITY_ID' => $UFObject,
                'FIELD_NAME' => 'UF_TG_CHAT_ID',
                'USER_TYPE_ID' => 'string',
                'MANDATORY' => '',
                "EDIT_FORM_LABEL" => Array('ru'=>'', 'en'=>''),
                "LIST_COLUMN_LABEL" => Array('ru'=>'', 'en'=>''),
                "LIST_FILTER_LABEL" => Array('ru'=>'', 'en'=>''),
                "ERROR_MESSAGE" => Array('ru'=>'', 'en'=>''),
                "HELP_MESSAGE" => Array('ru'=>'', 'en'=>''),
            ),
            'UF_HASH'=>Array(
                'ENTITY_ID' => $UFObject,
                'FIELD_NAME' => 'UF_HASH',
                'USER_TYPE_ID' => 'string',
                'MANDATORY' => '',
                "EDIT_FORM_LABEL" => Array('ru'=>'', 'en'=>''),
                "LIST_COLUMN_LABEL" => Array('ru'=>'', 'en'=>''),
                "LIST_FILTER_LABEL" => Array('ru'=>'', 'en'=>''),
                "ERROR_MESSAGE" => Array('ru'=>'', 'en'=>''),
                "HELP_MESSAGE" => Array('ru'=>'', 'en'=>''),
            ),
            'UF_STATUS'=>Array(
                'ENTITY_ID' => $UFObject,
                'FIELD_NAME' => 'UF_STATUS',
                'USER_TYPE_ID' => 'string',
                'MANDATORY' => '',
                "EDIT_FORM_LABEL" => Array('ru'=>'', 'en'=>''),
                "LIST_COLUMN_LABEL" => Array('ru'=>'', 'en'=>''),
                "LIST_FILTER_LABEL" => Array('ru'=>'', 'en'=>''),
                "ERROR_MESSAGE" => Array('ru'=>'', 'en'=>''),
                "HELP_MESSAGE" => Array('ru'=>'', 'en'=>''),
            ),
        );
    }
}
