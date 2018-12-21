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
$this->title = $category->name.' - 精品课程 - 51前途网';
if ($category->alias == 'course') {
    $this->title = '最靠谱的室内设计师培训精品课程 - 51前途网';
}
$this->keywords = '';
$this->description = '51前途网提供最靠谱的室内设计师培训精品课程，想提高自己的设计能力,来51前途网有名师指导是不错的选择。';
?>

<!--头部-->
<?= $this->render('/layouts/head') ?>
<?php $this->beginBody() ?>
<body>
<!--导航条-->
<?= $this->render('/layouts/header') ?>
  <main class="main main-boutique">
    <div class="container clear">
      <div class="box">
        <div class="box-bd">
          <ol class="breadcrumbs">
              <?php if (is_array($parents)) foreach ($parents as $parent) {?>
            <li>
                <?php if (count($parents) == 1) { $alias = 'course'; } else { $alias = ArrayHelper::getValue($parent,'alias');}?>
              <a href="/<?= $alias ?>"><?php if (ArrayHelper::getValue($parent,'level') == 1) { ?> 全部课程 <?php } else { echo ArrayHelper::getValue($parent,'name');}?> </a>
            </li>
              <?php } ?>
              <?php if($category->parent_id) {?><li><?= $category->name ?></li><?php } ?>
          </ol>
          <div class="category">
            <label class="fl category-label">分部课程：</label>
            <div class="category-list">
                <?php if (is_array($branchCourses)) foreach ($branchCourses as $branchCourse) {?>
                    <a href="/<?= ArrayHelper::getValue($branchCourse,'alias') ?>" class="category-item <?php if (ArrayHelper::getValue($branchCourse,'alias') == $category->alias ) {?>orange<?php } ?>"><?= ArrayHelper::getValue($branchCourse,'name') ?></a>
                <?php } ?>
              <a href="javascript:;" class="category-toggle">
                <i class="iconfont">&#xe66c;</i>
              </a>
            </div>
          </div>
          <div class="category-nav">
            <label for="">筛选课程：</label>
            <a class="orange" href="javascript:;"><?php if($category->parent_id) {?><?= $category->name ?><?php } else { echo "全部课程";}?></a>
            <a href="javascript:;" class="fr orange cageogry-link">
              <i class="iconfont">&#xe7c0;</i>
            </a>
            <div class="category-box">
              <dl class="category-list">
                  <?php if (array_key_exists('course',$oneCategory) && array_key_exists($oneCategory['course'],$twoCategory)) foreach ($twoCategory[$oneCategory['course']] as $two) {?>
                  <dd <?php if ($category->id == $two || $category->parent_id == $two || ArrayHelper::getValue($categoryList,$category->parent_id.'.parent_id') == $two) {?> class="active" <?php }?>>
                          <a href="/<?= $categoryList[$two]['alias'] ?>"><?= $categoryList[$two]['name'] ?> </a>
                  </dd>
                  <?php } ?>
              </dl>
                <?php if($category->parent_id > 0) { $threeCat = []; if (array_key_exists($category->id,$threeCategory)) {  $threeCat = $threeCategory[$category->id];} elseif (array_key_exists($category->parent_id,$threeCategory)) { $threeCat = $threeCategory[$category->parent_id];} elseif (array_key_exists($categoryList[$category->parent_id]['parent_id'],$threeCategory)) { $threeCat = $threeCategory[$categoryList[$category->parent_id]['parent_id']]; } ?>
                <?php if($threeCat) { ?>
              <dl class="category-list category-list-2">
                   <?php foreach ($threeCat as $three) { ?>
                <dd <?php if ($category->id == $three || $category->parent_id == $three) {?> class="active" <?php }?>>
                    <a href="/<?= $categoryList[$three]['alias'] ?>"><?= $categoryList[$three]['name'] ?> </a>
                </dd>
                  <?php } ?>
              </dl>
                <?php }} ?>
                <?php $fourId = 0; if ($categoryList[$category->id]['level'] == 3) { $fourId = $category->id;} elseif ($categoryList[$category->id]['level'] == 4) { $fourId = $category->parent_id; }
                if ($fourId && array_key_exists($fourId,$fourCategory)) { ?>
              <dl class="category-list category-list-3">
                  <?php if (is_array($fourCategory[$fourId])) foreach ( $fourCategory[$fourId] as $four) { ?>
                      <dd <?php if ($category->id == $four) {?> class="active" <?php }?>>
                          <a href="/<?= $categoryList[$four]['alias'] ?>"><?= $categoryList[$four]['name'] ?> </a>
                      </dd>
                  <?php } ?>
              </dl>
                <?php } ?>
            </div>
          </div>
          <!-- <img src="" alt=""> -->
          <div class="boutique-cover"  style="background-image:url(<?php if ($category->cover) { echo $category->cover; } else {?> /static/images/course_banner1168_140.png<?php }?>)"></div>
          <p class="lh-default mt16 text-indent"><?= $category->remark ?></p>
        </div>

      </div>
      <section class="section">
        <div class="row">
          <div class="col-md-9">
              <?php if (count($newsCourses) > 0) { ?>
            <ul class="row course-bq">
                <?php if (is_array($newsCourses)) foreach ($newsCourses as $newsCourse) {?>
              <li class="col-xs-6  col-sm-12 col-md-12">
                <div class=" box ov course-item">
                  <a class="course-item-pic fl mr16" href="/course/<?= ArrayHelper::getValue($newsCourse,'id')?>"><img class="" src="<?php if (ArrayHelper::getValue($newsCourse,'thumb')) { echo ArrayHelper::getValue($newsCourse,'thumb'); } else {?> /static/images/course268_180.png<?php }?>" alt=""></a>
                  <div class="box-bd course-info pr">
                      <h3 class="fs-lg "><a href="/course/<?= ArrayHelper::getValue($newsCourse,'id')?>"><?= ArrayHelper::getValue($newsCourse,'title')?></a></h3>
                    <p class="course-summary">
                      <span class="orange">在学：<?= ArrayHelper::getValue($newsCourse,'study_number')?></span>
                      <span class="course-price gray ml20">￥<?= ArrayHelper::getValue($newsCourse,'price')?></span>
                    </p>
                    <div class="course-type-list mt12">
                        <?php if (is_array(ArrayHelper::getValue($newsCourse, 'articleTags'))) foreach (ArrayHelper::getValue($newsCourse, 'articleTags') as $key => $tag) { if ($key < 3) {?>
                          <a class="course-type" href="/tag/<?= ArrayHelper::getValue($tag,'value')?>"><?= ArrayHelper::getValue($tag,'value')?></a>
                        <?php }} ?>
                    </div>
                    <p class="course-desc lh-default mt20"><?= ArrayHelper::getValue($newsCourse,'summary')?>
                    </p>
                    <button class="btn doyoo-link">学习咨询</button>

                  </div>
                </div>
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
              <?php } else { ?>
                  <div class="empty">
                      <img src="/static/images/empty.png">
                  </div>
                  <?php } ?>
          </div>
          <div class="col-xs-12 col-md-3">
            <div class="box">
              <div class="box-hd">
                <h2 class="box-title">推荐课程</h2>
                <a class="fr gray" href="/course">更多</a>
              </div>
            </div>
            <div class="row">
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

  <div class="mask"></div>

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