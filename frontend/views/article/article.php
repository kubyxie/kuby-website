<?php
/**
 * Author: kuby
 * Created at: 2018-09-19 21:16
 */

/* @var $this \yii\web\View */
/* @var $content string */
use frontend\assets\AppAsset;
use yii\helpers\ArrayHelper;

AppAsset::register($this);
$this->context->layout = false; //不使用布局
$this->title = $model->title.' - 51前途网';
$this->keywords = $model->seo_keywords;
$this->description = $model->seo_description;
?>
<!--头部-->
<?= $this->render('/layouts/head') ?>
<?php $this->beginBody() ?>
<body>
<!--导航条-->
<?= $this->render('/layouts/header') ?>
  <main class="main">
    <div class="container">
      <section class="section">
        <div class="row clear">
          <div class="col-xs-12 col-sm-12 col-md-9">
            <div class="box">
              <div class="box-bd">
                <article class="article">
                <?php if ($model) { ?>
                  <h2 class="article-title"><?= $model->title ?></h2>
                  <p class="course-desc-info fs-xs">
                    <span>
                      <i class="iconfont"></i> <?= $model->sub_title ?></span>
                    <span>
                      <i class="iconfont"></i> <?= date('Y.m.d',$model->created_at)?></span>
                    <span class="scan">
                      <i class="iconfont"></i> <?= $model->scan_count ?></span>
                    <span>
                      <i class="iconfont"></i> <?= count(ArrayHelper::getValue($model, 'articleLikes')) ?></span>
                  </p>
                  <div class="course-type-list">
                      <?php if (is_array(ArrayHelper::getValue($model, 'articleTags'))) foreach (ArrayHelper::getValue($model, 'articleTags') as $key => $tag) { if ($key < 3) {?>
                         <a class="course-type" href="/tag/<?= ArrayHelper::getValue($tag,'value')?>"><?= ArrayHelper::getValue($tag,'value')?></a>
                      <?php }} ?>
                  </div>
                  <div class="article-content">
                      <?= $model->articleContent->content ?>
                  </div>
                  <p class="gray mb20">版权申明：如有侵权文章或者侵权的词语、语句、图片，请立即联系我们删除。</p>
                  <p class="tc">
                      <a href="javascript:;" data-action="ding" data-id="<?=$model->id?>" id="addLike" class="addLike">
                          <button class="btn">
                            <i class="iconfont">&#xe67d;</i> <span class="count"><?= $model->getArticleLikeCount() ?></span>
                          </button>
                      </a>
                  </p>
                  <p class="article-links clear">
                      <?php
                      if ($prev !== null) {
                      ?>
                    <a href="<?= $prev->id ?>">上一篇：<?= $prev->title?></a>
                      <?php }  ?>
                      <?php
                      if ($next !== null) {
                      ?>
                    <a class="fr" href="<?= $next->id ?>">下一篇：<?= $next->title ?></a>
                      <?php }?>
                  </p>
                    <?php } else { ?>
                      <div class="empty">
                          <img src="/static/images/empty.png">
                      </div>
                  <?php } ?>
                </article>
              </div>
            </div>
            <div class="box">
              <div class="box-hd">
                <h2 class="box-title">相关文章</h2>
              </div>
              <div class="box-bd">
                <ul class="recommend-list">
                    <?php if (is_array($tagArticles)) foreach ($tagArticles as $tagArticle) {?>
                  <li class=" ov">
                      <a href="<?= ArrayHelper::getValue($tagArticle,'id')?>"><img class="fl" src="<?php if (ArrayHelper::getValue($tagArticle,'thumb')) { echo ArrayHelper::getValue($tagArticle,'thumb'); } else {?> /static/images/course268_180.png<?php }?>" alt=""></a>
                    <h3 class="recommend-title fs-lg orange"><a href="/<?= ArrayHelper::getValue($tagArticle,'type')?>/<?= ArrayHelper::getValue($tagArticle,'id')?>"><?= ArrayHelper::getValue($tagArticle,'title')?></a></h3>
                    <p class="recommend-desc  fs-xs">
                      <span>
                        <i class="iconfont"></i> <?= ArrayHelper::getValue($tagArticle,'sub_title')?></span>
                      <span>
                        <i class="iconfont"></i> <?= date('Y.m.d',ArrayHelper::getValue($tagArticle,'created_at'))?></span>
                      <span>
                        <i class="iconfont"></i> <?= ArrayHelper::getValue($tagArticle,'scan_count')?></span>
                      <span>
                        <i class="iconfont"></i> <?= count(ArrayHelper::getValue($tagArticle,'articleLikes'))?></span>
                    </p>
                    <div class="couse-type-list mt20">
                        <?php if (is_array(ArrayHelper::getValue($tagArticle, 'articleTags'))) foreach (ArrayHelper::getValue($tagArticle, 'articleTags') as $tag) {?>
                          <a class="course-type" href="/tag/<?= ArrayHelper::getValue($tag,'value')?>"><?= ArrayHelper::getValue($tag,'value')?></a>
                        <?php } ?>
                    </div>
                    <p class="recommend-info mt20"><?= ArrayHelper::getValue($tagArticle,'summary')?></p>
                  </li>
                    <?php } ?>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-md-3">
            <div class="box">
              <div class="box-hd">
                <h2 class="box-title">推荐课程</h2>
                <a class="fr gray" href="/course">更多</a>
              </div>
            </div>
            <div class="row">
                <?php if (is_array($recommendCourseArticles)) foreach ($recommendCourseArticles as $recommendCourseArticle) {?>
              <div class="col-xs-6  col-md-12">
                <div class="box course-item ">
                    <a href="/course/<?= ArrayHelper::getValue($recommendCourseArticle,'id')?>"><img class="" src="<?php if (ArrayHelper::getValue($recommendCourseArticle,'thumb')) { echo ArrayHelper::getValue($recommendCourseArticle,'thumb'); } else {?> /static/images/course268_180.png<?php }?>" alt=""></a>
                  <div class="course-info">
                      <h3 class="course-title"><a href="/course/<?= ArrayHelper::getValue($recommendCourseArticle,'id') ?>"><?= ArrayHelper::getValue($recommendCourseArticle,'title') ?></a>
                          <?php if (is_array(ArrayHelper::getValue($recommendCourseArticle, 'articleTags'))) foreach (ArrayHelper::getValue($recommendCourseArticle, 'articleTags') as $key => $tag) { if ($key < 1) {?>
                              <a class="course-type" href="/tag/<?= ArrayHelper::getValue($tag,'value')?>"><?= ArrayHelper::getValue($tag,'value')?></a>
                          <?php }} ?>
                    </h3>
                    <p>
                      <span class="orange">在学：<?= ArrayHelper::getValue($recommendCourseArticle,'study_number') ?></span>
                      <span class="gray fr">￥<?= ArrayHelper::getValue($recommendCourseArticle,'price') ?></span>
                    </p>
                  </div>
                </div>
              </div>
                <?php } ?>
            </div>
           <div class="row">
             <div class="col-xs-12">
             <div class="box">
                <div class="box-hd">
                  <h2 class="box-title">
                    大家感兴趣的内容
                  </h2>
                </div>
                <div class="box-bd">
                  <ul class="link-list">
                      <?php if (is_array($interestedArticles)) foreach ($interestedArticles as $interestedArticle) {?>
                    <li>
                      <a href="/<?= ArrayHelper::getValue($interestedArticle,'type')?>/<?= ArrayHelper::getValue($interestedArticle,'id')?>"><?= ArrayHelper::getValue($interestedArticle,'title')?></a>
                    </li>
                      <?php } ?>
                  </ul>
                </div>
            </div>
             </div>
             <div class=" col-xs-12">
             <div class="box">
              <div class="box-hd">
                <h2 class="box-title">
                  最近更新的内容
                </h2>
              </div>
              <div class="box-bd">
                <ul class="link-list">
                    <?php if (is_array($newsArticles)) foreach ($newsArticles as $newsArticle) {?>
                  <li>
                    <a href="/<?= ArrayHelper::getValue($newsArticle,'type')?>/<?= ArrayHelper::getValue($newsArticle,'id')?>"><?= ArrayHelper::getValue($newsArticle,'title')?></a>
                  </li>
                    <?php } ?>

                </ul>
              </div>
            </div>
             </div>
           </div>
           
          </div>
        </div>
      </section>
    </div>
  </main>
<!--底部-->
<?= $this->render('/layouts/footer') ?>
<!--左边-->
<?= $this->render('/layouts/left') ?>
<!--右边-->
<?= $this->render('/layouts/right') ?>
<div class="mask"></div>
<script type="text/javascript">
    $('.addLike').on('click', function() {
        var _aid = $(this).attr('data-id');
        $.ajax({
            url: "/like",
            data: {aid: _aid},
            type: 'get',
            dataType: 'html',
            success: function(_res) {
                if(_res > 0) {
                    $(".count").text((_res));
                } else {
                    alert('点赞失败');
                }
            }
        });
    });
</script>
</body>
<?php $this->endBody() ?>

</html>