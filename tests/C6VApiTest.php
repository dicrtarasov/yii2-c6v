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
use dicr\c6v\request\DeliveryEntity;
use PHPUnit\Framework\TestCase;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;

/**
 * Class C6VApiTest
 *
 * @package dicr\c6v
 */
class C6VApiTest extends TestCase
{
    /**
     * API
     *
     * @return C6VApi
     * @throws InvalidConfigException
     */
    private static function api() : C6VApi
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return Yii::$app->get('c6v');
    }

    /**
     * @throws Exception
     */
    public function testGetCities()
    {
        $ret = self::api()->getCities();
        self::assertNotEmpty($ret);
        self::assertIsArray($ret);
        self::assertArrayHasKey(0, $ret);
        self::assertIsString($ret[0]);
    }

    /**
     * @throws Exception
     */
    public function testGetCityFromIndex()
    {
        $ret = self::api()->getCityFromIndex(614000);
        self::assertSame('Пермь', $ret);
    }

    /**
     * @throws Exception
     */
    public function testCurrectCity()
    {
        $ret = self::api()->currectCity('Мос');
        self::assertSame('Москва', $ret);
    }

    /**
     * @throws Exception
     */
    public function testGetPrice()
    {
        $ret = self::api()->getPrice([
            'startCity' => 'Москва',
            'endCity' => 'Пермь',
            'weight' => 1,
            'width' => 20,
            'height' => 5,
            'length' => 15
        ]);

        self::assertIsArray($ret);
        self::assertArrayHasKey(0, $ret);
        self::assertInstanceOf(DeliveryEntity::class, $ret[0]);
        self::assertNotEmpty($ret[0]->name);
    }

    /**
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function testGetCompany()
    {
        $ret = self::api()->getCompany();
        self::assertIsArray($ret);
        self::assertArrayHasKey(0, $ret);
        self::assertIsString($ret[0]);
    }

    /**
     * @throws Exception
     * @throws InvalidConfigException
     */
    /* Сервис не работает, возвращает ошибку 500
    public function testSetCompany()
    {
        $ret = self::api()->setCompany([
            C6VApi::ALIAS_CDEKTK => false,
        ]);

        self::assertIsArray($ret);
        self::assertArrayHasKey(0, $ret);
        self::assertIsString($ret[0]);
    }*/

    /**
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function testGetPingServiceResult()
    {
        $ret = self::api()->getPingServiceResult();
        self::assertIsArray($ret);
        self::assertArrayHasKey(0, $ret);
        self::assertInstanceOf(CompanyEntity::class, $ret[0]);
    }
}
