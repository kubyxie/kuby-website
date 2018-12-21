<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-02 22:48
 */

namespace frontend\controllers;

use yii;
use yii\web\Controller;
use frontend\models\Article;
use yii\web\NotFoundHttpException;
use common\helpers\FamilyTree;
use frontend\models\Category;
use yii\helpers\ArrayHelper;
use common\models\meta\ArticleMetaTag;

class PageController extends Controller
{

    /**
     * 单页
     *
     * @param string $name
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($name = '')
    {
        if ($name == '') {
            $name = yii::$app->getRequest()->getPathInfo();
        }
        $model = Article::findOne(['type' => Article::SINGLE_PAGE, 'sub_title' => $name]);
        if (empty($model)) {
            throw new NotFoundHttpException('None page named ' . $name);
        }
        $parents = (new FamilyTree(Category::getCategories()))->getAncectors($model->cid);
        if ($parents)
        {
            array_multisort(array_column($parents, 'level'),SORT_ASC,$parents);
        } else {
            $parents = [Category::findOne($model->cid)];
        }
        $categoryAliasList = [];
        if (is_array($parents)) foreach ($parents as $parent) {
            $categoryAliasList[] = ArrayHelper::getValue($parent,'alias');
        }
        $data = $this->getArticle($model->id,$model);
        $data['model'] = $model;
        Yii::$app->params['cat'] = '/page/'.$name;
        return $this->render('view', $data);
    }

    /**
     *  文章内容页
     * @date 2018-09-20
     * @return []
     */
    public function getArticle($id,$model)
    {
        $prev = Article::find()
            ->where(['status' => Article::ARTICLE_PUBLISHED,'cid' => $model->cid,'type' => Article::SINGLE_PAGE])
            ->andWhere(['>', 'id', $id])
            ->orderBy("sort asc,created_at desc,id desc")
            ->limit(1)
            ->one();
        $next = Article::find()
            ->where(['status' => Article::ARTICLE_PUBLISHED,'cid' => $model->cid,'type' => Article::SINGLE_PAGE])
            ->andWhere(['<', 'id', $id])
            ->orderBy("sort desc,created_at desc,id asc")
            ->limit(1)
            ->one();//->createCommand()->getRawSql();
        //相关文章
        $tags = ArrayHelper::getValue($model,'articleTags');
        $tagsArray = ArrayHelper::getColumn($tags,'value');
        $tagAid = (new ArticleMetaTag())->getAidsByTag($tagsArray);
        $tagModules = Article::find()->where(['status' => Article::ARTICLE_PUBLISHED,'id' => $tagAid])->orderBy('created_at desc,id desc')->all();
        //$tagArticles = array_slice($tagModules,0,4);
        $tagArticles = $tagModules;
        //推荐课程(取2条)升序排序//课程分类
        $recommendCategory = Category::findOne(['alias' => 'course']);
        $recommendDescendants = Category::getDescendants($recommendCategory['id']);
        $recommendCid = ArrayHelper::getColumn($recommendDescendants, 'id');
        $recommendCid[] = $recommendCategory['id'];
        $recommendCourseArticle = Article::find()->where(['status' => Article::ARTICLE_PUBLISHED,'cid' => $recommendCid,'flag_recommend' => Article::HOT_YES])->orderBy(['sort' => SORT_ASC, 'created_at' => SORT_DESC, 'id' => SORT_DESC])->limit(2)->all();

        //教程文档分类
        $wordCategory = Category::findOne(['alias' => 'word']);
        $wordDescendants = Category::getDescendants($wordCategory['id']);
        $wordCid = ArrayHelper::getColumn($wordDescendants, 'id');
        $wordCid[] = $wordCategory['id'];
        //常见问题分类
        $questionCategory = Category::findOne(['alias' => 'question']);
        $questionDescendants = Category::getDescendants($questionCategory['id']);
        $questionCid = ArrayHelper::getColumn($questionDescendants, 'id');
        $questionCid[] = $questionCategory['id'];
        //行业资讯分类
        $informationCategory = Category::findOne(['alias' => 'information']);
        $informationDescendants = Category::getDescendants($informationCategory['id']);
        $informationCid = ArrayHelper::getColumn($informationDescendants, 'id');
        $informationCid[] = $informationCategory['id'];
        //大家感兴趣的内容取13条
        $interestedArticles = Article::find()->where(['status' => Article::ARTICLE_PUBLISHED])->orderBy(['scan_count' => SORT_DESC])->limit(13)->all();
        //最近更新的内容取13条
        $newsArticles = Article::find()->where(['status' => Article::ARTICLE_PUBLISHED])->orderBy(['created_at' => SORT_DESC,'id' => SORT_DESC])->limit(13)->all();
        $interestedArticleList = [];
        if (is_array($interestedArticles)) foreach ($interestedArticles as $interestedArticle) {
            if (in_array($interestedArticle->cid,$recommendCid)) {
                $type = 'course';
            } elseif (in_array($interestedArticle->cid,$wordCid)) {
                $type = 'word';
            } elseif (in_array($interestedArticle->cid,$questionCid)) {
                $type = 'question';
            } elseif (in_array($interestedArticle->cid,$informationCid)) {
                $type = 'information';
            }
            $interestedArticleList[] = [
                'id' => $interestedArticle->id,
                'title' => $interestedArticle->title,
                'type' => $type,
            ];
        }
        $newsArticleList = [];
        if (is_array($newsArticles)) foreach ($newsArticles as $newsArticle) {
            if (in_array($newsArticle->cid,$recommendCid)) {
                $type = 'course';
            } elseif (in_array($newsArticle->cid,$wordCid)) {
                $type = 'word';
            } elseif (in_array($newsArticle->cid,$questionCid)) {
                $type = 'question';
            } elseif (in_array($newsArticle->cid,$informationCid)) {
                $type = 'information';
            }
            $newsArticleList[] = [
                'id' => $newsArticle->id,
                'title' => $newsArticle->title,
                'type' => $type,
            ];
        }
        return [
            'prev' => $prev,
            'next' => $next,
            'tagArticles' => $tagArticles,
            'recommendCourseArticles' => $recommendCourseArticle,
            'interestedArticles' => $interestedArticleList,
            'newsArticles' => $newsArticleList
        ];
    }
}