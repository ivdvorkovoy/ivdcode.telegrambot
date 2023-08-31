<?php
/**
 * @author Ivan Dvorkovoy
 * @copyright Copyright (c) 2023 Ivan Dvorkovoy
 * @license @link https://github.com/ivdvorkovoy/ivdcode.telegrambot/blob/master/LICENSE GPL-3.0 License
 */

require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;
use IVDCode\TelegramBot\Services\ApiService;

if (!Loader::includeModule("ivdcode.telegrambot")) {
    http_response_code(404);
    die();
}

$response = json_decode(file_get_contents("php://input"));

if (!isset($response)) {
    http_response_code(404);
    die();
}

$apiService = new ApiService($response);
$apiService->processingCommand();

require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/epilog_after.php");