<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 30.08.20 23:12:11
 */

declare(strict_types = 1);
namespace dicr\c6v\request;

use dicr\c6v\C6VRequest;
use Yii;

use function array_merge;

/**
 * Получение города по индексу.
 *
 * @link https://c6v.ru/api#3
 */
class GetCityFromIndex extends C6VRequest
{
    /** @var int индекс */
    public $postcode;

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            ['postcode', 'required'],
            ['postcode', 'integer', 'min' => 1],
            ['postcode', 'filter', 'filter' => 'intval']
        ]);
    }

    /**
     * @inheritDoc
     */
    public function func() : string
    {
        return 'getCityFromIndex';
    }

    /**
     * @inheritDoc
     * @return ?string название города
     */
    public function send() : ?string
    {
        $data = parent::send();

        if (! empty($data['city'])) {
            return (string)$data['city'];
        }

        if (! empty($data['err'])) {
            // может быть не ошибка, а "индекс не найден"
            Yii::debug($data['err'], __METHOD__);
        }

        return null;
    }
}
