Yii2: Adm-Mailing Модуль для Adm CMS
================

Установка
------------
Удобнее всего установить это расширение через [composer](http://getcomposer.org/download/).

```
   "pavlinter/yii2-adm-mailing": "*",
```

Настройка
-------------
```php
'modules' => [
    ...
    'adm' => [
        ...
        'modules' => [
            'admmailing'
        ],
        ...
    ],
    'admmailing' => [
        'class' => 'pavlinter\admmailing\Module',
    ],
    ...
],
'components' => [
    ...
    'mailer' => [
        'class' => 'yii\swiftmailer\Mailer',
        'transport' => [
            'class' => 'Swift_SmtpTransport',
            'host' => 'localhost',
            'username' => 'username',
            'password' => 'password',
            'port' => '587',
            'encryption' => 'ssl',
        ],
    ],
    ...
],
```

Запустить миграцию
-------------
```php
yii migrate --migrationPath=@vendor/pavlinter/yii2-adm-mailing/admmailing/migrations
```