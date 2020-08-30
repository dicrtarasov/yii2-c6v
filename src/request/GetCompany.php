<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 30.08.20 21:48:14
 */

declare(strict_types = 1);
namespace dicr\c6v\request;

use dicr\c6v\C6VRequest;
use yii\base\Exception;
use yii\helpers\Json;

use function is_array;

/**
 * Получение списка транспортных компаний, указанных в личном кабинете.
 *
 * @link https://c6v.ru/api#6
 */
class GetCompany extends C6VRequest
{
    /**
     * @inheritDoc
     */
    public function func() : string
    {
        return 'getCompany';
    }

    /**
     * @inheritDoc
     * @return string[] список синонимов транспортных компаний
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

        return $data;
    }
}
