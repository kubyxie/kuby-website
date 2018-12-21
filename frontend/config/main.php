<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'user' => [
            'identityClass' => common\models\User::className(),
            'enableAutoLogin' => true,
        ],
        'session' => [
            'timeout' => 1440,//session过期时间，单位为秒
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => yii\log\FileTarget::className(),
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => yii\log\EmailTarget::className(),
                    'levels' => ['error', 'warning'],
                    'except' => [
                        'yii\debug\Module::checkAccess',
                    ],
                    'message' => [
                        'to' => ['313226713@qq.com'],//当触发levels配置的错误级别时，发送到此些邮箱（请改成自己的邮箱）
                        'subject' => '来自 51uit.com 前台的新日志消息',
                    ],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'cache' => [
            'class' => yii\caching\FileCache::className(),//使用文件缓存，可根据需要改成apc redis memcache等其他缓存方式
            'keyPrefix' => 'frontend',       // 唯一键前缀
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,//隐藏index.php
            'enableStrictParsing' => false,
            //'suffix' => '.html',//后缀，如果设置了此项，那么浏览器地址栏就必须带上.html后缀，否则会报404错误
            'rules' => [
                //'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
                //'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>?id=<id>'
                //'detail/<id:\d+>' => 'site/detail?id=$id',
                //'post/22'=>'site/detail',
                //'<controller:detail>/<id:\d+>' => '<controller>/index',
                'tag/<tag:.+>/<sort:\w+>/page-<page:\d+>' => 'search/tag',
                'tag/<tag:.+>/<sort:\w+>' => 'search/tag',
                'tag/<tag:.+>_page-<page:\d+>' => 'search/tag',
                'tag/<tag:.+>' => 'search/tag',
                'page/<name:\w+>' => 'page/view',
                'like' => 'article/like',
                '<cat:\w+>/<sort:\w+>/page-<page:\d+>' => 'article/index',
                //'<cat:\w+>/<sort:\w+>' => 'article/index',
                '<cat:\w+>/page-<page:\d+>' => 'article/index',
                '<cat:\w+>' => 'article/index',
                'word/<id:.+>' => 'article/word',
                'course/<id:.+>' => 'article/course',
                'question/<id:.+>' => 'article/question',
                'information/<id:.+>' => 'article/information',
                'login' => 'site/login',
                'sitemap.html' => 'site/nav',
                'search' => 'search/index',
                '' => 'article/index',
            ],
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => yii\i18n\PhpMessageSource::className(),
                    'basePath' => '@backend/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',

                    ],
                ],
                'front*' => [
                    'class' => yii\i18n\PhpMessageSource::className(),
                    'basePath' => '@frontend/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'frontend' => 'frontend.php',
                        'app/error' => 'error.php',

                    ],
                ],
            ],
        ],
        'assetManager' => [
            'linkAssets' => false,
            'bundles' => [
                yii\web\JqueryAsset::className() => [
                    'js' => [],
                ],
                frontend\assets\AppAsset::className() => [
                    'sourcePath' => '@frontend/web/static',
                    'css' => [
                        'a' => 'css/style.css',
                        'b' => 'plugins/toastr/toastr.min.css',
                    ],
                    'js' => [
                        'a' => 'js/jquery.min.js',
                        'b' => 'js/index.js',
                        'c' => 'plugins/toastr/toastr.min.js',
                    ],
                ],
                frontend\assets\IndexAsset::className() => [
                    'sourcePath' => '@frontend/web/static',
                    'js' => [
                        'a' => 'js/responsiveslides.min.js',
                    ]
                ],
                frontend\assets\ViewAsset::className() => [
                    'sourcePath' => '@frontend/web/static',
                    'css' => [
                        'a' => 'syntaxhighlighter/styles/shCoreDefault.css'
                    ],
                    'js' => [
                        'a' => 'syntaxhighlighter/scripts/shCore.js',
                        'b' => 'syntaxhighlighter/scripts/shBrushJScript.js',
                        'c' => 'syntaxhighlighter/scripts/shBrushPython.js',
                        'd' => 'syntaxhighlighter/scripts/shBrushPhp.js',
                        'e' => 'syntaxhighlighter/scripts/shBrushJava.js',
                        'f' =>'syntaxhighlighter/scripts/shBrushCss.js',
                    ]
                ],
            ]
        ]
    ],
    'params' => $params,
    'on beforeRequest' => [feehi\components\Feehi::className(), 'frontendInit'],
];
