<?php
namespace WheatleyWL\BXIBlockHelpers\Interfaces;

use WheatleyWL\BXIBlockHelpers\Exceptions\HLHelperException;

interface HLHelperInterface
{
    /**
     * Returns highload block class by highload block name
     *
     * @param string $name
     * @throws HLHelperException
     * @return \Bitrix\Main\Entity\DataManager
     */
    public static function getClassByName($name);

    /**
     * Returns highload block class by table name
     *
     * @param string $name
     * @throws HLHelperException
     * @return \Bitrix\Main\Entity\DataManager
     */
    public static function getClassByTableName($table);
}
