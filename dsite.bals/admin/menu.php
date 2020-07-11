<?
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if ($APPLICATION->GetGroupRight("dsite.bals") > "D") {
    $aMenu = array(
        "parent_menu" => "global_menu_store",
        "sort" => 100,
        "url" => "dsite_bals_transactions_list.php?lang=" . LANGUAGE_ID,
        "text" => Loc::getMessage('DSITE_BALS_MENU_TEXT'),
        "title" => Loc::getMessage('DSITE_BALS_MENU_TITLE'),
        "items_id" => "menu_dsite_bals_transactions_list",
    );
    return $aMenu;
}
return false;
?>