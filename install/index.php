<?php
/**
 * @author Ivan Dvorkovoy
 * @copyright Copyright (c) 2023 Ivan Dvorkovoy
 * @license @link https://github.com/ivdvorkovoy/ivdcode.telegrambot/blob/master/LICENSE GPL-3.0 License
 */

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use CAgent;
use Carbon\Carbon;
use IVDCode\TelegramBot\Agents\QueueAgent;
use IVDCode\TelegramBot\Highloads\TelegramUsers;
use IVDCode\TelegramBot\Services\WebhookService;
use IVDCode\TelegramBot\Tables\QueueMessageTable;
use IVDCode\TelegramBot\Tables\QueueTable;

Loc::loadMessages(__FILE__);

class ivdcode_telegrambot extends CModule
{
    var string $MODULE_ID = "ivdcode.telegrambot";
    var string $MODULE_VERSION;
    var string $MODULE_VERSION_DATE;
    var string $MODULE_NAME;
    var string $MODULE_DESCRIPTION;
    var string $MODULE_GROUP_RIGHTS;
    var string $PARTNER_NAME;
    var string $PARTNER_URI;

    public function __construct()
    {
        $arModuleVersion = [];

        include_once __DIR__ . "/version.php";

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_NAME = Loc::getMessage('IVDCODE_TELEGRAM_BOT_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('IVDCODE_TELEGRAM_BOT_MODULE_DESCRIPTION');
        $this->MODULE_GROUP_RIGHTS = 'N';
        $this->PARTNER_NAME = Loc::getMessage('IVDCODE_TELEGRAM_BOT_MODULE_PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('IVDCODE_TELEGRAM_BOT_MODULE_PARTNER_URI');
    }

    public function doInstall(): void
    {
        ModuleManager::registerModule($this->MODULE_ID);
        Loader::includeModule($this->MODULE_ID);

        $this->installDB();
        $this->installHL();
        $this->installFiles();
        $this->installAgents();
        $this->addHandlers();
    }

    public function doUninstall(): void
    {
        global $APPLICATION;
        Loader::includeModule($this->MODULE_ID);

        $request = Application::getInstance()->getContext()->getRequest();
        switch ($request['step']) {
            case null:
            case 1:
                $APPLICATION->IncludeAdminFile(Loc::getMessage('IVDCODE_TELEGRAM_BOT_MODULE_UNINSTALL_TITLE'), $this->getPath() . '/install/unstep1.php');
                break;
            case 2:
                if ($request['savedata'] != 'Y') {
                    $this->uninstallHL();
                    $this->uninstallDB();
                }

                WebhookService::deleteWebhook();
                $this->deleteHandlers();
                $this->uninstallFiles();
                $this->uninstallAgents();

                Option::delete($this->MODULE_ID);
                ModuleManager::unRegisterModule($this->MODULE_ID);

                $APPLICATION->IncludeAdminFile(Loc::getMessage('IVDCODE_TELEGRAM_BOT_MODULE_UNINSTALL_TITLE'), $this->getPath() . '/install/unstep2.php');
                break;
        }
    }

    public function installDB(): void
    {
        QueueTable::createTable();
        QueueMessageTable::createTable();
    }

    public function installHL(): void
    {
        TelegramUsers::create();
    }

    public function installFiles(): bool
    {
        copyDirFiles(
            $_SERVER['DOCUMENT_ROOT'] . '/local/modules/' . $this->MODULE_ID . '/install/admin',
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin',
            true, true
        );
        copyDirFiles(
            $_SERVER['DOCUMENT_ROOT'] . '/local/modules/' . $this->MODULE_ID . '/install/user_api',
            $_SERVER['DOCUMENT_ROOT'] . '/',
            true, true
        );
        return true;
    }

    public function installAgents(): void
    {
        CAgent::AddAgent(
            '\\' . QueueAgent::class . '::processMessageQueue();',
            $this->MODULE_ID,
            "N",
            600,
            date("d.m.Y H:i:s", strtotime(" +1 minutes")),
            "Y",
            date("d.m.Y H:i:s", strtotime(" +1 minutes")),
            100
        );

        CAgent::AddAgent(
            '\\' . QueueAgent::class . '::getMessageQueue();',
            $this->MODULE_ID,
            "N",
            600,
            date("d.m.Y H:i:s", strtotime(" +5 minutes")),
            "Y",
            date("d.m.Y H:i:s", strtotime(" +5 minutes")),
            100
        );
    }

    public function addHandlers(): void
    {
        $eventManager = EventManager::getInstance();
        $eventManager->registerEventHandler('main', 'OnBuildGlobalMenu', $this->MODULE_ID, '\IVDCode\TelegramBot\Menu', 'addGlobalMenuItem');
    }

    public function uninstallDB(): void
    {
        QueueTable::deleteTable();
        QueueMessageTable::deleteTable();
    }

    public function uninstallHL(): void
    {
        TelegramUsers::delete();
    }

    public function uninstallFiles(): bool
    {
        deleteDirFiles(
            $_SERVER['DOCUMENT_ROOT'] . '/local/modules/' . $this->MODULE_ID . '/install/admin',
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin'
        );
        deleteDirFiles(
            $_SERVER['DOCUMENT_ROOT'] . '/local/modules/' . $this->MODULE_ID . '/install/user_api',
            $_SERVER['DOCUMENT_ROOT'] . '/'
        );
        return true;
    }

    public function uninstallAgents(): bool
    {
        CAgent::RemoveModuleAgents($this->MODULE_ID);
        return true;
    }

    public function deleteHandlers(): void
    {$eventManager = EventManager::getInstance();
        $eventManager->unRegisterEventHandler('main', 'OnBuildGlobalMenu', $this->MODULE_ID, '\IVDCode\TelegramBot\Menu', 'addGlobalMenuItem');

    }

    public function getPath(bool $withoutDocumentRoot = false): string
    {
        if ($withoutDocumentRoot) {
            return str_ireplace($this->documentRoot, '', dirname(__DIR__));
        } else {
            return dirname(__DIR__);
        }
    }
}