<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-02 21:00
 */

namespace frontend\models;

use common\models\Article as CommonArticle;
use common\models\Options;

class Article extends CommonArticle
{
    public static function getAd($type,$name)
    {
        return Options::find()->where(['name' => $name,'type' => $type])->orderBy('sort ASC')->limit(5)->all();
    }
}