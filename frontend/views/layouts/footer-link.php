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
use frontend\models\FriendlyLink;

AppAsset::register($this);
?>
<footer class="footer">
    <div class="container ov">
        <label class="fl">友情链接：</label>
        <div class="friend-links ov">
            <?php
            $links = FriendlyLink::getFriendLink();
            foreach ($links as $link) {
                echo "<a target='_blank' href='{$link['url']}'>{$link['name']}</a>";
            }
            ?>
        </div>
    </div>
    <p class="tc fs-sm mt10">Copyright © 2018 广州市虎拓信息科技有限公司 <a target='_blank' href="http://www.miibeian.gov.cn">粤ICP备18072741号-1</a>  <a target='_blank' href="/sitemap.html">网站地图</a></p>
</footer>
<div class="mask"></div>