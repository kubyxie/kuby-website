<?php
$config = [
    'name' => 'CMS',
    'version' => '2.0.4.1',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'db' => [//数据库配置，这里的配置可能会被conf/db.local main-local.php配置覆盖
            'class' => yii\db\Connection::className(),
            /*'dsn' => 'mysql:host=localhost;port=3306;dbname=51qiantu',
            'username' => 'root',
            'password' => 'root',*/
            'dsn' => 'mysql:host=192.168.6.245;dbname=51uit',
            'username' => '51uit_rw',
            'password' => 'ACSWXW3zAEbSgCme',
            'charset' => 'utf8',
        ],
        'cdn' => [//支持使用 七牛 腾讯云 阿里云 网易云 具体配置请参见 http://doc.feehi.com/cdn.html
            'class' => feehi\cdn\DummyTarget::className(),//不使用cdn
        ],
        'cache' => [//缓存组件 具体配置请参考 http://doc.feehi.com/configs.html
            'class' => yii\caching\DummyCache::className(),//不使用缓存
        ],
        'formatter' => [//格式显示配置
            'dateFormat' => 'php:Y-m-d H:i',
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
            'currencyCode' => 'CHY',
            'nullDisplay' => '-',
        ],
        'mailer' => [//邮箱发件人配置，会被main-local.php以及后台管理页面中的smtp配置覆盖
            'class' => yii\swiftmailer\Mailer::className(),
            'viewPath' => '@common/mail',
            'useFileTransport' => false,//false发送邮件，true只是生成邮件在runtime文件夹下，不发邮件
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.feehi.com',  //每种邮箱的host配置不一样
                'username' => 'admin@feehi.com',
                'password' => 'password',
                'port' => '586',
                'encryption' => 'tls',
            ],
            'messageConfig' => [
                'charset' => 'UTF-8',
                'from' => ['admin@feehi.com' => 'Feehi CMS robot ']
            ],
        ],
        'feehi' => [
            'class' => feehi\components\Feehi::className(),
        ],
        'authManager' => [
            'class' => yii\rbac\DbManager::className(),
        ],
        'assetManager' => [
            'linkAssets' => false,
            'bundles' => [
                yii\widgets\ActiveFormAsset::className() => [
                    'js' => [
                        'a' => 'yii.activeForm.js'
                    ],
                ],
                yii\bootstrap\BootstrapAsset::className() => [
                    'css' => [],
                    'sourcePath' => null,
                ],
                yii\captcha\CaptchaAsset::className() => [
                    'js' => [
                        'a' => 'yii.captcha.js'
                    ],
                ],
                yii\grid\GridViewAsset::className() => [
                    'js' => [
                        'a' => 'yii.gridView.js'
                    ],
                ],
                yii\web\JqueryAsset::className() => [
                    'js' => [
                        'a' => 'jquery.js'
                    ],
                ],
                yii\widgets\PjaxAsset::className() => [
                    'js' => [
                        'a' => 'jquery.pjax.js'
                    ],
                ],
                yii\web\YiiAsset::className() => [
                    'js' => [
                        'a' => 'yii.js'
                    ],
                ],
                yii\validators\ValidationAsset::className() => [
                    'js' => [
                        'a' => 'yii.validation.js'
                    ],
                ],
            ],
        ],
    ],
];
$install = yii::getAlias('@common/config/conf/db.php');
if( file_exists($install) ){
    return yii\helpers\ArrayHelper::merge($config, (require $install));
}
return $config;