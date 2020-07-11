<?php
namespace Dsite\Bals\Internals;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\ORM\Data\DataManager,
    Bitrix\Main\ORM\Fields\DatetimeField,
    Bitrix\Main\ORM\Fields\IntegerField,
    Bitrix\Main\ORM\Fields\StringField,
    Bitrix\Main\ORM\Fields\Validators\LengthValidator,
    \Bitrix\Main\SystemException,
    \Bitrix\Main\ArgumentTypeException;

Loc::loadMessages(__FILE__);
/**
 * Class UsersTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> USER_ID int mandatory
 * <li> BALS int mandatory
 * <li> DATE_UPDATE datetime optional
 * </ul>
 *
 * @package Bitrix\Bals
 **/

class UsersTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'dsite_bals_users';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     * @throws SystemException
     */
    public static function getMap()
    {
        return [
            new IntegerField(
                'ID',
                [
                    'primary' => true,
                    'autocomplete' => true,
                    'title' => Loc::getMessage('USERS_ENTITY_ID_FIELD')
                ]
            ),
            new IntegerField(
                'USER_ID',
                [
                    'required' => true,
                    'title' => Loc::getMessage('USERS_ENTITY_USER_ID_FIELD')
                ]
            ),
            new IntegerField(
                'BALS',
                [
                    'required' => true,
                    'title' => Loc::getMessage('USERS_ENTITY_BALS_FIELD')
                ]
            ),
            new DatetimeField(
                'DATE_UPDATE',
                [
                    'title' => Loc::getMessage('USERS_ENTITY_DATE_UPDATE_FIELD')
                ]
            ),
        ];
    }
}