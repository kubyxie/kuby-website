<?php
return [
    'components' => [
        'db' => [
            'class' => yii\db\Connection::className(),
            /*'dsn' => 'mysql:host=localhost;port=3306;dbname=51qiantu',
            'username' => 'root',
            'password' => 'root',*/
            'dsn' => 'mysql:host=192.168.6.245;port=3306;dbname=51uit',
            'username' => '51uit_rw',
            'password' => 'ACSWXW3zAEbSgCme',
            'charset' => 'utf8',
            'tablePrefix' => 'qt_',
        ],
        'mailer' => [
            'class' => yii\swiftmailer\Mailer::className(),
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
    ],
    'bootstrap' => ['debug'],
    'modules' => [
        'debug' => [
            'class' => yii\debug\Module::className(),
            /*'allowedIPs' => ['192.168.6.245', '127.0.0.1','192.168.0.83','14.152.49.*']*/
        ]
    ]
];