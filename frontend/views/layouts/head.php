<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use frontend\assets\AppAsset;

AppAsset::register($this);
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <?php /*!isset($this->metaTags['keywords']) && $this->registerMetaTag(['name' => 'keywords', 'content' => yii::$app->feehi->seo_keywords], 'keywords');*/?><!--
    --><?php /*!isset($this->metaTags['description']) && $this->registerMetaTag(['name' => 'description', 'content' => yii::$app->feehi->seo_description], 'description');*/?>
    <meta name="keywords" content="<?= $this->keywords ?>">
    <meta name="description" content="<?= $this->description ?>">
    <meta charset="<?= yii::$app->charset ?>">
    <?= Html::csrfMetaTags() ?>
    <meta http-equiv="X-UA-Compatible" content="IE=10,IE=9,IE=8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
    <meta http-equiv="cache-control" content="no-cache">
    <link rel="stylesheet" href="/static/css/index.css">
    <link rel="stylesheet" href="/static/css/pagination.css">
    <!--<link href="../assets/aac468c7/css/bootstrap.css" rel="stylesheet">-->
    <!--
    <script src="http://wechatfe.github.io/vconsole/lib/vconsole.min.js?v=3.2.0"></script>
    <script>
    // init vConsole
    var vConsole = new VConsole();
    console.log('Hello world');
    </script> 
    -->
</head>