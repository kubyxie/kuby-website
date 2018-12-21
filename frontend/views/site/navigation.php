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
$this->title = '导航页';
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
        <div class="box">
          <div class="box-bd">
            <div class="nav-tab">
              <ul class="nav-tab-bar">
                <li class="">
                  <a href="">课程导航</a>
                </li>
                <li>
                  <a href="">教程导航</a>
                </li>
                <li>
                  <a href="">问题导航</a>
                </li>
                <li>
                  <a href="">标签导航</a>
                </li>
              </ul>
              <div class="nav-tab-content">
                <div class="nav-tab-panel ">
                  <ul class="nav-list-first">
                      <?php if (array_key_exists('course',$oneCategory)) foreach ($twoCategory[$oneCategory['course']] as $twoKey => $two) { ?>
                    <li>
                      <label class="nav-list-label nav-list-first-label"><a href="/<?= $categoryList[$two]['alias'] ?>"><?= $categoryList[$two]['name'] ?></a></label>
                      <div class="nav-list-second">
                          <?php if (array_key_exists($two,$threeCategory)) foreach ($threeCategory[$two] as $threeKey => $three) { ?>
                        <div class="nav-list-second-item">
                          <label class="nav-list-label nav-list-second-label"><a href="/<?= $categoryList[$three]['alias'] ?>"><?= $categoryList[$three]['name'] ?></a></label>
                          <ul class="nav-list-third">
                              <?php if (array_key_exists($three,$fourCategory)) foreach ($fourCategory[$three] as $fourKey => $four) { ?>
                            <li>
                              <a href="/<?= $categoryList[$four]['alias'] ?>"><?= $categoryList[$four]['name'] ?></a>
                            </li>
                              <?php } ?>
                          </ul>
                        </div>
                          <?php } ?>
                      </div>
                    </li>
                      <?php } ?>
                  </ul>
                </div>
                <div class="nav-tab-panel">
                  <ul class="nav-list-first">
                      <?php if (array_key_exists('word',$oneCategory)) foreach ($twoCategory[$oneCategory['word']] as $twoKey => $two) { ?>
                    <li>
                      <label class="nav-list-label nav-list-first-label"><a href="/<?= $categoryList[$two]['alias'] ?>"><?= $categoryList[$two]['name'] ?></a></label>
                      <div class="nav-list-second">
                          <?php if (array_key_exists($two,$threeCategory)) foreach ($threeCategory[$two] as $threeKey => $three) { ?>
                        <div class="nav-list-second-item">
                          <label class="nav-list-label nav-list-second-label"><a href="/<?= $categoryList[$three]['alias'] ?>"><?= $categoryList[$three]['name'] ?></a></label>
                          <ul class="nav-list-third">
                              <?php if (array_key_exists($three,$fourCategory)) foreach ($fourCategory[$three] as $fourKey => $four) { ?>
                            <li>
                              <a href="/<?= $categoryList[$four]['alias'] ?>"><?= $categoryList[$four]['name'] ?></a>
                            </li>
                              <?php } ?>
                          </ul>
                        </div>
                          <?php } ?>
                      </div>
                    </li>
                      <?php } ?>
                  </ul>
                </div>
                <div class="nav-tab-panel">
                  <ul class="nav-list-first">
                      <?php if (array_key_exists('question',$oneCategory)) foreach ($twoCategory[$oneCategory['question']] as $twoKey => $two) { ?>
                    <li>
                      <label class="nav-list-label nav-list-first-label"><a href="/<?= $categoryList[$two]['alias'] ?>"><?= $categoryList[$two]['name'] ?></a></label>
                      <div class="nav-list-second">
                          <?php if (array_key_exists($two,$threeCategory)) foreach ($threeCategory[$two] as $threeKey => $three) { ?>
                        <div class="nav-list-second-item">
                          <label class="nav-list-label nav-list-second-label"><a href="/<?= $categoryList[$three]['alias'] ?>"><?= $categoryList[$three]['name'] ?></a></label>
                          <ul class="nav-list-third">
                              <?php if (array_key_exists($three,$fourCategory)) foreach ($fourCategory[$three] as $fourKey => $four) { ?>
                            <li>
                              <a href="/<?= $categoryList[$four]['alias'] ?>"><?= $categoryList[$four]['name'] ?></a>
                            </li>
                              <?php } ?>
                          </ul>
                        </div>
                          <?php } ?>
                      </div>
                    </li>
                      <?php } ?>
                  </ul>
                </div>
                <div class="nav-tab-panel">
                  <div class="nav-tag-list">
                      <?php if (is_array($tagModules)) foreach ($tagModules as $tagModule) { ?>
                          <a class="nav-tag-item" href="/tag/<?= ArrayHelper::getValue($tagModule,'value') ?>"><?= ArrayHelper::getValue($tagModule,'value') ?></a>
                      <?php } ?>
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
  <script>
    new Tab(document.querySelector('.nav-tab-bar'), document.querySelectorAll('.nav-tab-panel'))
  </script>
</body>
<?php $this->endBody() ?>

</html>