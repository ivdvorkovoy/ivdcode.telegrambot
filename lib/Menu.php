<?php
/**
 * @author Ivan Dvorkovoy
 * @copyright Copyright (c) 2023 Ivan Dvorkovoy
 * @license @link https://github.com/ivdvorkovoy/ivdcode.telegrambot/blob/master/LICENSE GPL-3.0 License
 */

namespace IVDCode\TelegramBot;

class Menu
{
    public static function addGlobalMenuItem(&$aGlobalMenu, &$aModuleMenu)
    {
        global $USER;

        if ($USER->isAuthorized() && $USER->isAdmin()) {
            $aGlobalMenu[] = [
                "menu_id" => "global_menu_ivdcode_telegram_bot",
                "page_icon" => "service_title_icon",
                "index_icon" => "service_page_icon",
                "text" => "IVDCode - TelegramBot",
                "title" => "IVDCode - TelegramBot",
                "sort" => "900",
                "items_id" => "global_menu_ivdcode_telegram_bot",
                "help_section" => "ivdcodetelegrambot",
                "items" => [
                    [
                        "parent_menu" => "global_menu_ivdcode_telegram_bot",
                        "icon" => "default_menu_icon",
                        "page_icon" => "default_page_icon",
                        "url" => "/bitrix/admin/ivdcode_telegrambot.php",
                        "sort" => "100",
                        "text" => "Настройки",
                        "title" => "Настройки",
                        "more_url" => [],
                    ]
                ]
            ];
        }
    }
}