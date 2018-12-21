<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-05 13:08
 */

namespace frontend\controllers;

use common\models\meta\ArticleMetaTag;
use yii;
use frontend\models\Article;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use common\models\Category;
use yii\helpers\ArrayHelper;
use yii\data\Pagination;

class SearchController extends Controller
{

    /**
     * 搜索
     *
     * @return string
     */
    public function actionIndex()
    {
        $where = ['type' => Article::ARTICLE];
        $query = Article::find()->select([])->where($where);
        $keyword = htmlspecialchars(yii::$app->getRequest()->get('q'));
        $query->andFilterWhere(['like', 'title', $keyword]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'sort' => SORT_ASC,
                    'id' => SORT_DESC,
                ]
            ]
        ]);
        return $this->render('/article/index', [
            'dataProvider' => $dataProvider,
            'type' => yii::t('frontend', 'Search keyword {keyword} results', ['keyword'=>$keyword]),
        ]);
    }

    public function actionTag($tag = '',$sort = 'news')
    {
        $metaTagModel = new ArticleMetaTag();
        $aids = $metaTagModel->getAidsByTag($tag);
        $where = ['status' => Article::ARTICLE_PUBLISHED,'type' => Article::ARTICLE];
        $orderBy = ['sort' => SORT_ASC, 'id' => SORT_DESC];
        if ($sort == 'news') {
            $orderBy = ['created_at' => SORT_DESC,'id' => SORT_DESC];
        } elseif ($sort == 'hot') {
            $orderBy = ['scan_count' => SORT_DESC,'id' => SORT_DESC];
        }

        $datas = Article::find()->select([])->where($where)->where(['in', 'id', $aids])->orderBy($orderBy);
        $pages = new Pagination(['totalCount' =>$datas->count(), 'defaultPageSize' => 10]);    //实例化分页类,带上参数(总条数,每页显示条数)
        $modules = $datas->offset($pages->offset)->limit($pages->limit)->all();

        //推荐课程(取3条)升序排序
        $courseCategory = Category::findOne(['alias' => 'course']);
        $courseDescendants = Category::getDescendants($courseCategory['id']);
        $courseCid = ArrayHelper::getColumn($courseDescendants, 'id');
        $courseCid[] = $courseCategory['id'];

        //常见问题分类
        $questionCategory = Category::findOne(['alias' => 'question']);
        $questionDescendants = Category::getDescendants($questionCategory['id']);
        $questionCid = ArrayHelper::getColumn($questionDescendants, 'id');
        $questionCid[] = $questionCategory['id'];

        //教程文档分类
        $wordCategory = Category::findOne(['alias' => 'word']);
        $wordDescendants = Category::getDescendants($wordCategory['id']);
        $wordCid = ArrayHelper::getColumn($wordDescendants, 'id');
        $wordCid[] = $wordCategory['id'];

        //行业资讯分类
        $informationCategory = Category::findOne(['alias' => 'information']);
        $informationDescendants = Category::getDescendants($informationCategory['id']);
        $informationCid = ArrayHelper::getColumn($informationDescendants, 'id');
        $informationCid[] = $informationCategory['id'];

        $hotCourseArticle = Article::find()->where(['status' => Article::ARTICLE_PUBLISHED,'cid' => $courseCid,'flag_recommend' => Article::HOT_YES])->orderBy(['sort' => SORT_ASC, 'created_at' => SORT_DESC, 'id' => SORT_DESC,])->limit(3)->all();
        $hotCourseArticles = $hotCourseArticle;
        //课程列表取20条最新发布的按照升序排序加时间排序
        //$newsTags = array_slice($modules,0,20);
        $newsTags = [];
        if (is_array($modules)) foreach ($modules as $module) {
            if (in_array($module->cid,$courseCid)) {
                $module->type = 'course';
            } elseif (in_array($module->cid,$wordCid)) {
                $module->type = 'word';
            } elseif (in_array($module->cid,$questionCid)) {
                $module->type = 'question';
            } elseif (in_array($module->cid,$informationCid)) {
                $module->type = 'information';
            }
            $newsTags[] = $module;
        }

        $wordImageTextDescendants = Article::find()->where(['status' => Article::ARTICLE_PUBLISHED,'cid' => $informationCid])->orderBy(['scan_count' => SORT_DESC, 'id' => SORT_DESC])->limit(13)->all();
        $data['newsTags'] = $newsTags;
        $data['hotCourseArticles'] = $hotCourseArticles;
        $data['wordImageTextDescendants'] = $wordImageTextDescendants;
        $data['tag'] = $tag;
        $data['sort'] = $sort;
        $data['pages'] = $pages;
        $data['type'] = yii::t('frontend', 'Tag {tag} related articles', ['tag'=>$tag]);
        Yii::$app->params['cat'] = 'tag/'.$tag;
        return $this->render('/article/tag', $data);
    }
}
