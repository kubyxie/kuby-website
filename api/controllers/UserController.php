<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-30 18:10
 */
namespace api\controllers;

use yii\web\Response;

class UserController extends \yii\rest\ActiveController
{
    public $modelClass = "api\models\User";

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;//默认浏览器打开返回json
        return $behaviors;
    }

    public function actions()
    {
        return [];
    }

    public function verbs()
    {
        return [
            'index' => ['GET'],
            'sao-fei' => ['GET'],
            'login' => ['POST'],
            'register' => ['POST'],
        ];
    }

    public function actionIndex()
    {
        return [
            "这是一个测试api的例子"
        ];
    }

    public function actionSaoFei()
    {
        return [
            "骚飞确实好骚，太骚了"
        ];
    }
}
