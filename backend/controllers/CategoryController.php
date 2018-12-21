<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace backend\controllers;

use backend\actions\ViewAction;
use yii\data\ArrayDataProvider;
use common\models\Category;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;
use yii\helpers\ArrayHelper;
use backend\models\search\CategorySearch;
use Yii;

class CategoryController extends \yii\web\Controller
{

    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(){
                    //$searchModel = new CategorySearch();
                    /*$dataProvider = $searchModel->search(yii::$app->getRequest()->getQueryParams());
                    $datas = $dataProvider->getModels();*/
                    $datas = Category::getCategories();
                    $data = [];
                    if (is_array($datas)) foreach ($datas as $row) {
                        $cover = '';
                        $site = Yii::$app->params['web']['url'];
                        if (ArrayHelper::getValue($row,'cover')) {
                            $cover = $site.ArrayHelper::getValue($row,'cover');
                        }
                        $data[ArrayHelper::getValue($row,'id')] = [
                            'id' => ArrayHelper::getValue($row,'id'),
                            'parent_id' => ArrayHelper::getValue($row,'parent_id'),
                            'name' => ArrayHelper::getValue($row,'name'),
                            'alias' => ArrayHelper::getValue($row,'alias'),
                            'sort' => ArrayHelper::getValue($row,'sort'),
                            'remark' => ArrayHelper::getValue($row,'remark'),
                            'cover' => $cover,
                            'level' => ArrayHelper::getValue($row,'level'),
                            'created_at' => ArrayHelper::getValue($row,'created_at'),
                            'updated_at' => ArrayHelper::getValue($row,'updated_at'),
                        ];
                    }
                    $dataProvider = new ArrayDataProvider([
                        'allModels' => $data,
                        'pagination' => [
                            'pageSize' => 10
                        ]
                    ]);
                    return [
                        'dataProvider' => $dataProvider,
                        //'searchModel' => $searchModel,
                    ];
                }
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
                'modelClass' => Category::className(),
            ],
            'create' => [
                'class' => CreateAction::className(),
                'modelClass' => Category::className(),
                'successRedirect' => 'index.php?r=category/index'
            ],
            'update' => [
                'class' => UpdateAction::className(),
                'modelClass' => Category::className(),
                'successRedirect' => 'index.php?r=category/index'
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'modelClass' => Category::className(),
            ],
            'sort' => [
                'class' => SortAction::className(),
                'modelClass' => Category::className(),
            ],
        ];
    }

}