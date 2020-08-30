<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 30.08.20 21:59:49
 */

declare(strict_types = 1);
namespace dicr\c6v\request;

use dicr\c6v\C6VApi;
use dicr\helper\JsonEntity;

/**
 * Информация о доставке.
 */
class DeliveryEntity extends JsonEntity
{
    /**
     * @var string синоним службы доставки (напр. "dellintk")
     * @see C6VApi::COMPANY_ALIASES
     */
    public $server;

    /** @var string название службы доставки (напр. "Деловые Линии" */
    public $name;

    /** @var int срок доставки, дней ("3") */
    public $days;

    /** @var string тип доставки ("Склад-Склад") */
    public $description;

    /** @var int стоимость доставки (624) */
    public $price;
}
