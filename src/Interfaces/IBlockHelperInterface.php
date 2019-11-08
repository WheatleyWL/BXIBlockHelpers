<?php
namespace WheatleyWL\BXIBlockHelpers\Interfaces;

use WheatleyWL\BXIBlockHelpers\Exceptions\IBlockHelperException;

interface IBlockHelperInterface
{
    /**
     * @param string $code
     * @param null|string $type
     * @param string $siteId
     * @throws IBlockHelperException
     * @return int
     */
    public static function getIBlockIdByCode($code, $type = null, $siteId = null);

    /**
     * @param string $code
     * @param int $iblockId
     * @throws IBlockHelperException
     * @return int
     */
    public static function getPropertyIDByCode($code, $iblockId);

    /**
     * @param string $xmlId
     * @param string $code
     * @param int $iblockId
     * @throws IBlockHelperException
     * @return int
     */
    public static function getEnumIDByXMLID($xmlId, $code, $iblockId);

    /**
     * @param int $enumId
     * @param string $code
     * @param int $iblockId
     * @throws IBlockHelperException
     * @return string
     */
    public static function getXMLIDByEnumID($enumId, $code, $iblockId);

    /**
     * @param string $code
     * @param int $iblockId
     * @throws IBlockHelperException
     * @return int
     */
    public static function getSectionIdByCode($code, $iblockId);

    /**
     * @param $code
     * @param $iblockId
     * @throws IBlockHelperException
     * @return mixed
     */
    public static function getElementIdByCode($code, $iblockId);
}
