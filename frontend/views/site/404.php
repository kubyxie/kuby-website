<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;
use frontend\assets\AppAsset;
use yii\helpers\ArrayHelper;

AppAsset::register($this);
$this->context->layout = false; //不使用布局
$this->title = $name;
?>
<!--头部-->
<?= $this->render('/layouts/head') ?>

<?php $this->beginBody() ?>
<body>
<!--导航条-->
<?= $this->render('/layouts/header') ?>
  <main class="main">
    <div class="tc">
        <img class="error-404" src="../static/images/404.png" alt="">
        <span id="totalSecond">5</span>秒后自动返回
    </div>
  </main>
<!--底部-->
<?= $this->render('/layouts/footer') ?>
<!--左边-->
<?= $this->render('/layouts/left') ?>
<!--右边-->
<?= $this->render('/layouts/right') ?>
<div class="mask"></div>
<script type="text/javascript" charset="utf-8" src="http://lead.soperson.com/20003526/10096887.js"></script>
<script language="javascript" type="text/javascript">
    <!--
    var second = document.getElementById('totalSecond').textContent;
    if (navigator.appName.indexOf("Explorer") > -1)  //判断是IE浏览器还是Firefox浏览器，采用相应措施取得秒数
    {
        second = document.getElementById('totalSecond').innerText;
    } else
    {
        second = document.getElementById('totalSecond').textContent;
    }
    setInterval("redirect()", 1000);  //每1秒钟调用redirect()方法一次
    function redirect()
    {
        if (second < 0)
        {
            location.href = 'http://'+document.domain;
        } else
        {
            if (navigator.appName.indexOf("Explorer") > -1)
            {
                document.getElementById('totalSecond').innerText = second--;
            } else
            {
                document.getElementById('totalSecond').textContent = second--;
            }
        }
    }
    -->
</script>
</body>
<?php $this->endBody() ?>
</html>
