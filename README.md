# API интегратора доставок c6v.ru для Yii2
 
## Настройка
В конфиге нужно настроить компонент:
 
```php
'components' => [
    'c6v' => [
        'class' => dicr\c6v\C6VApi::class,
        'key' => 'ваш ключ API'
    ]
];          
```

## Использование
```php
/** @var C6VApi $api */
$api = Yii::$app->c6v;

// получение списка городов
$cities = $api->getCities();

// город по индексу
$city = $api->getCityFromIndex(614000);

// расчет доставки
$data = $api->getPrice([
    'startCity' => 'Москва',
    'endCity' => 'Пермь',
    'weight' => 1,
    'width' => 20,
    'height' => 5,
    'length' => 15
]);

// и т.д. и т.п....
```
