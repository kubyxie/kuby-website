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
<div class="content-wrap">
    <div style="text-align:center;padding:10px 0;font-size:16px;background-color:#ffffff;height: 460px">
        <h2 style="font-size:36px;margin-bottom:10px;"><?= Html::encode($this->title) ?></h2>
        <p align="center"><?= nl2br(Html::encode($message)) ?></p>
        <div style="margin-top: 20px">
            <p>
                <?= yii::t('frontend', 'The above error occurred while the Web server was processing your request.') ?>
            </p>
            <p>
                <?= yii::t('frontend', 'Please contact us if you think this is a server error. Thank you.') ?>
            </p>
        </div>
        <span id="totalSecond">5</span>秒后自动返回
    </div>
</div>
<!--底部-->
<?= $this->render('/layouts/footer') ?>
<!--左边-->
<?= $this->render('/layouts/left') ?>
<!--右边-->
<?= $this->render('/layouts/right') ?>
<div class="mask"></div>
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
