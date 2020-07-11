<?php

namespace Dsite\Bals\Internals;

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\ORM\Data\DataManager,
    Bitrix\Main\ORM\Fields\DatetimeField,
    Bitrix\Main\ORM\Fields\IntegerField,
    Bitrix\Main\ORM\Fields\StringField,
    Bitrix\Main\ORM\Fields\Validators\LengthValidator,
    Bitrix\Main\SystemException,
    Bitrix\Main\ArgumentTypeException,
    Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\UserTable;
use Bitrix\Main\ORM\Query\Join;

Loc::loadMessages(__FILE__);

/**
 * Class TransactionsTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> USER_ID int mandatory
 * <li> TYPE int mandatory
 * <li> BALS int mandatory
 * <li> DATE datetime optional
 * <li> CODE string(255) optional
 * </ul>
 **/
class TransactionsTable extends DataManager
{
    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName()
    {
        return 'dsite_bals_transactions';
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
                    'title' => Loc::getMessage('TRANSACTIONS_ENTITY_ID_FIELD')
                ]
            ),
            new IntegerField(
                'USER_ID',
                [
                    'required' => true,
                    'title' => Loc::getMessage('TRANSACTIONS_ENTITY_USER_ID_FIELD')
                ]
            ),
            new IntegerField(
                'TYPE',
                [
                    'required' => true,
                    'title' => Loc::getMessage('TRANSACTIONS_ENTITY_TYPE_FIELD')
                ]
            ),
            new IntegerField(
                'BALS',
                [
                    'required' => true,
                    'title' => Loc::getMessage('TRANSACTIONS_ENTITY_BALS_FIELD')
                ]
            ),
            new DatetimeField(
                'DATE',
                [
                    'title' => Loc::getMessage('TRANSACTIONS_ENTITY_DATE_FIELD')
                ]
            ),
            new StringField(
                'CODE',
                [
                    'validation' => [__CLASS__, 'validateCode'],
                    'title' => Loc::getMessage('TRANSACTIONS_ENTITY_CODE_FIELD')
                ]
            ),
            (new Reference(
                'USER',
                UserTable::class,
                Join::on('this.USER_ID', 'ref.ID')
            ))->configureJoinType('inner'),
        ];
    }


    /**
     * Returns validators for CODE field.
     *
     * @return LengthValidator[]
     * @throws ArgumentTypeException
     */
    public static function validateCode()
    {
        return [
            new LengthValidator(null, 255),
        ];
    }
}