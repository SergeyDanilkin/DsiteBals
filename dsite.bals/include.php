<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UserTable;
use \Bitrix\Main\Type\DateTime;
use  \Dsite\Bals\Internals\TransactionsTable;
use \Dsite\Bals\Internals\UsersTable;

Loc::loadMessages(__FILE__);

/**
 * Class DsiteBals
 */
class DsiteBals
{
    /**
     * @param int $userId
     * @param int $bals
     */
    public static function addBals($userId = 0, $bals = 0)
    {
        global $DB;
        $DB->StartTransaction();
        try {
            if ($userId <= 0) {
                throw new \Exception(Loc::getMessage('DSITE_BALS_ERROR_EMPTY_USER'));
            }
            if ($bals == 0) {
                throw new \Exception(Loc::getMessage('DSITE_BALS_ERROR_EMPTY_BALS'));
            }
            if (!UserTable::getList(['filter' => ['ID' => $userId], 'select' => ['ID'], 'limit' => 1])->fetch()) {
                throw new \Exception(Loc::getMessage('DSITE_BALS_ERROR_EMPTY_USER'));
            }

            $rs = TransactionsTable::add(
                [
                    'USER_ID' => $userId,
                    'BALS' => abs($bals),
                    'TYPE' => ($bals > 0) ? 1 : 0,
                    'DATE' => new DateTime(date('Y-m-d H:i:s'), 'Y-m-d H:i:s'),
                    'CODE' => self::genCode(8)
                ]
            );

            if (!$rs->isSuccess()) {
                throw new \Exception(Loc::getMessage('DSITE_BALS_ERROR_DB_ADD'));
            }

            if ($uBals = UsersTable::getList( // check if user has bals
                [
                    'filter' => ['USER_ID' => $userId],
                    'limit' => 1,
                    'select' => ['ID', 'BALS']
                ]
            )->fetch()) {
                $rs = UsersTable::update(
                    $uBals['ID'],
                    [
                        'USER_ID' => $userId,
                        'BALS' => $uBals['BALS'] + $bals,
                        'DATE_UPDATE' => new DateTime(date('Y-m-d H:i:s'), 'Y-m-d H:i:s'),
                    ]
                );
                if (!$rs->isSuccess()) {
                    throw new \Exception(Loc::getMessage('DSITE_BALS_ERROR_DB_ADD'));
                }
            } else {
                $rs = UsersTable::add(
                    [
                        'USER_ID' => $userId,
                        'BALS' => $bals,
                        'DATE_UPDATE' => new DateTime(date('Y-m-d H:i:s'), 'Y-m-d H:i:s'),
                    ]
                );
                if (!$rs->isSuccess()) {
                    throw new \Exception(Loc::getMessage('DSITE_BALS_ERROR_DB_ADD'));
                }
            }
            $DB->Commit();

        } catch (\Exception $e) {
            $DB->Rollback();
            echo $e->getMessage();
        }
    }

    /**
     * @param int $length
     * @return string
     */
    private static function genCode($length = 8)
    {
        try {
            if ($length <= 0) {
                throw new \Exception(Loc::getMessage('DSITE_BALS_ERROR_CODE_LENGTH'));
            }
            $chars = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
            $keys = array_rand($chars, $length);
            $string = '';
            foreach ($keys as $k) {
                $string .= $chars[$k];
            }
            return $string;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>