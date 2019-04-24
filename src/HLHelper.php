<?php
namespace WheatleyWL\BXIBlockHelpers;

use \Bitrix\Main\Loader;
use \Bitrix\Highloadblock\HighloadBlockTable;
use WheatleyWL\BXIBlockHelpers\Interfaces\HLHelperInterface;
use WheatleyWL\BXIBlockHelpers\Exceptions\HLHelperException;

class HLHelper implements HLHelperInterface
{
    protected static function checkModule()
    {
        if(!Loader::includeModule('highloadblock')) {
            throw new HLHelperException('Could not include hlblock module');
        }
    }

    /**
     * @inheritDoc
     */
    public static function getClassByName($name)
    {
        self::checkModule();

        $hl = HighloadBlockTable::getList([
            'filter' => [
                '=NAME' => $name
            ]
        ])->fetch();

        if(empty($hl)) {
            throw new HLHelperException("No highload block with '{$name}' name found");
        }
        
        $class = HighloadBlockTable::compileEntity($hl)->getDataClass();
        if(empty($class)) {
            throw new HLHelperException("Failed to compile highload block '{$name}'");
        }
        
        return $class;
    }

    /**
     * @inheritDoc
     */
    public static function getClassByTableName($table)
    {
        self::checkModule();

        $hl = HighloadBlockTable::getList([
            'filter' => [
                '=TABLE_NAME' => $table
            ]
        ])->fetch();

        if(empty($hl)) {
            throw new HLHelperException("No highload block with '{$table}' table name found");
        }

        $class = HighloadBlockTable::compileEntity($hl)->getDataClass();
        if(empty($class)) {
            throw new HLHelperException("Failed to compile highload block '{$table}'");
        }

        return $class;
    }
}
