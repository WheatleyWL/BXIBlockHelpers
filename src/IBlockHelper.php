<?php
namespace WheatleyWL\BXIBlockHelpers;

use \Bitrix\Main\Loader;
use \Bitrix\Main\Entity;
use \Bitrix\Iblock;
use WheatleyWL\BXIBlockHelpers\Interfaces\IBlockHelperInterface;
use WheatleyWL\BXIBlockHelpers\Exceptions\IBlockHelperException;

class IBlockHelper implements IBlockHelperInterface
{
    const CACHE_IBLOCKS     = 'IBLOCKS';
    const CACHE_PROPERTIES  = 'PROPERTIES';
    const CACHE_XMLIDS      = 'XMLIDS';
    const CACHE_SECTIONS    = 'SECTIONS';

    protected static $CACHED = [
        'IBLOCKS' => [],
        'PROPERTIES' => [],
        'XMLIDS' => [],
        'SECTIONS' => []
    ];

    /**
     * Loads iblock module
     *
     * @throws IBlockHelperException
     * @throws \Bitrix\Main\LoaderException
     */
    protected static function checkModule()
    {
        if(!Loader::includeModule('iblock')) {
            throw new IBlockHelperException('Could not include iblock module');
        }
    }

    /**
     * Caches all valid iblocks for further usage
     * Iblocks considered valid if they have mnemonic code set
     *
     * @throws IBlockHelperException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     */
    protected static function checkIblocks()
    {
        self::checkModule();

        if(empty(self::$CACHED[self::CACHE_IBLOCKS])) {
            $res = Iblock\IblockTable::getList([
                'select' => [
                    'ID',
                    'CODE',
                    'IBLOCK_TYPE_ID',
                    'SITE_' => 'SITE'
                ],
                'filter' => [
                    '!CODE' => false
                ],
                'runtime' => [
                    new Entity\ReferenceField(
                        'SITE',
                        \Bitrix\Iblock\IblockSiteTable::class,
                        [
                            'this.ID' => 'ref.IBLOCK_ID'
                        ]
                    )
                ]
            ]);

            while ($iblock = $res->fetch()) {
                $sid = $iblock['SITE_SITE_ID'];

                self::$CACHED[self::CACHE_IBLOCKS][$iblock['IBLOCK_TYPE_ID']][$iblock['CODE']][$sid] = $iblock['ID'];
            }
        }
    }

    /**
     * @inheritDoc
     */
    public static function getIBlockIdByCode($code, $type = null, $siteId = null)
    {
        self::checkIblocks();

        // if no site provided - use current one
        // for admin section this should be provided explicitly since SITE_ID = LANGUAGE_ID
        if($siteId === null) {
            $siteId = SITE_ID;
        }

        if (!empty($code) && !empty($type)) {
            if (self::$CACHED[self::CACHE_IBLOCKS][$type][$code][$siteId]) {
                return self::$CACHED[self::CACHE_IBLOCKS][$type][$code][$siteId];
            }
        } elseif (!empty($code)) {
            foreach (self::$CACHED[self::CACHE_IBLOCKS] as $iblocks) {
                if ($iblocks[$code][$siteId]) {
                    return $iblocks[$code][$siteId];
                }
            }
        }

        throw new IBlockHelperException('No iblock found with given params');
    }

    /**
     * @inheritDoc
     */
    public static function getPropertyIdByCode($code, $iblockId)
    {
        if(empty($code)) {
            throw new IBlockHelperException("Property code must not be empty");
        }

        if(empty($iblockId)) {
            throw new IBlockHelperException("IblockId must not be empty");
        }

        if(empty(self::$CACHED[self::CACHE_PROPERTIES][$iblockId][$code])) {
            $res = \Bitrix\Iblock\PropertyTable::getList([
                'select' => [
                    'ID',
                    'CODE',
                    'IBLOCK_ID'
                ],
                'filter' => [
                    'CODE' => $code,
                    'IBLOCK_ID' => $iblockId
                ]
            ]);

            $prop = $res->fetch();
            if (empty($prop)) {
                throw new IBlockHelperException("No property found with code '{$code}' at iblock '{$iblockId}'");
            }

            self::$CACHED[self::CACHE_PROPERTIES][$prop['IBLOCK_ID']][$prop['CODE']] = $prop['ID'];
        }

        if(empty(self::$CACHED[self::CACHE_PROPERTIES][$iblockId][$code])) {
            throw new IBlockHelperException("No property found with code '{$code}' at iblock '{$iblockId}'");
        }
        
        return self::$CACHED[self::CACHE_PROPERTIES][$iblockId][$code];
    }

    /**
     * @param $code
     * @param $iblockId
     * @throws IBlockHelperException
     * @throws \Bitrix\Main\ArgumentException
     */
    protected static function checkPropertyEnums($code, $iblockId)
    {
        if(empty(self::$CACHED[self::CACHE_XMLIDS][$iblockId][$code]))
        {
            $propertyId = self::getPropertyIdByCode($code, $iblockId);

            $res = \Bitrix\Iblock\PropertyEnumerationTable::getList([
                'select' => [
                    'ID',
                    'XML_ID'
                ],
                'filter' => [
                    'PROPERTY_ID' => $propertyId,
                ]
            ]);

            while($enum = $res->fetch()) {
                self::$CACHED[self::CACHE_XMLIDS][$iblockId][$code][$enum['XML_ID']] = $enum['ID'];
            }
        }
    }

    /**
     * @inheritDoc
     */
    public static function getEnumIdByXmlId($xmlId, $code, $iblockId)
    {
        if(empty($xmlId)) {
            throw new IBlockHelperException("XML_ID must not be empty");
        }

        self::checkPropertyEnums($code, $iblockId);

        if(empty(self::$CACHED[self::CACHE_XMLIDS][$iblockId][$code][$xmlId])) {
            throw new IBlockHelperException("No enum ID found with XML_ID '{$xmlId}' on property '{$code}' at iblock '{$iblockId}'");
        }

        return self::$CACHED[self::CACHE_XMLIDS][$iblockId][$code][$xmlId];
    }

    /**
     * @inheritDoc
     */
    public static function getXmlIdByEnumId($enumId, $code, $iblockId)
    {
        if(empty($enumId)) {
            throw new IBlockHelperException("ENUM_ID must not be empty");
        }

        self::checkPropertyEnums($code, $iblockId);

        foreach(self::$CACHED[self::CACHE_XMLIDS][$iblockId][$code] as $xmlid => $id) {
            if($id == $enumId) {
                return $xmlid;
            }
        }

        throw new IBlockHelperException("No enum XML_ID found with ID '{$enumId}' on property '{$code}' at iblock '{$iblockId}'");
    }

    /**
     * @inheritDoc
     */
    public static function getSectionIdByCode($code, $iblockId)
    {
        if(empty($code)) {
            throw new IBlockHelperException("Section code must not be empty");
        }

        if(empty($iblockId)) {
            throw new IBlockHelperException("Section iblock id must not be empty");
        }

        if(empty(self::$CACHED[self::CACHE_SECTIONS][$iblockId][$code])) {
            $res = \Bitrix\Iblock\SectionTable::getList([
                'select' => [
                    'ID'
                ],
                'filter' => [
                    'CODE' => $code,
                    'IBLOCK_ID' => $iblockId
                ]
            ]);

            $section = $res->fetch();
            if(empty($section)) {
                throw new IBlockHelperException("No section found with code '{$code}' at iblock '{$iblockId}'");
            }

            self::$CACHED[self::CACHE_SECTIONS][$iblockId][$code] = $section['ID'];
        }

        if(empty(self::$CACHED[self::CACHE_SECTIONS][$iblockId][$code])) {
            throw new IBlockHelperException("No section found with code '{$code}' at iblock '{$iblockId}'");
        }

        return self::$CACHED[self::CACHE_SECTIONS][$iblockId][$code];
    }

    /**
     * @inheritDoc
     */
    public static function getElementIdByCode($code, $iblockId)
    {
        if(empty($code)) {
            throw new IBlockHelperException("Element code must not be empty");
        }

        if(empty($iblockId)) {
            throw new IBlockHelperException("Element iblock id must not be empty");
        }

        $res = \Bitrix\IBlock\ElementTable::getList([
            'select' => [
                'ID'
            ],
            'filter' => [
                'CODE' => $code,
                'IBLOCK_ID' => $iblockId
            ]
        ]);

        $element = $res->fetch();
        if(empty($element)) {
            throw new IBlockHelperException("No element found with code '{$code}' at iblock '{$iblockId}'");
        }

        return $element['ID'];
    }
}
