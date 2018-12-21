<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-02 21:00
 */

namespace frontend\models;

use common\models\Category as CommonCategory;

class Category extends CommonCategory
{

    /**
     *  根据分类别名获取某条分类信息
     * @date 2018-09-19
     * @return []
     */
    public function getOneCategoryByAlias($alias)
    {
        return Category::findOne(['alias' => $alias]);
    }

}