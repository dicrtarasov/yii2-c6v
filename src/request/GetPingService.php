<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 31.08.20 00:29:55
 */

declare(strict_types = 1);
namespace dicr\c6v\request;

use dicr\c6v\C6VRequest;
use yii\base\Exception;
use yii\helpers\Json;

use function array_map;
use function is_array;

/**
 * Проверка доступности серверов транспортных компаний.
 *
 * @link https://c6v.ru/api#8
 */
class GetPingService extends C6VRequest
{
    /**
     * @inheritDoc
     */
    public function func() : string
    {
        return 'getPingServiceResult';
    }

    /**
     * @inheritDoc
     * @return CompanyEntity[] информация о состоянии доступности транспортных компаний
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
            return new CompanyEntity([
                'json' => $data
            ]);
        }, $data);
    }
}
