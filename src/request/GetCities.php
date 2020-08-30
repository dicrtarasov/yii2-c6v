<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 30.08.20 19:37:45
 */

declare(strict_types = 1);
namespace dicr\c6v\request;

use dicr\c6v\C6VRequest;
use yii\base\Exception;
use yii\helpers\Json;

use function array_map;
use function is_array;
use function sort;

/**
 * Запрос списка городов.
 *
 * @link https://c6v.ru/api#2
 */
class GetCities extends C6VRequest
{
    /**
     * @inheritDoc
     */
    public function func() : string
    {
        return 'getCities';
    }

    /**
     * @inheritDoc
     * @return string[] список названий городов в алфавитном порядке
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

        $cities = array_map(static function (array $city) {
            return $city['name'];
        }, $data);

        sort($cities);

        return $cities;
    }
}
