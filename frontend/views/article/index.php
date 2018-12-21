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
$this->title = yii::$app->feehi->website_title;
$this->keywords = yii::$app->feehi->seo_keywords;
$this->description = yii::$app->feehi->seo_description;
?>
<!--头部-->
<?= $this->render('/layouts/head') ?>
<?php $this->beginBody() ?>
<body>
<!--导航条-->
<?= $this->render('/layouts/header') ?>
  <main class="main">
    <div class="container ">
      <div class="banner row clear">
        <div class="col-xs-12 col-sm-9 col-md-9  slider-stage">
          <div class="banner-slider  slider-wrap">
          <ul class="slider-container clear">
              <?php if (is_array($adLists)) foreach ($adLists as $adList) { ?>
             <li class="slider-item">
                <a  href="<?= ArrayHelper::getValue($adList, 'link') ?>">
                    <img src="<?php if (ArrayHelper::getValue($adList, 'img')) { echo ArrayHelper::getValue($adList, 'img'); } else {?> /static/images/index_banner896_400.png<?php }?>" alt="<?= ArrayHelper::getValue($adList, 'desc') ?>" title="<?= ArrayHelper::getValue($adList, 'desc') ?>">
                    <p class="banner-desc"><?= ArrayHelper::getValue($adList, 'desc') ?></p>
                  </a>
             </li>
              <?php } ?>
            </ul>
          </div>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-3 ">
          <div class="banner-list box">
            <div class="box-hd">
              <h2 class="box-title">IT百科</h2>
            </div>
            <div class="box-bd box-bd--pd">
              <ul class="new-list">
                  <?php if (is_array($headLines)) foreach ($headLines as $headLine) {?>
                <li>
                  <a href="/<?= ArrayHelper::getValue($headLine,'type') ?>/<?= ArrayHelper::getValue($headLine,'id') ?>"><?= ArrayHelper::getValue($headLine,'title') ?></a>
                </li>
                  <?php } ?>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="box hot-course">
        <div class="box-hd">
          <h2 class="box-title">热门课程</h2>
        </div>
        <div class="box-bd">
          <ul class="hot-list tc">
              <?php if (is_array($hotCourses)) foreach ($hotCourses as $hotCourse) {?>
                  <li>
                      <a href="/<?= ArrayHelper::getValue($hotCourse,'alias') ?>"><?= ArrayHelper::getValue($hotCourse,'name') ?></a>
                  </li>
              <?php } ?>
          </ul>
        </div>
      </div>
      <section class="section">
        <ul class="section-course row clear">
            <?php if (is_array($hotCourseArticles)) foreach ($hotCourseArticles as $hotCourseArticle) {?>
          <li class="col-xs-6 col-sm-6 col-md-3 ">
            <div class="box course-item">
              <a href="/course/<?= ArrayHelper::getValue($hotCourseArticle,'id') ?>"><img src="<?php if (ArrayHelper::getValue($hotCourseArticle,'thumb')) { echo ArrayHelper::getValue($hotCourseArticle,'thumb'); } else {?> /static/images/course268_180.png<?php }?>" alt=""></a>
              <div class="course-info">
                  <h3 class="course-title"><a href="/course/<?= ArrayHelper::getValue($hotCourseArticle,'id') ?>"><?= ArrayHelper::getValue($hotCourseArticle,'title') ?></a>
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
          </li>
            <?php } ?>
        </ul>
      </section>
      <section class="section">
        <div class="row clear">
          <div class="col-xs-12 col-sm-12 col-md-9">
            <div class="box">
              <div class="box-hd">
                <h2 class="box-title">
                  热门教程
                </h2>
              </div>
              <div class="box-bd box-bd-picture">
                <div class="row clear">
                    <?php if (is_array($hotWordName)) foreach ($hotWordName as $key => $hotName) {?>
                  <div class="col-xs-12 col-sm-12 col-md-6">
                    <h3 class="md-title"><?= $hotName ?></h3>
                    <div class="md-list">
                        <?php if (array_key_exists($key,$hotWordArticles)) foreach ($hotWordArticles[$key] as $row) {?>
                      <a href="/word/<?= ArrayHelper::getValue($row,'id') ?>"><?= ArrayHelper::getValue($row,'title') ?></a>
                        <?php } ?>
                    </div>
                  </div>
                    <?php } ?>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="box">
              <div class="box-hd">
                <h2 class="box-title">
                  图文教程
                </h2>
                  <a href="/word_image_text" class="fr gray link-more">更多</a>
              </div>
              <div class="box-bd">
                <ul class="link-list">
                    <?php if (is_array($hotWordImageTextArticles)) foreach ($hotWordImageTextArticles as $hotWordImageTextArticle) {?>
                        <li>
                            <a href="/word/<?= ArrayHelper::getValue($hotWordImageTextArticle,'id') ?>"><?= ArrayHelper::getValue($hotWordImageTextArticle,'title') ?></a>
                        </li>
                    <?php } ?>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </section>
      <section class="section">
        <div class="section-course row clear">
          <div class="col-xs-12 col-sm-12 col-md-9">
            <div class="box">
              <div class="box-hd">
                <h2 class="box-title">
                  推荐课程
                </h2>
                <div class="menu-wrap fr">
                  <div class="menu-dialog">
                    <ul class="menu-list">
                        <?php if (is_array($recommendCourseArticles)) foreach ($recommendCourseArticles as $key => $recommendCourseArticle) { ?>
                      <li <?php if ($key == $cid) {?> class="active" <?php } ?>>
                        <a href="?cid=<?= $key ?>"><?= ArrayHelper::getValue($recommendCourseName,$key.'.name') ?></a>
                      </li>
                        <?php } ?>
                    </ul>
                  </div>
                  <a href="javascript:;" class="link-nav">
                    <i class="ml10 iconfont orange">&#xe7bd;</i>
                  </a>
                </div>

              </div>

            </div>
            <div class="row clear">
                <?php if (empty($cid)) { $i = 0; if (is_array($recommendCourseArticles)) foreach ($recommendCourseArticles as $key => $recommendCourseArticle) {
                    if (is_array($recommendCourseArticle)) foreach ($recommendCourseArticle as $row) {
                        $i++;
                        if ($i < 7) {
                    ?>
                    <div class="col-xs-6 col-sm-6 col-md-4">
                        <div class="box course-item">
                            <a href="/course/<?= ArrayHelper::getValue($row,'id') ?>"><img src="<?php if (ArrayHelper::getValue($row,'thumb')) { echo ArrayHelper::getValue($row,'thumb'); } else {?> /static/images/course268_180.png<?php }?>" alt=""></a>
                            <div class="course-info">
                                <h3 class="course-title"><a href="course/<?= ArrayHelper::getValue($row,'id') ?>"><?= ArrayHelper::getValue($row,'title') ?></a>
                                    <?php if (is_array(ArrayHelper::getValue($row, 'articleTags'))) foreach (ArrayHelper::getValue($row, 'articleTags') as $key => $tag) { if ($key < 1) {?>
                                        <a class="course-type" href="/tag/<?= ArrayHelper::getValue($tag,'value')?>"><?= ArrayHelper::getValue($tag,'value')?></a>
                                    <?php }} ?>

                                </h3>
                                <p>
                                    <span class="orange">在学：<?= ArrayHelper::getValue($row,'study_number') ?></span>
                                    <span class="gray fr">￥<?= ArrayHelper::getValue($row,'price') ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php }}}} else {
                    if (array_key_exists($cid,$recommendCourseArticles)) foreach (ArrayHelper::getValue($recommendCourseArticles,$cid) as $row) {
                    ?>
                    <div class="col-xs-6 col-sm-6 col-md-4">
                        <div class="box course-item">
                            <a href="/course/<?= ArrayHelper::getValue($row,'id') ?>"><img src="<?php if (ArrayHelper::getValue($row,'thumb')) { echo ArrayHelper::getValue($row,'thumb'); } else {?> /static/images/course268_180.png<?php }?>" alt=""></a>
                            <div class="course-info">
                                <h3 class="course-title"><a href="/course/<?= ArrayHelper::getValue($row,'id') ?>"><?= ArrayHelper::getValue($row,'title') ?></a>
                                    <a class="course-type" href="/<?= ArrayHelper::getValue($row,'category.alias') ?>"><?= ArrayHelper::getValue($row,'category.name') ?></a>
                                </h3>
                                <p>
                                    <span class="orange">在学：<?= ArrayHelper::getValue($row,'study_number') ?></span>
                                    <span class="gray fr">￥<?= ArrayHelper::getValue($row,'price') ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php }} ?>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="box">
              <div class="box-hd">
                <h2 class="box-title">
                  优秀学员感言
                </h2>
                <a href="/word_student_feeling" class="fr gray link-more">更多</a>
              </div>
              <div class="box-bd">
                <ul class="excellent-list row clear">
                    <?php if (is_array($wordStudentFeelingArticles)) foreach ($wordStudentFeelingArticles as $wordStudentFeelingArticle) {
                    ?>
                  <li class="col-xs-12 col-sm-4 col-md-12">
                    <div class="excellent-item">
                      <figure class="excellent-info ">
                          <a href="/word/<?= ArrayHelper::getValue($wordStudentFeelingArticle,'id') ?>"><img class="fl" src="<?php if (ArrayHelper::getValue($wordStudentFeelingArticle,'avatar')) { echo ArrayHelper::getValue($wordStudentFeelingArticle,'avatar'); } else {?> /static/images/student160_160.png<?php }?>" alt=""></a>
                        <h3 class="excellent-title"><?= ArrayHelper::getValue($wordStudentFeelingArticle, 'sub_title')?></h3>
                          <p>
                              <?php if (ArrayHelper::getValue($wordStudentFeelingArticle, 'company_name')) { echo  ArrayHelper::getValue($wordStudentFeelingArticle, 'company_name');} else { echo '某互联网公司';}?>
                        </p>
                        <p class="orange">薪资待遇：￥<?= ArrayHelper::getValue($wordStudentFeelingArticle, 'salary')?></p>
                      </figure>
                      <p><a href="/word/<?= ArrayHelper::getValue($wordStudentFeelingArticle, 'id')?>"><?= ArrayHelper::getValue($wordStudentFeelingArticle, 'summary')?></a></p>
                    </div>
                  </li>
<?php } ?>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </section>
      <section class="section">
        <div class="section-course row clear">
          <div class="col-xs-12 col-sm-12 col-md-9">
            <div class="box">
              <div class="box-hd">
                <h2 class="box-title">推荐教程</h2>
              </div>
              <div class="box-bd">
                <ul class="recommend-list">
                    <?php if (is_array($recommendWordArticles)) foreach ($recommendWordArticles as $recommendWordArticle) {
                    ?>
                  <li class=" ov">
                      <a href="/word/<?= ArrayHelper::getValue($recommendWordArticle,'id') ?>"><img class="fl" src="<?php if (ArrayHelper::getValue($recommendWordArticle,'thumb')) { echo ArrayHelper::getValue($recommendWordArticle,'thumb'); } else {?> /static/images/course268_180.png<?php }?>" alt=""></a>
                    <h3 class="recommend-title fs-lg orange"><a href="/word/<?= ArrayHelper::getValue($recommendWordArticle, 'id')?>"><?= ArrayHelper::getValue($recommendWordArticle, 'title')?></a></h3>
                    <p class="recommend-desc  fs-xs">
                      <span>
                        <i class="iconfont">&#xe620;</i> <?= ArrayHelper::getValue($recommendWordArticle, 'sub_title')?></span>
                      <span>
                        <i class="iconfont">&#xe632;</i> <?= date('Y.m.d',ArrayHelper::getValue($recommendWordArticle, 'created_at'))?></span>
                      <span>
                        <i class="iconfont">&#xe694;</i> <?= ArrayHelper::getValue($recommendWordArticle, 'scan_count')?></span>
                      <span>
                        <i class="iconfont">&#xe67d;</i> <?= count(ArrayHelper::getValue($recommendWordArticle, 'articleLikes')) ?></span>
                    </p>
                    <div class="couse-type-list mt20">
                        <?php if (is_array(ArrayHelper::getValue($recommendWordArticle, 'articleTags'))) foreach (ArrayHelper::getValue($recommendWordArticle, 'articleTags') as $key => $tag) { if ($key < 3) {?>
                      <a class="course-type" href="/tag/<?= ArrayHelper::getValue($tag,'value')?>"><?= ArrayHelper::getValue($tag,'value')?></a>
                        <?php }} ?>
                    </div>
                    <p class="recommend-info mt20"><?= ArrayHelper::getValue($recommendWordArticle,'summary')?></p>
                  </li>
                    <?php } ?>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-3">
            <div class="box">
              <div class="box-hd">
                <h2 class="box-title">优秀学员作品</h2>
                  <a href="/word_student_product" class="fr gray link-more">更多</a>
              </div>
              <div class="box-bd">
                <ul class="work-list row clear">
                    <?php if (is_array($wordStudentProductArticles)) foreach ($wordStudentProductArticles as $wordStudentProductArticle) {
                    ?>
                  <li class="col-xs-6 col-sm-6 col-md-12">
                    <div class="work-item">
                        <a href="/word/<?= ArrayHelper::getValue($wordStudentProductArticle,'id') ?>"><img src="<?php if (ArrayHelper::getValue($wordStudentProductArticle,'avatar')) { echo ArrayHelper::getValue($wordStudentProductArticle,'avatar'); } else {?> /static/images/course268_180.png<?php }?>" alt=""></a>
                        <p class="work-summary"><a href="/word/<?= ArrayHelper::getValue($wordStudentProductArticle,'id') ?>"><?= ArrayHelper::getValue($wordStudentProductArticle, 'title')?></a>
                        <span class="fr"><?= ArrayHelper::getValue($wordStudentProductArticle, 'sub_title')?></span>
                      </p>
                    </div>
                  </li>
                    <?php } ?>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </section>
      <section class="section">
        <div class="section-course row clear">
          <!--<div class="col-xs-12 col-sm-6 col-md-3">
            <div class="box">
              <div class="box-hd">
                <h2 class="box-title">参考文档</h2>
              </div>
              <div class="box-bd">
                <ul class="link-list">
                    <?php /*if (is_array($wordConsultArticles)) foreach ($wordConsultArticles as $wordConsultArticle) {
                    */?>
                  <li>
                    <a href="/word/<?/*= ArrayHelper::getValue($wordConsultArticle,'id') */?>"><?/*= ArrayHelper::getValue($wordConsultArticle, 'title')*/?></a>
                  </li>
                <?php /*} */?>
                </ul>
              </div>
            </div>
          </div>-->
          <!--<div class="col-xs-12 col-sm-6 col-md-3">
            <div class="box">
              <div class="box-hd">
                <h2 class="box-title">就业指导</h2>
              </div>
              <div class="box-bd">
                <ul class="link-list">
                    <?php /*if (is_array($wordJobArticles)) foreach ($wordJobArticles as $wordJobArticle) {
                        */?>
                        <li>
                            <a href="/word/<?/*= ArrayHelper::getValue($wordJobArticle,'id') */?>"><?/*= ArrayHelper::getValue($wordJobArticle, 'title')*/?></a>
                        </li>
                    <?php /*} */?>
                </ul>
              </div>
            </div>
          </div>-->

          <div class="col-xs-12 col-sm-6 col-md-6">
            <div class="box">
              <div class="box-hd">
                <h2 class="box-title">行业资讯</h2>
              </div>
              <div class="box-bd">
                <ul class="link-list">
                    <?php if (is_array($informationArticles)) foreach ($informationArticles as $informationArticle) {
                        ?>
                        <li>
                            <a href="/information/<?= ArrayHelper::getValue($informationArticle,'id') ?>"><?= ArrayHelper::getValue($informationArticle, 'title')?></a>
                        </li>
                    <?php } ?>
                </ul>
              </div>
            </div>
          </div>
            <div class="col-xs-12 col-sm-6 col-md-6">
                <div class="box">
                    <div class="box-hd">
                        <h2 class="box-title">常见问题</h2>
                    </div>
                    <div class="box-bd">
                        <ul class="link-list">
                            <?php if (is_array($questionArticles)) foreach ($questionArticles as $questionArticle) {
                                ?>
                                <li>
                                    <a href="/question/<?= ArrayHelper::getValue($questionArticle,'id') ?>"><?= ArrayHelper::getValue($questionArticle, 'title')?></a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
      </section>
    </div>
  </main>
    <!--底部-->
  <?= $this->render('/layouts/footer-link') ?>
    <!--左边-->
  <?= $this->render('/layouts/left') ?>
    <!--右边-->
  <?= $this->render('/layouts/right') ?>
  <div class="mask"></div>
</body>
<?php $this->endBody() ?>
<?php
if (yii::$app->feehi->website_statics_script) {
    echo yii::$app->feehi->website_statics_script;
}
?>
<script>
  new Slider();
</script>
</html>