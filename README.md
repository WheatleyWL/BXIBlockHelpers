IBlockHelpers
=============

Небольшая библиотека для упрощения работы с инфоблоками и Highload блоками в 1C-Bitrix.

# Возможности
* Получение ID инфоблока по символьному коду, типу и сайту;
* Получение ID свойства инфоблока по символьному коду и ID инфоблока;
* Получение ID секции (раздела) инфоблока по символьному коду и ID инфоблока;
* Получение ID пункта в свойстве типа "список" по его XML_ID, коду свойства и ID инфоблока;
* Получение XML_ID пункта в свойства типа "список" по его ID, коду свойства и ID нифоблока;
* Получение класса для работы с Highload блоком по названию Highload блока;
* Получение класса для работы с Highload блоком по названию таблицы Highload блока.

# Установка
Библиотека может быть добавлена в проект при помощи [Composer](https://getcomposer.org/):
    
    composer require wheatleywl/bx-iblock-helpers
    
В файл /local/php_interface/init.php нужно добавить подключение autoload.php из папки vendor.

# Примеры использования

**Получение ID инфоблока по символьному коду, типу и сайту:**
```php
use \WheatleyWL\BXIBlockHelpers\IBlockHelper;

// выборка инфоблока по коду
$iblockId = IBlockHelper::getIBlockIdByCode('pages');

// выборка инфоблока по коду и типу инфоблока
$iblockId = IBlockHelper::getIBlockIdByCode('pages', 'content');

// выборка инфоблока по коду, типу инфоблока и идентификатору сайта
$iblockId = IBlockHelper::getIBlockIdByCode('pages', 'content', 's1');

// выборка инфоблока по коду и идентификатору сайта
$iblockId = IBlockHelper::getIBlockIdByCode('pages', null, 's1');
```
**Примечание**: при запросе ID инфоблока из административного раздела обязательно должен быть указан идентификатор сайта, которому принадлежит инфоблок.

**Получение ID свойства по символьному коду и ID инфоблока:**
```php
$propCode = IBlockHelper::getPropertyIdByCode('BLOCK', 1);
```

**Получение ID секции по символьному коду и ID инфоблока:**
```php
$section = IBlockHelper::getSectionIdByCode('AWESOME_SECTION', 1);
```

**Получение ID пункта по XML_ID:**
```php
$enumId = IBlockHelper::getEnumIdByXmlId('FLAT', 'CONTAINER_STYLE', 1);
```

**Получение XML_ID пункта по его ID:**
```php
$xmlId = IBlockHelper::getXmlIdByEnumId(1, 'CONTAINER_STYLE', 1);
```

**Получение класса Highload блока по его названию:**
```php
use \WheatleyWL\BXIBlockHelpers\HLHelper;

$entity = HLHelper::getClassByName('TestEntity');
```

**Получение класса Highload блока по названию его таблицы:**
```php
$entity = HLHelper::getClassByName('test_entities');
```

# Обработка ошибок
Если запрашеваемая сущность не может быть найдена или возникнет иная ошибка (например, невозможно подключить модуль или переданы некорректные данные)
будет выброшено исключение типа IBlockHelperException или HLHelperException, в зависимости от используемого класса.

