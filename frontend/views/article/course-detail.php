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
$this->title = $model->title;
$this->keywords = $model->seo_keywords;
$this->description = $model->seo_description;
?>
<!--头部-->
<?= $this->render('/layouts/head') ?>
<?php $this->beginBody() ?>
<body>
<!--导航条-->
<?= $this->render('/layouts/header') ?>
  <main class="main ">
    <div class="container clear">
      <section class="section">
        <div class="row clear">
          <div class="col-md-12  ">
            <div class="course-detail box  clear">
              <img class="col-md-5" src="<?php if ($model->thumb) { echo $model->thumb; } else {?> /static/images/course700_486.png<?php }?>">
              <div class="box-bd col-md-7 ">
                <h3 class="course-title"><?= $model->title ?></h3>
                  <?php if ($model->summary) { ?>
                <div class="course-desc">
                  <p><?= $model->summary ?></p>
                </div>
                  <?php } ?>
                <div class="course-type-list mt12">
                    <?php if (is_array(ArrayHelper::getValue($model, 'articleTags'))) foreach (ArrayHelper::getValue($model, 'articleTags') as $key => $tag) { if ($key < 3) {?>
                        <a class="course-type" href="/tag/<?= ArrayHelper::getValue($tag,'value')?>"><?= ArrayHelper::getValue($tag,'value')?></a>
                    <?php }} ?>
                </div>
                <p class="course-summary">
                  <span class="orange">在学：<?= $model->study_number ?></span>
                  <span class="course-price gray ml20">￥<?= $model->price ?></span>
                  <button class="btn doyoo-link">学习咨询</button>
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>
      <section class="section hide" >
        <div class="row">
          <div class="col-md-12">
            <div class="tab tab--white">
              <ul class="tab-bar">
                <li class="active">套餐一</li>
                <li>套餐二</li>
                <li>套餐三</li>
                <li>套餐四</li>
              </ul>
              <div class="tab-content">
                <div class="tab-panel active">
                  <div class="row clear">
                    <div class="col-xs-12 col-sm-12 col-md-pakeage">
                      <div class="pakeage-list">
                        <div class="pakeage-wrap ">
                          <div class="pakeage-container clear">
                            <div class="pakeage-item">
                              <div class="box course-item ">
                                <img src="http://placehold.it/450X300" alt="">
                                <div class="course-info">
                                  <h3 class="course-title">某某课程
                                    <span class="course-type">UI设计</span>
                                  </h3>
                                  <p>
                                    <span class="course-count orange">在学：123434</span>
                                    <span class="course-price gray fr">￥9999.99</span>
                                  </p>
                                </div>
                              </div>
                            </div>
                            <span class="pakeage-add">
                              <i class="iconfont">&#xe624;</i>
                            </span>
                            <div class="pakeage-item">
                              <div class="box course-item ">
                                <img src="http://placehold.it/450X300" alt="">
                                <div class="course-info">
                                  <h3 class="course-title">某某课程
                                    <span class="course-type">UI设计</span>
                                  </h3>
                                  <p>
                                    <span class="course-count orange">在学：123434</span>
                                    <span class="course-price  gray fr">￥9999.99</span>
                                  </p>
                                </div>
                              </div>
                            </div>
                            <span class="pakeage-add">
                              <i class="iconfont">&#xe624;</i>
                            </span>
                            <div class="pakeage-item">
                              <div class="box course-item ">
                                <img src="http://placehold.it/450X300" alt="">
                                <div class="course-info">
                                  <h3 class="course-title">某某课程
                                    <span class="course-type">UI设计</span>
                                  </h3>
                                  <p>
                                    <span class="course-count orange">在学：123434</span>
                                    <span class="course-price  gray fr">￥9999.99</span>
                                  </p>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <a href="javascript:;" class="pakeage-opera pakeage-opera-left">
                          <i class="iconfont">&#xe7be;</i>
                        </a>
                        <a href="javascript:;" class="pakeage-opera pakeage-opera-right">
                          <i class="iconfont">&#xe7bf;</i>
                        </a>
                      </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-pakeage-info">
                      <p class="pakeage-price">原价：￥99999.99</p>
                      <p class="pakeage-discount orange bold">套餐：￥9999.99</p>
                      <p class="pakeage-count">课程：12 门</p>
                      <button class="btn">学习咨询</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <section class="section">
        <div class="row clear">
          <div class="col-md-9">
            <div class="tab tab-course-detail">
              <ul class="tab-bar">
                <li class="active">课程目录</li>
                <li>相关文章</li>
                <li>学员作品</li>
              </ul>
              <div class="tab-content">
                <div class="tab-panel active">
                <?php if ($model) { ?>
                    <?= ArrayHelper::getValue($model,'articleContent.content') ?>
                    <?php } else { ?>
                      <div class="empty">
                          <img src="/static/images/empty.png">
                      </div>
                  <?php } ?>
                </div>
                <div class="tab-panel">
                    <?php if ($tagArticles) { ?>
                  <ul class="recommend-list">
                      <?php if (is_array($tagArticles)) foreach ($tagArticles as $tagArticle) {?>
                    <li class=" ov">
                        <a href="/<?= ArrayHelper::getValue($tagArticle,'type')?>/<?= ArrayHelper::getValue($tagArticle,'id')?>"><img class="fl" src="<?php if (ArrayHelper::getValue($tagArticle,'thumb')) { echo ArrayHelper::getValue($tagArticle,'thumb'); } else {?> /static/images/course268_180.png<?php }?>" alt=""></a>
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
                          <?php if (is_array(ArrayHelper::getValue($tagArticle, 'articleTags'))) foreach (ArrayHelper::getValue($tagArticle, 'articleTags') as $key=>$tag) { if ($key < 3) {?>
                             <a class="course-type" href="/tag/<?= ArrayHelper::getValue($tag,'value')?>"><?= ArrayHelper::getValue($tag,'value')?></a>
                          <?php } } ?>
                      </div>
                      <p class="recommend-info mt20"><?= ArrayHelper::getValue($tagArticle,'summary')?></p>
                    </li>
                      <?php } ?>
                  </ul>

                  <?php } else { ?>
                      <div class="empty">
                          <img src="/static/images/empty.png">
                      </div>
                  <?php } ?>
                </div>

                <div class="tab-panel tc">
                    <?php if ($studentProductArticles) { ?>
                  <ul class="work-list row clear">
                      <?php if (is_array($studentProductArticles)) foreach ($studentProductArticles as $studentProductArticle) {?>
                    <li class="col-xs-6 col-sm-6 col-md-4">
                      <div class="work-item">
                          <a href="/word/<?= ArrayHelper::getValue($studentProductArticle,'id')?>"><img src="<?php if (ArrayHelper::getValue($studentProductArticle,'avatar')) { echo ArrayHelper::getValue($studentProductArticle,'avatar'); } else {?> /static/images/product364_264.png<?php }?>" alt=""></a>
                          <p class="work-summary"><a href="/word/<?= ArrayHelper::getValue($studentProductArticle,'id')?>"><?= ArrayHelper::getValue($studentProductArticle,'title') ?></a>
                          <span class="fr"><?= ArrayHelper::getValue($studentProductArticle,'sub_title') ?></span>
                        </p>
                      </div>
                    </li>
                      <?php } ?>
                  </ul>
                  <?php } else { ?>
                      <div class="empty">
                          <img src="/static/images/empty.png">
                      </div>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">

            <div class="box">
              <div class="box-hd">
                <h2 class="box-title">更多课程</h2>
              </div>
            </div>
            <div class="row clear">
                <?php if (is_array($courses)) foreach ($courses as $course) { ?>
              <div class="col-xs-6  col-md-12">
                <div class="box course-item ">
                    <a href="<?= ArrayHelper::getValue($course, 'id') ?>"><img src="<?php if (ArrayHelper::getValue($course,'thumb')) { echo ArrayHelper::getValue($course,'thumb'); } else {?> /static/images/course268_180.png<?php }?>" alt=""></a>
                  <div class="course-info">
                    <h3 class="course-title"><a href="<?= ArrayHelper::getValue($course, 'id') ?>"><?= ArrayHelper::getValue($course, 'title') ?></a>
                        <?php if (is_array(ArrayHelper::getValue($course, 'articleTags'))) foreach (ArrayHelper::getValue($course, 'articleTags') as $key => $tag) { if ($key < 1) {?>
                            <a class="course-type" href="/tag/<?= ArrayHelper::getValue($tag,'value')?>"><?= ArrayHelper::getValue($tag,'value')?></a>
                        <?php }} ?>

                    </h3>
                    <p>
                      <span class="orange">在学：<?= ArrayHelper::getValue($course, 'study_number') ?></span>
                      <span class="gray fr">￥<?= ArrayHelper::getValue($course, 'price') ?></span>
                    </p>
                  </div>
                </div>
              </div>
                <?php } ?>
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
  <script>
     var docWidth = document.body.scrollWidth;

//var swipers = []
// if (docWidth > 750) {
//   var list = document.querySelectorAll('.pakeage-list');
//   swipers = new Array(list.length)
// }
// new Tab(document.querySelector('.tab--white .tab-bar'), document.querySelectorAll('.tab--white .tab-panel'), function (panel, index) {
//   if (docWidth > 750) {
//     if (!swipers[index]) {
//       swipers[index] = new Swiper(panel.querySelector('.pakeage-list'));
//     }
//   }
// })
 new Tab(document.querySelector('.tab-course-detail .tab-bar'), document.querySelectorAll('.tab-course-detail .tab-panel'))
  </script>
</body>
<?php $this->endBody() ?>
</html>