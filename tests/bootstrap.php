<?php
/*
 * @copyright 2019-2020 Dicr http://dicr.org
 * @author Igor A Tarasov <develop@dicr.org>
 * @license MIT
 * @version 30.08.20 23:49:31
 */

/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types = 1);

/** @var string */

use yii\log\Dispatcher;

define('YII_ENV', 'dev');

/** @var bool */
define('YII_DEBUG', true);

require_once(dirname(__DIR__) . '/vendor/autoload.php');
require_once(dirname(__DIR__) . '/vendor/yiisoft/yii2/Yii.php');

new yii\console\Application([
    'id' => 'test',
    'basePath' => __DIR__,
    'components' => [
        'cache' => yii\caching\ArrayCache::class,
        'urlManager' => [
            'hostInfo' => 'https://dicr.org'
        ],
        'log' => [
            'class' => Dispatcher::class,
            'targets' => [
                'console' => dicr\log\ConsoleTarget::class
            ]
        ],
        'c6v' => [
            'class' => dicr\c6v\C6VApi::class,
            'key' => '85072163e47398131f7ef31e7302cd9cec3aa1da'
        ]
    ]
]);