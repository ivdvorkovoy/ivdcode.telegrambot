<?php

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use CAgent;
use Carbon\Carbon;

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

    }

    public function installFiles(): void
    {

    }

    public function installAgents(): void
    {

    }

    public function addHandlers(): void
    {

    }

    public function uninstallDB(): void
    {

    }

    public function uninstallFiles(): void
    {

    }

    public function uninstallAgents(): void
    {

    }

    public function deleteHandlers(): void
    {

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