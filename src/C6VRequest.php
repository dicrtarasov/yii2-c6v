<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 31.08.20 00:03:00
 */

declare(strict_types = 1);
namespace dicr\c6v;

use dicr\helper\JsonEntity;
use dicr\validate\ValidateException;
use Yii;
use yii\base\Exception;
use yii\httpclient\Client;

use function array_merge;

/**
 * Запрос C6V
 *
 * @property-read C6VApi $api
 */
abstract class C6VRequest extends JsonEntity
{
    /** @var C6VApi */
    protected $_api;

    /**
     * C6VRequest constructor.
     *
     * @param C6VApi $api
     * @param array $config
     */
    public function __construct(C6VApi $api, $config = [])
    {
        $this->_api = $api;

        parent::__construct($config);
    }

    /**
     * API.
     *
     * @return C6VApi
     */
    public function getApi()
    {
        return $this->_api;
    }

    /**
     * Функция API.
     *
     * @return string
     */
    abstract public function func() : string;

    /**
     * Отправка запроса.
     *
     * @return mixed данные ответа (переопределяется в наследнике)
     * @throws Exception
     */
    public function send()
    {
        if (! $this->validate()) {
            throw new ValidateException($this);
        }

        $request = $this->_api->httpClient->createRequest()
            ->setMethod('get')
            ->setUrl(array_merge([
                '', 'key' => $this->_api->key, 'q' => $this->func()
            ], $this->getJson()))
            ->setHeaders([
                'Accept' => 'application/json',
                'Accept-Charset' => 'UTF-8'
            ]);

        Yii::debug('Запрос: ' . $request->toString(), __METHOD__);
        $response = $request->send();

        Yii::debug('Ответ: ' . $response->toString(), __METHOD__);

        if (! $response->isOk) {
            throw new Exception('Ошибка запроса: ' . $response->toString());
        }

        $response->format = Client::FORMAT_JSON;

        return $response->data;
    }
}
