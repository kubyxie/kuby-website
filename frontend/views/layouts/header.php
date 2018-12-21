<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

/* @var $this \yii\web\View */
/* @var $content string */
use frontend\assets\AppAsset;
use frontend\models\Menu;
use yii\helpers\ArrayHelper;

AppAsset::register($this);
?>
<header class="header">
    <div class="container">
        <h1 class="title">
            <a href="<?= yii::$app->getHomeUrl() ?>">
                51前途
            </a>
        </h1>
        <nav class="nav">
            <ul class="nav-list clear">
                <?php $menus = Menu::getHeaderMenu();
                foreach ($menus as $menu) {
                    if (ArrayHelper::getValue($menu,'url') == Yii::$app->params['cat']) {
                        echo "<li class='active'>";
                    } else {
                        echo "<li>";
                    }
                    echo "<a href='{$menu['url']}'>{$menu['name']}</a>";
                    echo "</li>";
                }
                ?>
            </ul>
            <ul class="nav-contact">
                <li  class="doyoo-link">
                    <i class="iconfont">&#xe633;</i>
                    <p>意见反馈</p>
                </li>
                <li class="nav-contact-us">
                    <i  class="iconfont">&#xe654;</i>
                    <p>关注我们</p>
                </li>
                <li  class="doyoo-link">
                    <i  class="iconfont">&#xe6ab;</i>
                    <p>立即咨询</p>
                </li>
            </ul>
        </nav>
        <a href="javascript:;" class="link-nav">菜单
            <i class="ml10 iconfont orange">&#xe7bd;</i>
        </a>
    </div>
</header>