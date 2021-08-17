<?php

namespace Bitrock\Models\Infoblock;
use Birtock\Models\Highload\HLModel;
use Bitrock\Models\Model;
use \Bitrix\Main\Loader;
use Bitrock\Utils\Logger\Logger;
use Letsrock\TextErrorTrait;
use Bitrock\Models\BitrixModelTrait;
use Letsrock\CreatableModel\CreatableModel;
use \CIBlockElement;

Loader::includeModule('iblock');

class InfoblockModel extends Model implements CreatableModel
{
    use BitrixModelTrait, TextErrorTrait;

    public CONST VALUE = 'VALUE';
    public CONST NAME = 'NAME';
    public CONST ID_STRING = 'ID';
    public CONST IB_ID = 'IBLOCK_ID';
    public CONST UF_XML_ID = 'UF_XML_ID';
    public CONST PROPERTIES_STRING = 'PROPERTIES';

    protected $infoblockId;
    protected $symbolCode;
    protected $getAllPropsMode = true;

    /** Если есть необходимость в создание ЧПУ урлов у инфоблока,
     * у модели нужно вызвать метод setSefMode('/section/#SECTION_URL#'...)
     */
    protected $sefMode;

    public function fetch(
        $order = [self::ID_STRING => 'ASC'],
        $filter = [],
        $select = ['*'],
        bool $sefMode = false,
        $arGroupBy = false,
        $arNavStartParams = false
    )
    {
        $filter = $this->setFullFilter($filter);

        $query = CIBlockElement::GetList($order, $filter, $arGroupBy, $arNavStartParams, $select);
        if ($sefMode) $query->SetUrlTemplates($this->getSefMode());

        if ($row = $query->GetNextElement()) {
            $elem = $row->getFields();
            $elem[static::PROPERTIES_STRING] = $this->getAllPropsMode
            ? $row->getProperties()
            : $this->getPropertiesInItem($elem);
            return $elem;
        }

        return [];
    }

    public function fetchAll(
        $order = [self::ID_STRING => 'ASC'],
        $filter = [],
        $select = ['*'],
        bool $sefMode = false,
        $arGroupBy = false,
        $arNavStartParams = false
    )
    {
        $filter = $this->setFullFilter($filter);
        $res = [];

        $query = CIBlockElement::GetList($order, $filter, $arGroupBy, $arNavStartParams, $select);
        if ($sefMode) $query->SetUrlTemplates($this->getSefMode());
        while ($row = $query->GetNextElement()) {
            $element = $row->getFields();
            $element[static::PROPERTIES_STRING] = $this->getAllPropsMode
                ? $row->getProperties()
                : $this->getPropertiesInItem($element);
            $res[] = $element;
        }

        return $res;
    }

    public function add(array $data)
    {
        if (empty($data)) return false;

        $data['IBLOCK_ID'] = $this->getInfoblockId();

        $element = new \CIBlockElement;

        if ($elementId = $element->Add($data)) {
            return $elementId;
        }

        $this->addError($element->LAST_ERROR);
        return false;
    }

    public function createByFactory(array $data)
    {
        return $this->add($data);
    }

    /** Метод для получения значений свойства
     * типа "привязка к элементу" для элемента CIBlockResult
     * @param array $properties
     * @param string $propertyName
     * @param string $propertyType
     * @return array
     */
    public function fetchLinkedProperties(
        $properties,
        $propertyName,
        $order = [self::ID_STRING => 'ASC'],
        $filter = [],
        $select = ['*'],
        bool $sefMode = false,
        $arGroupBy = false,
        $arNavStartParams = false
    ) {
        if (
            empty($properties)
            || empty($propertyName)
            || empty($properties[$propertyName][static::VALUE])
        ) return [];

        $value = $properties[$propertyName][static::VALUE];
        if (is_array($value)) {
            $value = array_unique($value);
        }
        $ids = [self::ID_STRING => $value];
        $preFilter = array_merge($ids, $filter);

        return $this->fetchAll(
            $order,
            $preFilter,
            $select,
            $sefMode,
            $arGroupBy,
            $arNavStartParams
        );
    }

    /** Метод достает свойства типа
     * "привязка к элементу сразу для нескольких элементов CIBlockResult"
     */
    public function fetchAllLinkedProperties(
        array $items,
        string $propertyName,
        array $order = [self::ID_STRING => 'ASC'],
        array $filter = [],
        array $select = ['*'],
        bool $sefMode = false,
        $arGroupBy = false,
        $arNavStartParams = false
    )
    {
        if (empty($items || empty($propertyName))) return false;

        $propertyIds = [];

        //Собираем айдишники со всех элементов
        foreach ($items as $item) {
            $itemProps = $item[static::PROPERTIES_STRING];
            $propertyId = $itemProps[$propertyName][static::VALUE];
            if (!empty($propertyId)) {
                if (is_array($propertyId)) {
                    $propertyIds = array_merge($propertyId, $propertyIds);
                } else {
                    $propertyIds[] = $propertyId;
                }
            }
        }

        if (!empty($propertyIds)) {
            return $this->fetchLinkedProperties(
                [$propertyName => [static::VALUE => $propertyIds]],
                $propertyName,
                $order,
                $filter,
                $select,
                $sefMode,
                $arGroupBy,
                $arNavStartParams
            );
        }

        return false;
    }

    /** Метод для получение элементов из свойств типа Highload
     * @param array $items
     * @param string $propertyName
     * @param string $hlmodelName - название класса хайлод-модели
     * @param array $order
     * @param array $filter
     * @param array $select
     * @return array|bool
     */
    public function fetchAllLinkedHLProperties(
        $items,
        $propertyName,
        HLModel $hlModel,
        $order = [self::ID_STRING => 'ASC'],
        $filter = [],
        $select = ['*']
    )
    {
        if (
            empty($items)
            || empty($propertyName)
            || empty($hlmodelName)
        ) return false;

        $propertyIds = [];

        //Собираем UF_XML_ID со всех элементов
        foreach ($items as $item) {
            $itemProps = $item[static::PROPERTIES_STRING];
            $propertyId = $itemProps[$propertyName][static::VALUE];
            if (!empty($propertyId)) {
                if (is_array($propertyId)) {
                    $propertyIds = array_merge($propertyId, $propertyIds);
                } else {
                    $propertyIds[] = $propertyId;
                }
            }
        }

        if (!empty($propertyIds)) {
            return $this->fetchLinkedHLProperties(
                [$propertyName => [static::VALUE => $propertyIds]],
                $propertyName,
                $hlModel,
                $order,
                $filter,
                $select
            );
        }

        return false;
    }

    public function fetchLinkedHLProperties(
        $properties,
        $propertyName,
        HLModel $hlModel,
        $order = [self::ID_STRING => 'ASC'],
        $filter = [],
        $select = ['*']
    )
    {
        if (
            empty($properties)
            || empty($propertyName)
            || empty($properties[$propertyName][static::VALUE])
        ) return [];

        $value = $properties[$propertyName][static::VALUE];
        $valueIsArray = is_array($value);

        if ($valueIsArray) {
            $preFilter = array_merge($value, $filter);
        } else {
            $preFilter = array_merge($filter, [self::UF_XML_ID => $value]);
        }

        return $hlModel->fetchAll($order, $preFilter, $select);
    }

    public static function addPropertyPrefix($string)
    {
        if (empty($string)) return false;

        return 'PROPERTY_' . $string;
    }

    public static function addValuePostfix($string)
    {
        if (empty($string)) return false;

        return $string . '_VALUE';
    }

    /** Добавляет строке вид PROPERTY_ $string _VALUE */
    public static function addPropertyEnclose($string)
    {
        if (empty($string)) return false;

        return static::addValuePostfix(static::addPropertyPrefix($string));
    }

    public static function removePropertyEnclose($string)
    {
        return preg_replace(['#PROPERTY_#', '#_VALUE#'], '', $string);
    }

    public static function checkPropertyEnclose($string)
    {
        return preg_match('#^PROPERTY_.*_VALUE$#', $string);
    }

    public function getSymbolCode()
    {
        return $this->symbolCode;
    }

    public function getInfoblockId()
    {
        return $this->infoblockId;
    }

    public function setInfoblockId($id)
    {
        if (empty($id)) return false;

        $this->infoblockId = (int)$id;
        return true;
    }

    public function setAllPropsMode($bool)
    {
        $this->getAllPropsMode = $bool;
    }

    private function getPrefilter()
    {
        return ['IBLOCK_ID' => $this->getInfoblockId()];
    }

    protected function setFullFilter($filter)
    {
        if (empty($filter)) return $this->getPrefilter();

        return array_merge($filter, $this->getPrefilter());
    }

    public static function getIbProps(
        $arOrder = [],
        $filter = []
    ) {
        if (empty($filter[static::IB_ID])) return false;

        $propsList = \CIBlockProperty::GetList(
            $arOrder,
            $filter
        );

        while ($propsRow = $propsList->GetNext()) {
            $res[] = $propsRow;
        }

        return collect($res);
    }

    /**
     * @return mixed
     */
    public function getSefMode()
    {
        return $this->sefMode;
    }

    /**
     * @param mixed $sefMode
     */
    public function setSefMode($sefMode): void
    {
        $this->sefMode = $sefMode;
    }

    private static function lookForPropertyKey($key)
    {
        return substr('PROPERTY_', $key);
    }

    /** принимает массив, полученный при помощи GetNext
    и приводит к виду, в котором у элемента сформирован массив PROPERTIES и в него попадают символьный код свойства и массив VALUE со значениями
     */
    private function getPropertiesInItem($item)
    {
        if (empty($item)) return false;
        $properties = [];

        foreach ($item as $fieldKey => $fieldValue) {
            if (static::checkPropertyEnclose($fieldKey)) {
                $clearPropertyCode = static::removePropertyEnclose($fieldKey);

                if ($clearPropertyCode) {
                    $properties[$clearPropertyCode][static::VALUE] = $fieldValue;
                }
            }
        }
        return $properties;
    }

    public static function getInfoblockById($infoblockId)
    {
        if (empty($infoblockId)) return false;

        $infoblock = \CIBlock::GetList(
            [],
            ['ID' => $infoblockId]
        );

        return $infoblock->Fetch();
    }

    public static function getActiveFilter()
    {
        return ['ACTIVE' => 'Y'];
    }
}