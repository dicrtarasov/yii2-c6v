<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 31.08.20 00:29:55
 */

declare(strict_types = 1);
namespace dicr\c6v;

use dicr\c6v\request\CompanyEntity;
use dicr\c6v\request\CurrectCity;
use dicr\c6v\request\DeliveryEntity;
use dicr\c6v\request\GetCities;
use dicr\c6v\request\GetCityFromIndex;
use dicr\c6v\request\GetCompany;
use dicr\c6v\request\GetPingService;
use dicr\c6v\request\GetPrice;
use dicr\c6v\request\SetCompany;
use dicr\http\CachingClient;
use dicr\http\HttpCompressionBehavior;
use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\base\InvalidConfigException;

use function array_merge;

/**
 * API агрегатора доставки.
 *
 * @property-read CachingClient $httpClient клиент HTTP
 * @link https://c6v.ru/api
 * @noinspection MissingPropertyAnnotationsInspection
 */
class C6VApi extends Component
{
    /** @var string Деловые линии */
    public const ALIAS_DELLINTK = 'dellintk';

    /** @var string */
    public const ALIAS_JELDORTK = 'jeldortk';

    /** @var string */
    public const ALIAS_GTDTK = 'gtdtk';

    /** @var string */
    public const ALIAS_NRGTK = 'nrgtk';

    /** @var string */
    public const ALIAS_PECTK = 'pectk';

    /** @var string */
    public const ALIAS_CDEKTK = 'cdektk';

    /** @var string */
    public const ALIAS_EMSPOST = 'emspost';

    /** @var string */
    public const ALIAS_DIMEXTK = 'dimextk';

    /** @var string */
    public const ALIAS_MAGICTRANSTK = 'magictranstk';

    /** @var string */
    public const ALIAS_VOZOVOZTK = 'vozovoztk';

    /** @var string */
    public const ALIAS_GLAVDOSTAVKATK = 'glavdostavkatk';

    /** @var string */
    public const ALIAS_BAIKALSRTK = 'baikalsrtk';

    /** @var string */
    public const ALIAS_RUSSIANPOST = 'russianpost';

    /**
     * @var string[] идентификаторы транспортных компаний
     */
    public const COMPANY_ALIASES = [
        self::ALIAS_DELLINTK, self::ALIAS_JELDORTK, self::ALIAS_GTDTK, self::ALIAS_NRGTK, self::ALIAS_PECTK,
        self::ALIAS_CDEKTK, self::ALIAS_EMSPOST, self::ALIAS_DIMEXTK, self::ALIAS_MAGICTRANSTK, self::ALIAS_VOZOVOZTK,
        self::ALIAS_GLAVDOSTAVKATK, self::ALIAS_BAIKALSRTK, self::ALIAS_RUSSIANPOST
    ];

    /** @var string API URL */
    public const URL_API = 'http://api.c6v.ru';

    /** @var string API URL */
    public $url = self::URL_API;

    /** @var string ключ API */
    public $key;

    /** @var array конфиг httpClient */
    public $httpClientConfig = [];

    /**
     * @inheritDoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if (empty($this->url)) {
            throw new InvalidConfigException('url');
        }

        if (empty($this->key)) {
            throw new InvalidConfigException('key');
        }
    }

    /** @var CachingClient */
    private $_httpClient;

    /**
     * Клиент HTTP.
     *
     * @return CachingClient
     * @throws InvalidConfigException
     */
    public function getHttpClient() : CachingClient
    {
        if (! isset($this->_httpClient)) {
            $this->_httpClient = Yii::createObject(array_merge([
                'class' => CachingClient::class,
                'baseUrl' => $this->url,
                'as compression' => HttpCompressionBehavior::class
            ]));
        }

        return $this->_httpClient;
    }

    /**
     * Создает запрос.
     *
     * @param array $config
     * @return C6VRequest
     * @throws InvalidConfigException
     */
    public function createRequest(array $config) : C6VRequest
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return Yii::createObject($config, [$this]);
    }

    /**
     * Список городов.
     *
     * @return array
     * @throws Exception
     */
    public function getCities() : array
    {
        /** @var GetCities $request */
        $request = $this->createRequest([
            'class' => GetCities::class
        ]);

        return $request->send();
    }

    /**
     * Названия города по индексу.
     *
     * @param int $postcode индекс
     * @return ?string название города
     * @throws Exception
     */
    public function getCityFromIndex(int $postcode) : ?string
    {
        /** @var GetCityFromIndex $request */
        $request = $this->createRequest([
            'class' => GetCityFromIndex::class,
            'postcode' => $postcode
        ]);

        return $request->send();
    }

    /**
     * Авто-подсказка названия города по его части.
     *
     * @param string $city
     * @return ?string полное название города
     * @throws Exception
     */
    public function currectCity(string $city) : ?string
    {
        /** @var CurrectCity $request */
        $request = $this->createRequest([
            'class' => CurrectCity::class,
            'city' => $city
        ]);

        return $request->send();
    }

    /**
     * Расчет вариантов доставки.
     *
     * @param array $config конфиг запроса GetPrice
     * @return DeliveryEntity[] информация о доставки
     * @throws Exception
     */
    public function getPrice(array $config) : array
    {
        /** @var GetPrice $request */
        $request = $this->createRequest([
                'class' => GetPrice::class,
            ] + $config);

        return $request->send();
    }

    /**
     * Список транспортных компаний.
     *
     * @return string[] алиасы компаний
     * @throws Exception
     */
    public function getCompany() : array
    {
        /** @var GetCompany $request */
        $request = $this->createRequest([
            'class' => GetCompany::class,
        ]);

        return $request->send();
    }

    /**
     * Включение/отключение компаний.
     *
     * @param bool[] $companies [алиас_компании => bool вкл./откл]
     * @return string[] включенные компании
     * @throws Exception
     */
    public function setCompany(array $companies) : array
    {
        /** @var SetCompany $request */
        $request = $this->createRequest([
            'class' => SetCompany::class,
            'companies' => $companies
        ]);

        return $request->send();
    }

    /**
     * Запрос состояния доступности транспортных компаний.
     *
     * @return CompanyEntity[]
     * @throws Exception
     */
    public function getPingServiceResult() : array
    {
        /** @var GetPingService $request */
        $request = $this->createRequest([
            'class' => GetPingService::class
        ]);

        return $request->send();
    }
}
