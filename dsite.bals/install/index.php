<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Application;
use \Bitrix\Main\Loader;

$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang) - strlen("/install/index.php"));

Loc::loadMessages($strPath2Lang . '/install.php');

/**
 * Class dsite_bals
 */
class dsite_bals extends CModule
{
    public $MODULE_ID = 'dsite.bals';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_CSS;
    public $strError = '';

    /**
     * dsite_bals constructor.
     */
    public function __construct()
    {
        $arModuleVersion = array();
        include(__DIR__ . "/version.php");
        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        $this->MODULE_NAME = Loc::getMessage($this->MODULE_ID . "_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage($this->MODULE_ID . "_MODULE_DESC");
        $this->PARTNER_NAME = Loc::getMessage($this->MODULE_ID . "_PARTNER_NAME");
        $this->PARTNER_URI = Loc::getMessage($this->MODULE_ID . "_PARTNER_URI");
    }

    function DoInstall()
    {
        $this->InstallFiles();
        $this->InstallDB();
        ModuleManager::registerModule($this->MODULE_ID);
        $this->addTestData();
    }

    function InstallFiles()
    {
        CopyDirFiles(__DIR__ . '/admin', Application::getDocumentRoot() . '/bitrix/admin', true);
        return true;
    }

    function InstallDB()
    {
        global $DB;
        $DB->runSQLBatch(__DIR__ . '/db/' . strtolower($DB->type) . '/install.sql');
        return true;
    }

    function DoUninstall()
    {
        $this->UnInstallFiles();
        $this->UnInstallDB();
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    function UnInstallFiles()
    {
        DeleteDirFiles(__DIR__ . '/admin', Application::getDocumentRoot() . '/bitrix/admin', true);
        return true;
    }

    function UnInstallDB()
    {
        global $DB;
        $DB->runSQLBatch(__DIR__ . '/db/' . strtolower($DB->type) . '/uninstall.sql');
        return true;
    }

    function addTestData()
    {
        if (Loader::includeModule($this->MODULE_ID)) {
            $user = new \CUser;
            $rand = rand();
            $arFields = array(
                "NAME" => 'test_dsite_bals_' . $rand,
                "LOGIN" => 'test_dsite_bals_' . $rand,
                "EMAIL" => 'test_dsite_bals_' . $rand . '@test.ru',
                "LID" => "ru",
                "ACTIVE" => "Y",
                "PASSWORD" => 123456,
                "CONFIRM_PASSWORD" => 123456,
                "GROUP_ID" => array(2)
            );
            $userId = $user->Add($arFields);
            if ($userId > 0){
                DsiteBals::addBals($userId,100);
            }
        }
    }
}
