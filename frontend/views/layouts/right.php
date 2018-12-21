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

AppAsset::register($this);
?>
<div class="fixed-bar">
    <ul>
        <li  class="doyoo-link">
            <a href="javascript:;">
                <i class="iconfont">&#xe633;</i>
                <p>意见反馈</p>
            </a>
        </li>
        <li class="about-us">
            <a href="javascript:;">
                <i class="iconfont">&#xe654;</i>
                <p>关注我们</p>
            </a>
            <div class="qrcode">
                <img src="/static/images/qrcode.jpg" width="180" alt="">
            </div> 
        </li>
        <li class="doyoo-link">
            <a href="javascript:;">
                <i class="iconfont">&#xe6ab;</i>
                <p>立即咨询</p>
            </a>
        </li>
        <li class="go-top">
            <a href="javascript:;">
                <i class="iconfont">&#xe64f;</i>
                <p>返回顶部</p>
            </a>
        </li>
    </ul>
</div>
<div class="qrcode-dialog">
    <img src="/static/images/qrcode.jpg" width="180" alt="">
</div> 
<script src="/static/js/main.min.js"></script>
<script src="/static/js/jquery.min.js"></script>
<script type="text/javascript" charset="utf-8" src="http://lead.soperson.com/20003526/10096887.js"></script>
<!--百度统计-->
<script>
    var _hmt = _hmt || [];
    (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?05ba9f67f360ac9a5ae12b309f0c1bce";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();
</script>
<!--百度推送-->
<script>
    (function(){
        var bp = document.createElement('script');
        var curProtocol = window.location.protocol.split(':')[0];
        if (curProtocol === 'https') {
            bp.src = 'https://zz.bdstatic.com/linksubmit/push.js';
        }
        else {
            bp.src = 'http://push.zhanzhang.baidu.com/push.js';
        }
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(bp, s);
    })();
</script>
<!--360推送-->
<script>(function(){
        var src = (document.location.protocol == "http:") ? "http://js.passport.qihucdn.com/11.0.1.js?b7ca194cbcd1349f9e133b525ed2f5c7":"https://jspassport.ssl.qhimg.com/11.0.1.js?b7ca194cbcd1349f9e133b525ed2f5c7";
        document.write('<script src="' + src + '" id="sozz"><\/script>');
    })();
</script>




