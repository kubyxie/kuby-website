<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

/**
 * @var $this \yii\web\View
 * @var $model common\models\Options
 */

use backend\widgets\ActiveForm;

$this->title = yii::t('app', 'Website Map');
$this->params['breadcrumbs'][] = yii::t('app', 'Website Map');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <?=$this->render('/widgets/_ibox-title')?>
            <div class="ibox-content">
                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
                <?= $form->field($model, 'xmlFile')->fileInput()->label(yii::t('app', 'Website Map Xml')) ?>
                <div class="hr-line-dashed"></div>
                <?= $form->uploadButtons() ?>
                <?php ActiveForm::end() ?>
            </div>
        </div>
        <div class="ibox float-e-margins"><a href="index.php?r=setting/create-sitemap">生成网站地图</a></div>
    </div>
</div>
