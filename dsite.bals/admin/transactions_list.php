<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Entity\Query;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Dsite\Bals\Internals\TransactionsTable;

Loader::includeModule('dsite.bals');

Loc::loadMessages(__FILE__);

$POST_RIGHT = $APPLICATION->GetGroupRight('dsite.bals');
if ($POST_RIGHT == "D")
    $APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));

$entity_id = 'dsite_bals_transactions_list';
$sTableID = "dsite_bals_transactions";

$oSort = new CAdminUiSorting($sTableID, "ID", "desc");
$lAdmin = new CAdminUiList($sTableID, $oSort);

$filterFields = array(
    array(
        "id" => "ID",
        "name" => Loc::getMessage('TRANSACTIONS_LIST_ID_FIELD'),
        "filterable" => "",
        "default" => true
    ),
    array(
        "id" => "USER.EMAIL",
        "name" => Loc::getMessage("TRANSACTIONS_LIST_EMAIL_FIELD"),
        "filterable" => "%",
        "default" => true
    ),
    array(
        "id" => "TYPE",
        "name" => Loc::getMessage("TRANSACTIONS_LIST_TYPE_FIELD"),
        "type" => "list",
        "items" => array(
            "1" => Loc::getMessage("TRANSACTIONS_LIST_TYPE_FIELD_PLUS"),
            "0" => Loc::getMessage("TRANSACTIONS_LIST_TYPE_FIELD_MINUS")
        ),
        "filterable" => "",
        "default" => true
    ),
    array(
        "id" => "BALS",
        "name" => Loc::getMessage('TRANSACTIONS_LIST_BALS_FIELD'),
        "filterable" => "",
        "default" => true
    ),
    array(
        "id" => "DATE",
        "name" => Loc::getMessage("TRANSACTIONS_LIST_DATE_FIELD"),
        "type" => "date",
        "default" => true
    ),

    array(
        "id" => "CODE",
        "name" => Loc::getMessage("TRANSACTIONS_LIST_CODE_FIELD"),
        "filterable" => "%",
        "default" => true
    ),
);
$USER_FIELD_MANAGER->AdminListAddFilterFieldsV2($entity_id, $filterFields);
$arFilter = array();
$lAdmin->AddFilter($filterFields, $arFilter);

$USER_FIELD_MANAGER->AdminListAddFilterV2($entity_id, $arFilter, $sTableID, $filterFields);

setHeaderColumn($lAdmin);

$nav = $lAdmin->getPageNavigation("pages-dsite_bals_transactions_list");

$userQuery = getUserQuery($lAdmin, $arFilter, $filterFields, $excelMode, $sTableID, $nav);

$result = $userQuery->exec();

$totalCountRequest = $lAdmin->isTotalCountRequest();
if ($totalCountRequest) {
    $lAdmin->sendTotalCountResponse($result->getCount());
}

$n = 0;
$pageSize = $lAdmin->getNavSize();
while ($data = $result->fetch()) {
    $n++;
    if ($n > $pageSize && !$excelMode) {
        break;
    }

    if ($data['TYPE'] == 1) {
        $data['TYPE'] = Loc::getMessage('TRANSACTIONS_LIST_TYPE_FIELD_PLUS');
    } elseif ($data['TYPE'] == 0) {
        $data['TYPE'] = Loc::getMessage('TRANSACTIONS_LIST_TYPE_FIELD_MINUS');
    }

    if ($data['DSITE_BALS_INTERNALS_TRANSACTIONS_USER_EMAIL']) {
        $data['USER.EMAIL'] = $data['DSITE_BALS_INTERNALS_TRANSACTIONS_USER_EMAIL'];
    }

    $row =& $lAdmin->addRow($data['ID'], $data);

    $arActions = array();
    $row->addActions($arActions);
}

$nav->setRecordCount($nav->getOffset() + $n);
$lAdmin->setNavigation($nav, Loc::getMessage("MAIN_USER_ADMIN_PAGES"), false);

$aContext = array();
$lAdmin->AddAdminContextMenu($aContext);

$lAdmin->CheckListMode();

$APPLICATION->SetTitle(Loc::getMessage("TITLE"));

require($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/include/prolog_admin_after.php");

$lAdmin->DisplayFilter($filterFields);
$lAdmin->DisplayList(["SHOW_COUNT_HTML" => true]);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");

function setHeaderColumn(CAdminUiList $lAdmin)
{
    $arHeaders = array(
        array("id" => "ID", "content" => Loc::getMessage('TRANSACTIONS_LIST_ID_FIELD'), "sort" => "id", "default" => true),
        array("id" => "USER.EMAIL", "content" => Loc::getMessage('TRANSACTIONS_LIST_EMAIL_FIELD'), "sort" => "email", "default" => true),
        array("id" => "TYPE", "content" => Loc::getMessage('TRANSACTIONS_LIST_TYPE_FIELD'), "sort" => "type", "default" => true),
        array("id" => "BALS", "content" => Loc::getMessage('TRANSACTIONS_LIST_BALS_FIELD'), "sort" => "bals", "default" => true),
        array("id" => "DATE", "content" => Loc::getMessage('TRANSACTIONS_LIST_DATE_FIELD'), "sort" => "date", "default" => true),
        array("id" => "CODE", "content" => Loc::getMessage('TRANSACTIONS_LIST_CODE_FIELD'), "sort" => "code", "default" => true),
    );

    $lAdmin->addHeaders($arHeaders);
}

/**
 * @param CAdminUiList $lAdmin
 * @param $arFilter
 * @param $filterFields
 * @param $excelMode
 * @param $tableId
 * @param null $nav
 * @return Query
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\SystemException
 */
function getUserQuery(CAdminUiList $lAdmin, $arFilter, $filterFields, $excelMode, $tableId, $nav = null)
{
    global $by, $order;

    $totalCountRequest = $lAdmin->isTotalCountRequest();

    $userQuery = new Query(TransactionsTable::getEntity());
    $listSelectFields = ($totalCountRequest ? [] : $lAdmin->getVisibleHeaderColumns());
    if (!in_array("ID", $listSelectFields))
        $listSelectFields[] = "ID";

    $listRatingColumn = preg_grep('/^RATING_(\d+)$/i', $listSelectFields);
    if (!empty($listRatingColumn))
        $listSelectFields = array_diff($listSelectFields, $listRatingColumn);

    $userQuery->setSelect($listSelectFields);
    $sortBy = strtoupper($by);
    if (!TransactionsTable::getEntity()->hasField($sortBy)) {
        $sortBy = "ID";
    }
    $sortOrder = strtoupper($order);
    if ($sortOrder <> "DESC" && $sortOrder <> "ASC") {
        $sortOrder = "DESC";
    }
    $userQuery->setOrder(array($sortBy => $sortOrder));
    if ($totalCountRequest) {
        $userQuery->countTotal(true);
    }

    if ($nav instanceof Bitrix\Main\UI\PageNavigation) {
        $userQuery->setOffset($nav->getOffset());
        if (!$excelMode)
            $userQuery->setLimit($nav->getLimit() + 1);
    }

    foreach ($arFilter as $filterKey => $filterValue) {
        $userQuery->addFilter($filterKey, $filterValue);
    }

    return $userQuery;
}
?>