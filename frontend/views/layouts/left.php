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
<div class="fixed-sider">
    <a href="javascript:;" class="fixed-sider-title fs-md">课程分类 <i class="iconfont">&#xe64f;</i></a>
    <dl class="fixed-sider-list">
        <?php $courses = Menu::getCourseMenu();
        foreach ($courses as $course) {
            echo "<dd>";
            echo "<a target='_blank' href='/{$course['alias']}'>{$course['name']}</a>";
            echo "</dd>";
        }
        ?>

    </dl>
</div>