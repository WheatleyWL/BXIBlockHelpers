<?php
namespace WheatleyWL\BXIBlockHelpers;

use PHPUnit\Framework\TestCase;
use \Bitrix\Main\Entity;

class IBlockHelperTest extends TestCase
{
    private function standardGetIBlockId($code, $type = null, $site = null)
    {
        $filter = [
            '=CODE' => $code
        ];

        if($type !== null) {
            $filter['IBLOCK_TYPE_ID'] = $type;
        }

        if($site !== null) {
            $filter['SITE_SITE_ID'] = $site;
        }

        $id = \Bitrix\Iblock\IblockTable::getList([
            'select' => [
                'ID',
                'SITE_' => 'SITE'
            ],
            'filter' => $filter,
            'runtime' => [
                new Entity\ReferenceField(
                    'SITE',
                    \Bitrix\Iblock\IblockSiteTable::class,
                    [
                        'this.ID' => 'ref.IBLOCK_ID'
                    ]
                )
            ]
        ])->fetch()['ID'];

        return $id;
    }

    public function testGetIBlockByCode()
    {
        $searchIBlockCode = 'pages';
        $iblockId = IBlockHelper::getIBlockIdByCode($searchIBlockCode);

        $expected = $this->standardGetIBlockId($searchIBlockCode);

        $this->assertEquals($expected, $iblockId);
    }

    public function testGetIBlockByCodeAndType()
    {
        $searchIBlockCode = 'pages';
        $searchIBlockType = 'conent'; // -_-
        $iblockId = IBlockHelper::getIBlockIdByCode($searchIBlockCode, $searchIBlockType);

        $expected = $this->standardGetIBlockId($searchIBlockCode, $searchIBlockType);

        $this->assertEquals($expected, $iblockId);
    }

    public function testGetIBlockByCodeTypeAndSite()
    {
        $searchIBlockCode = 'pages';
        $searchIBlockType = 'conent';
        $searchIBlockSite = 's1';
        $iblockId = IBlockHelper::getIBlockIdByCode($searchIBlockCode, $searchIBlockType, $searchIBlockSite);

        $expected = $this->standardGetIBlockId($searchIBlockCode, $searchIBlockType, $searchIBlockSite);

        $this->assertEquals($expected, $iblockId);
    }

    public function testGetIBlockPropertyByCode()
    {
        $searchPropertyCode = 'BLOCK';
        $searchPropertyIBlockCode = 'pages';

        $iblockId = IBlockHelper::getIBlockIdByCode($searchPropertyIBlockCode);
        $propertyId = IBlockHelper::getPropertyIdByCode($searchPropertyCode, $iblockId);

        $expectedIBlock = $this->standardGetIBlockId($searchPropertyIBlockCode);
        $expected = \Bitrix\Iblock\PropertyTable::getList([
            'select' => [
                'ID'
            ],
            'filter' => [
                'IBLOCK_ID' => $expectedIBlock,
                'CODE' => $searchPropertyCode
            ]
        ])->fetch()['ID'];

        $this->assertEquals($expectedIBlock, $iblockId);
        $this->assertEquals($expected, $propertyId);
    }
}
