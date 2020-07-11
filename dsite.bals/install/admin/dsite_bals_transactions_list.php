<?
if(file_exists($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/dsite.bals/admin/transactions_list.php"))
    include_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/dsite.bals/admin/transactions_list.php";
elseif(file_exists($_SERVER["DOCUMENT_ROOT"] . "/local/modules/dsite.bals/admin/transactions_list.php"))
    include_once $_SERVER["DOCUMENT_ROOT"] . "/local/modules/dsite.bals/admin/transactions_list.php";
?>