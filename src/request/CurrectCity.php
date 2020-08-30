<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 30.08.20 19:51:54
 */

declare(strict_types = 1);
namespace dicr\c6v\request;

use dicr\c6v\C6VRequest;
use Yii;

/**
 * Авто-подсказка города по частичному названию.
 *
 * Название от current + correct = currect
 *
 * @link https://c6v.ru/api#4
 */
class CurrectCity extends C6VRequest
{
    /** @var string частичное название города */
    public $city;

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            ['city', 'trim'],
            ['city', 'required']
        ];
    }

    /**
     * @inheritDoc
     */
    public function func() : string
    {
        // некорректное написание названия (current + correct)
        return 'currectCity';
    }

    /**
     * @inheritDoc
     * @return string найденный город
     */
    public function send() : ?string
    {
        $data = parent::send();

        if (! empty($data['city'])) {
            return (string)$data['city'];
        }

        if (! empty($data['err'])) {
            // моет быть не ошибка, а просто "город не найден"
            Yii::debug($data['err']);
        }

        return null;
    }
}
