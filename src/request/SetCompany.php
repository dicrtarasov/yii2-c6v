<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 30.08.20 23:34:20
 */

declare(strict_types = 1);
namespace dicr\c6v\request;

use dicr\c6v\C6VRequest;
use yii\base\Exception;
use yii\helpers\Json;

use function array_map;
use function explode;
use function is_array;

/**
 * Изменение списка транспортных компаний
 *
 * @link https://c6v.ru/api#7
 */
class SetCompany extends C6VRequest
{
    /**
     * @var bool[] [string алиас_компании => bool вкл./откл.]
     * @see C6VApi::COMPANY_ALIASES
     */
    public $companies;

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            ['companies', function (string $attribute) {
                if (empty($this->{$attribute})) {
                    $this->addError($attribute, 'Не заданы компании');
                } elseif (! is_array($this->{$attribute})) {
                    $this->addError($attribute, $attribute . ' должен быть массивом');
                }
            }]
        ];
    }

    /**
     * @inheritDoc
     */
    public function getJson() : array
    {
        return array_map(static function ($val) {
            return $val ? 'true' : 'false';
        }, $this->companies);
    }

    /**
     * @inheritDoc
     */
    public function setJson(array $json, bool $skipUnknown = true)
    {
        $this->companies = $json;
    }

    /**
     * @inheritDoc
     */
    public function func() : string
    {
        return 'setCompany';
    }

    /**
     * @inheritDoc
     * @return string[] список алиасов включенных компаний
     */
    public function send() : array
    {
        $data = parent::send();

        if (! is_array($data) || empty($data['response'])) {
            throw new Exception('Некорректный ответ: ' . Json::encode($data));
        }

        if ($data['response'] !== 'ok') {
            throw new Exception('Ошибка: ' . $data['desc'] ?? Json::encode($data));
        }

        return (array)explode('-', $data['tk'] ?? '');
    }
}
