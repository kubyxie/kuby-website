<?php
/**
 * Author: kuby
 * Created at: 2018-09-19 21:16
 */

/* @var $this \yii\web\View */
/* @var $content string */

use frontend\assets\AppAsset;
use yii\helpers\ArrayHelper;
use yii\widgets\LinkPager;

AppAsset::register($this);
$this->context->layout = false; //不使用布局
$this->title = $tag.' - 51前途网';
$this->keywords = '';
$this->description = '';
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
              <div class="col-xs-12 col-md-9">
                  <div class="box">
                      <div class="box-hd">
                          <h2 class="box-title"><?= $tag ?></h2>
                          <span class="fr fs-lg"><a href="/tag/<?= $tag ?>/news" <?php if ($sort == 'news') { ?>class="orange" <?php } ?>>最新</a> / <a href="/tag/<?= $tag ?>/hot" <?php if ($sort == 'hot') { ?>class="orange" <?php } ?>>最热</a></span>
                      </div>
                      <div class="box-bd">
                          <ul class="recommend-list">
                              <?php if (is_array($newsTags)) foreach ($newsTags as $newsTag) {?>
                                  <li class=" ov">
                                      <a href="/<?= ArrayHelper::getValue($newsTag,'type')?>/<?= ArrayHelper::getValue($newsTag,'id')?>"><img class="fl" src="<?php if (ArrayHelper::getValue($newsTag,'thumb')) { echo ArrayHelper::getValue($newsTag,'thumb'); } else {?> /static/images/course268_180.png<?php }?>" alt=""></a>
                                      <h3 class="recommend-title fs-lg orange"><a href="/<?= ArrayHelper::getValue($newsTag,'type')?>/<?= ArrayHelper::getValue($newsTag,'id')?>"><?= ArrayHelper::getValue($newsTag,'title')?></a></h3>
                                      <p class="recommend-desc  fs-xs">
                      <span>
                        <i class="iconfont"></i> <?= ArrayHelper::getValue($newsTag,'sub_title')?></span>
                                          <span>
                        <i class="iconfont"></i> <?= date('Y.m.d',ArrayHelper::getValue($newsTag,'created_at'))?></span>
                                          <span>
                        <i class="iconfont"></i> <?= ArrayHelper::getValue($newsTag,'scan_count')?></span>
                                          <span>
                        <i class="iconfont"></i> <?= count(ArrayHelper::getValue($newsTag,'articleLikes'))?></span>
                                      </p>
                                      <div class="couse-type-list mt20">
                                          <?php if (is_array(ArrayHelper::getValue($newsTag, 'articleTags'))) foreach (ArrayHelper::getValue($newsTag, 'articleTags') as $key=>$tag) { if ($key < 3) {?>
                                              <a class="course-type" href="/tag/<?= ArrayHelper::getValue($tag,'value')?>"><?= ArrayHelper::getValue($tag,'value')?></a>
                                          <?php } } ?>
                                      </div>
                                      <p class="recommend-info mt20"><?= ArrayHelper::getValue($newsTag,'summary')?></p>
                                  </li>
                              <?php } ?>
                          </ul>
                          <div class="tc mt10">
                              <?=
                              LinkPager::widget([
                                  'pagination' => $pages,
                              ]);
                              ?>
                          </div>
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
                  <div class="row clear">
                      <?php if (is_array($hotCourseArticles)) foreach ($hotCourseArticles as $hotCourseArticle) {?>
                          <div class="col-xs-6  col-md-12">
                              <div class="box course-item ">
                                  <a href="/course/<?= ArrayHelper::getValue($hotCourseArticle,'id')?>"><img src="<?php if (ArrayHelper::getValue($hotCourseArticle,'thumb')) { echo ArrayHelper::getValue($hotCourseArticle,'thumb'); } else {?> /static/images/course268_180.png<?php }?>" alt=""></a>
                                  <div class="course-info">
                                      <h3 class="course-title"><a href="/course/<?= ArrayHelper::getValue($hotCourseArticle,'id')?>"><?= ArrayHelper::getValue($hotCourseArticle,'title')?></a>
                                          <?php if (is_array(ArrayHelper::getValue($hotCourseArticle, 'articleTags'))) foreach (ArrayHelper::getValue($hotCourseArticle, 'articleTags') as $key => $tag) { if ($key < 1) {?>
                                              <a class="course-type" href="/tag/<?= ArrayHelper::getValue($tag,'value')?>"><?= ArrayHelper::getValue($tag,'value')?></a>
                                          <?php }} ?>
                                      </h3>
                                      <p>
                                          <span class="orange">在学：<?= ArrayHelper::getValue($hotCourseArticle,'study_number') ?></span>
                                          <span class="gray fr">￥<?= ArrayHelper::getValue($hotCourseArticle,'price') ?></span>
                                      </p>
                                  </div>
                              </div>
                          </div>
                      <?php } ?>
                  </div>
                 <div class="row clear">
                     <div class=" col-xs-12">
                     <div class="box">
                      <div class="box-hd">
                          <h2 class="box-title">
                              热门资讯
                          </h2>
                      </div>
                      <div class="box-bd">
                          <ul class="link-list">
                              <?php if (is_array($wordImageTextDescendants)) foreach ($wordImageTextDescendants as $wordImageTextDescendant) {?>
                                  <li>
                                      <a href="/information/<?= ArrayHelper::getValue($wordImageTextDescendant,'id') ?>"><?= ArrayHelper::getValue($wordImageTextDescendant,'title') ?></a>
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
</body>
<?php $this->endBody() ?>

</html>