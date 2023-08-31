<?php
/**
 * @author Ivan Dvorkovoy
 * @copyright Copyright (c) 2023 Ivan Dvorkovoy
 * @license @link https://github.com/ivdvorkovoy/ivdcode.telegrambot/blob/master/LICENSE GPL-3.0 License
 */

namespace IVDCode\TelegramBot\Services;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;
use Bitrix\Main\ORM\Data\DataManager;
use IVDCode\TelegramBot\Settings\Setting;

/**
 * Class HighloadService
 */
class HighloadService
{
    public static array $hl = array();

    /**
     * @param $code
     * @return string
     */
    public static function getDataHL($code): string
    {
        Loader::includeModule("highloadblock");

        if (isset(self::$hl[$code])) return self::$hl[$code];

        self::$hl[$code] = self::getDataClassByTable($code);

        return self::$hl[$code];
    }

    /**
     * @param $code
     * @return string|null
     */
    public static function getDataClassByTable($code): ?string
    {
        $tableArray = HighloadBlockTable::getList(array('filter' => array('NAME' => Setting::getCodeHL($code))))->Fetch();

        if ($tableArray === false) return null;

        return HighloadBlockTable::compileEntity($tableArray)->getDataClass();
    }

}