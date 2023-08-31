<?php
/**
 * @author Ivan Dvorkovoy
 * @copyright Copyright (c) 2023 Ivan Dvorkovoy
 * @license @link https://github.com/ivdvorkovoy/ivdcode.telegrambot/blob/master/LICENSE GPL-3.0 License
 */

use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use IVDCode\TelegramBot\Services\WebhookService;
use IVDCode\TelegramBot\Settings\Setting;

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php";

Loader::includeModule("ivdcode.telegrambot");

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrx/modules/main/include/prolog_admin_after.php";

global $USER;

if ($USER->isAuthorized() && $USER->isAdmin()) {
    $webhookStatus = WebhookService::getWebHookStatus();

    $request = Context::getCurrent()->getRequest();

    if ($request->isPost()) {
        $result = [];
        $result["bot_url"] = htmlentities($request["bot_url"], ENT_QUOTES, "UTF-8");
        $result["bot_token"] = htmlentities($request["bot_token"], ENT_QUOTES, "UTF-8");

        if (Setting::getBotUrl() !== $result["bot_url"]) Setting::setBotUrl($result["bot_url"]);
        if (Setting::getApiToken() !== $result["bot_token"]) Setting::setApiToken($result["bot_token"]);

        WebhookService::setWebhook();

        LocalRedirect("ivdcode_telegrambot.php");
    }
    ?>
    <form method="post" name="telegrambot">
        <div style="height: 20px"></div>
        Ссылка на Телеграм-бота: <input name="bot_url" type="text" value="<?= Setting::getBotUrl() ?>">
        <div style="height: 20px"></div>
        Токен для Телеграм-бота: <input name="api_token" type="text" value="<?= Setting::getApiToken() ?>">
        <div style="height: 20px"></div>
        Статус: <span style="color:<?= $webhookStatus['color'] ?>"><?= $webhookStatus['description'] ?></span>
        <div style="height: 20px"></div>
        <input type="submit" name="sub" value="Сохранить"/>
    </form>
    <?
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php';