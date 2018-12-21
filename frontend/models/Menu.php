<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-03 00:15
 */
namespace frontend\models;

use common\models\Menu as CommonMenu;
//use frontend\models\Category;
use yii\helpers\ArrayHelper;

class Menu extends CommonMenu
{
    public function beforeSave($insert)
    {
        $this->type = self::FRONTEND_TYPE;
        return parent::beforeSave($insert);
    }

    static public function getHeaderMenu()
    {
        return Menu::find()->where(['is_display' => Menu::DISPLAY_YES,'type' => Menu::FRONTEND_TYPE])->orderBy("sort asc,parent_id asc")->asArray()->all();
    }

    static public function getCourseMenu()
    {
        $category = Category::findOne(['alias' => 'course']);
        $descendants = Category::getDescendants(ArrayHelper::getValue($category,'id'));
        array_multisort(array_column($descendants, 'sort'),SORT_ASC,$descendants);
        $hotCourse = array_slice($descendants,0,20);
        return $hotCourse;
    }
}