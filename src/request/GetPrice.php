<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 30.08.20 23:26:33
 */

declare(strict_types = 1);
namespace dicr\c6v\request;

use dicr\c6v\C6VRequest;
use yii\base\Exception;
use yii\helpers\Json;

use function array_map;
use function array_merge;
use function in_array;
use function is_array;
use function is_bool;

/**
 * Расчет сроков и стоимости доставки.
 * Используйте только указанные в списке метода getCities названия городов.
 *
 * Вы также можете указать необязательные GET параметры arrivalDoor (расчет от двери)
 * и derivalDoor (расчет до двери), которые принимают boolean значение true либо false.
 * Если не указывать данные параметры, то по-умолчанию расчет будет производиться по схеме
 * склад-склад или автоматически рассчитывать склад-дверь, либо дверь-склад, если в выбранном
 * городе нет пункта выдачи и транспортной компании придется доставлять груз курьером.
 *
 * @link https://c6v.ru/api#5
 */
class GetPrice extends C6VRequest
{
    /**
     * @var string Город отправления
     * Название должно совпадать с названием города возвращаемым через getCities.
     */
    public $startCity;

    /**
     * @var string Город получателя
     * Название должно совпадать с названием города возвращаемым через getCities.
     */
    public $endCity;

    /** @var int вес посылки в кг (идиоты) */
    public $weight;

    /** @var int ширина посылки в см */
    public $width;

    /** @var int высота посылки в см */
    public $height;

    /** @var int длины посылки в см */
    public $length;

    /** @var ?bool доставка от двери */
    public $arrivalDoor;

    /** @var ?bool доставка до двери */
    public $derivalDoor;

    /**
     * @inheritDoc
     */
    public function attributeFields() : array
    {
        /** не конвертировать поля в snake_case */
        return [];
    }

    /**
     * @inheritDoc
     */
    public function value2data(string $attribute, $value)
    {
        // конвертируем bool в string
        if (in_array($attribute, ['arrivalDoor', 'derivalDoor'], true)) {
            return $this->{$attribute} === true ? 'true' : ($this->{$attribute} === false ? 'false' : null);
        }

        return parent::value2data($attribute, $value);
    }

    /**
     * @inheritDoc
     */
    public function data2value(string $attribute, $data)
    {
        if (in_array($attribute, ['arrivalDoor', 'derivalDoor'], true)) {
            $map = ['true' => true, 'false' => false];

            return $map[$data] ?? null;
        }

        return parent::data2value($attribute, $data);
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['startCity', 'endCity'], 'trim'],
            [['startCity', 'endCity'], 'required'],

            [['width', 'height', 'length'], 'required'],
            [['width', 'height', 'length'], 'integer', 'min' => 1],

            [['arrivalDoor', 'derivalDoor'], 'default'],
            [['arrivalDoor', 'derivalDoor'], function (string $attribute) {
                if (isset($this->{$attribute})) {
                    if ($this->{$attribute} === 'true') {
                        $this->{$attribute} = true;
                    } elseif ($this->{$attribute} === 'false') {
                        $this->{$attribute} = false;
                    } elseif (! is_bool($this->{$attribute})) {
                        $this->addError($attribute, 'Значение ' . $attribute . ' должно быть булевым');
                    }
                }
            }],
        ]);
    }

    /**
     * @inheritDoc
     */
    public function func() : string
    {
        return 'getPrice';
    }

    /**
     * @inheritDoc
     * @return DeliveryEntity[] список вариантов доставки
     */
    public function send() : array
    {
        $data = parent::send();

        if (! is_array($data)) {
            throw new Exception('Некорректный ответ: ' . Json::encode($data));
        }

        if (! empty($data['err'])) {
            throw new Exception('Ошибка: ' . $data['err']);
        }

        return array_map(static function (array $data) {
            return new DeliveryEntity(['json' => $data]);
        }, $data);
    }
}
