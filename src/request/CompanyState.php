<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 30.08.20 22:22:00
 */

declare(strict_types = 1);
namespace dicr\c6v\request;

use dicr\c6v\C6VApi;
use dicr\helper\JsonEntity;

/**
 * Информация о статусе доступности компании.
 */
class CompanyState extends JsonEntity
{
    /**
     * @var string алиас компании ("dellintk")
     * @see C6VApi::COMPANY_ALIASES
     */
    public $name;

    /** @var ?string HTTP-статус ответа ("200" или "") */
    public $status;

    /** @var ?string ("1.39") */
    public $upTime;

    /** @var ?string ("ok", "server not available") */
    public $errText;

    /**
     * @inheritDoc
     */
    public function attributeFields() : array
    {
        // отключаем переопределение аттрибутов в snake_case поля
        return [];
    }
}
