<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-02 22:48
 */

namespace frontend\controllers;

use yii;
use common\libs\Constants;
use frontend\models\form\ArticlePasswordForm;
use yii\base\Event;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use frontend\models\Article;
use frontend\models\Category;
use frontend\models\Comment;
use yii\data\ActiveDataProvider;
use common\models\meta\ArticleMetaLike;
use common\models\meta\ArticleMetaTag;
use yii\web\NotFoundHttpException;
use yii\filters\HttpCache;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\XmlResponseFormatter;
use common\helpers\FamilyTree;
use yii\data\Pagination;

class ArticleController extends Controller
{


    public function behaviors()
    {
        return [
            [
                'class' => HttpCache::className(),
                'only' => ['word','information','course','question'],
                'lastModified' => function ($action, $params) {
                    $id = yii::$app->getRequest()->get('id');
                    $model = Article::findOne(['id' => $id, 'type' => Article::ARTICLE, 'status' => Article::ARTICLE_PUBLISHED]);
                    if( $model === null ) throw new NotFoundHttpException(yii::t("frontend", "Article id {id} is not exists", ['id' => $id]));
                    Article::updateAllCounters(['scan_count' => 1], ['id' => $id]);
                    if($model->visibility == Constants::ARTICLE_VISIBILITY_PUBLIC) return $model->updated_at;
                },
            ],
        ];
    }

    /**
     * 分类列表页（导航条）
     *
     * @param string $cat 分类别名
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionIndex($cat = '',$sort = 'news',$cid = '')
    {
        if ($cat == '') {
            $cat = yii::$app->getRequest()->getPathInfo();
        }
        $where = ['type' => Article::ARTICLE, 'status' => Article::ARTICLE_PUBLISHED];
        $categoryAliasList = [];//分类别名列表
        $parents = [];//获取分类的所有祖先节点
        if ($cat != '' && $cat != 'index') {
            if ($cat == yii::t('app', 'uncategoried')) {
                $where['cid'] = 0;
            } else {
                if (! $category = Category::findOne(['alias' => $cat])) {
                    throw new NotFoundHttpException(yii::t('frontend', 'None category named {name}', ['name' => $cat]));
                }
                $descendants = Category::getDescendants($category['id']);
                if ( empty($descendants) ) {
                    $where['cid'] = $category['id'];
                } else {
                    $cids = ArrayHelper::getColumn($descendants, 'id');
                    $cids[] = $category['id'];
                    $where['cid'] = $cids;
                    $categoryAliasList = ArrayHelper::getColumn($descendants, 'alias');
                }
                $categoryAliasList[] = $category['alias'];
                if ($category['parent_id'] > 0) {
                    $parents = (new FamilyTree(Category::getCategories()))->getAncectors($category['id']);
                    array_multisort(array_column($parents, 'level'),SORT_ASC,$parents);
                    if (is_array($parents)) foreach ($parents as $parent) {
                        $categoryAliasList[] = $parent['alias'];
                    }
                }
            }
        }
        $orderBy = ['sort' => SORT_ASC, 'created_at' => SORT_DESC, 'id' => SORT_DESC];
        if ($sort ==  'news') {
            $orderBy = ['created_at' => SORT_DESC, 'id' => SORT_DESC];//最新排序
        } else if ($sort == 'hot') {
            $orderBy = ['scan_count' => SORT_DESC, 'id' => SORT_DESC];//最热排序（根据预览数）
        }
        if (in_array('word',$categoryAliasList)) {//教程文档
            $datas = Article::find()->where($where)->orderBy($orderBy);
            $pages = new Pagination(['totalCount' =>$datas->count(), 'defaultPageSize' => 10]);    //实例化分页类,带上参数(总条数,每页显示条数)
            $modules = $datas->offset($pages->offset)->limit($pages->limit)->all();

            Yii::$app->params['cat'] = '/word';
            $data = $this->getWord($cat,$modules,$parents,$descendants,'word');
            $data['category'] = $category;
            $data['sort'] = $sort;
            $data['pages'] = $pages;
            $data['type'] = ( !empty($cat) ? yii::t('frontend', 'Category {cat} articles', ['cat'=>$category->alias]) : yii::t('frontend', 'Latest Articles') );
            return $this->render('word', $data);
        } elseif (in_array('course',$categoryAliasList)) {//精品课程
            $datas = Article::find()->where($where)->orderBy($orderBy);
            $pages = new Pagination(['totalCount' =>$datas->count(), 'defaultPageSize' => 10]);    //实例化分页类,带上参数(总条数,每页显示条数)
            $modules = $datas->offset($pages->offset)->limit($pages->limit)->all();

            Yii::$app->params['cat'] = '/course';
            $data = $this->getCourse($cat,$modules,$parents,$descendants);
            $data['category'] = $category;
            $data['pages'] = $pages;
            $data['type'] = ( !empty($cat) ? yii::t('frontend', 'Category {cat} articles', ['cat'=>$category->alias]) : yii::t('frontend', 'Latest Articles') );
            return $this->render('course', $data);
        } elseif (in_array('question',$categoryAliasList)) {//常见问题
            $datas = Article::find()->where($where)->orderBy($orderBy);
            $pages = new Pagination(['totalCount' =>$datas->count(), 'defaultPageSize' => 10]);    //实例化分页类,带上参数(总条数,每页显示条数)
            $modules = $datas->offset($pages->offset)->limit($pages->limit)->all();

            $data = $this->getWord($cat,$modules,$parents,$descendants,'question');
            Yii::$app->params['cat'] = '/question';
            $data['category'] = $category;
            $data['pages'] = $pages;
            $data['sort'] = $sort;
            $data['type'] = ( !empty($cat) ? yii::t('frontend', 'Category {cat} articles', ['cat'=>$category->alias]) : yii::t('frontend', 'Latest Articles') );
            return $this->render('question', $data);
        } elseif (in_array('information',$categoryAliasList)) {//行业资讯
            $datas = Article::find()->where($where)->orderBy($orderBy);
            $pages = new Pagination(['totalCount' =>$datas->count(), 'defaultPageSize' => 10]);    //实例化分页类,带上参数(总条数,每页显示条数)
            $modules = $datas->offset($pages->offset)->limit($pages->limit)->all();

            $data = $this->getWord($cat,$modules,$parents,$descendants,'information');
            Yii::$app->params['cat'] = '/information';
            $data['category'] = $category;
            $data['sort'] = $sort;
            $data['pages'] = $pages;
            $data['type'] = ( !empty($cat) ? yii::t('frontend', 'Category {cat} articles', ['cat'=>$category->alias]) : yii::t('frontend', 'Latest Articles') );
            return $this->render('information', $data);
        } else { //首页
            //课程分类
            $courseCategory = Category::findOne(['alias' => 'course']);
            $courseDescendants = Category::getDescendants($courseCategory['id']);
            $courseCid = ArrayHelper::getColumn($courseDescendants, 'id');
            $courseCid[] = $courseCategory['id'];
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

            $where['flag_headline'] = Article::HEAD_LINE_YES;
            $modules = Article::find()->where(['not in','cid',$courseCid])->andWhere($where)->orderBy($orderBy)->limit(7)->all();
            $data = $this->getIndex();
            $data['type'] = ( !empty($cat) ? yii::t('frontend', 'Category {cat} articles', ['cat'=>$category->alias]) : yii::t('frontend', 'Latest Articles') );
            $data['cid'] = $cid;
            $headLines = [];
            if (is_array($modules)) foreach ($modules as $module) {
                if (in_array($module->cid,$courseCid)) {
                    $type = 'course';
                } elseif (in_array($module->cid,$wordCid)) {
                    $type = 'word';
                } elseif (in_array($module->cid,$questionCid)) {
                    $type = 'question';
                } elseif (in_array($module->cid,$informationCid)) {
                    $type = 'information';
                }
                $headLines[] = [
                    'id' => $module->id,
                    'title' => $module->title,
                    'type' => $type,
                ];
            }
            $data['headLines'] = $headLines;//IT百科7条记录（设置为头条的）
            Yii::$app->params['cat'] = '/';
            return $this->render('index', $data);
        }
    }

    /**
     * 教程文档详情页
     *
     * @param $id
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionWord($id)
    {
        //分类
        $category = Category::findOne(['alias' => 'word']);
        $Descendants = Category::getDescendants($category['id']);
        $cid = ArrayHelper::getColumn($Descendants, 'id');
        $cid[] = $category['id'];
        $model = Article::findOne(['id' => $id, 'type' => Article::ARTICLE, 'status' => Article::ARTICLE_PUBLISHED,'cid' => $cid]);
        if( $model === null ) throw new NotFoundHttpException(yii::t("frontend", "Article id {id} is not exists", ['id' => $id]));

        $data = $this->getArticle($id,$model);
        $data['model'] = $model;
        Yii::$app->params['cat'] = '/word';
        return $this->render('article', $data);
    }

    /**
     * 课程详情页
     *
     * @param $id
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionCourse($id)
    {
        $categoryId = Category::findOne(['alias' => 'course']);
        $categoryIds = Category::getDescendants($categoryId['id']);
        $categoryCid = ArrayHelper::getColumn($categoryIds, 'id');
        $categoryCid[] = $categoryId['id'];
        $model = Article::findOne(['id' => $id, 'type' => Article::ARTICLE, 'status' => Article::ARTICLE_PUBLISHED,'cid' => $categoryCid]);
        if( $model === null ) throw new NotFoundHttpException(yii::t("frontend", "Article id {id} is not exists", ['id' => $id]));

        //更多课程
        $courses = Article::find()->where(['status' => Article::ARTICLE_PUBLISHED,'cid' => $categoryCid])->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC])->limit(4)->all();
        //相关文章
        $tags = ArrayHelper::getValue($model,'articleTags');
        $tagsArray = ArrayHelper::getColumn($tags,'value');
        $tagAid = (new ArticleMetaTag())->getAidsByTag($tagsArray);
        $tagModules = Article::find()->where(['not in','cid',$categoryCid])->andWhere(['status' => Article::ARTICLE_PUBLISHED,'id' => $tagAid])->orderBy('created_at desc,id desc')->limit(4)->all();
        $tagArticles = [];
        //$tagArticles = array_slice($tagModules,0,4);
        //获取优秀学员作品(取4条)升序排序，取推荐状态的
        $studentProductCategory = Category::findOne(['alias' => 'word_student_product']);
        $studentProductDescendants = Category::getDescendants($studentProductCategory['id']);
        $studentProductCid = ArrayHelper::getColumn($studentProductDescendants, 'id');
        $studentProductCid[] = $studentProductCategory['id'];
        $studentProductArticles = Article::find()->where(['status' => Article::ARTICLE_PUBLISHED,'id' => $tagAid,'cid' => $studentProductCid,'flag_recommend' => Article::RECOMMEND_YES])->orderBy(['sort' => SORT_ASC, 'created_at' => SORT_DESC, 'id' => SORT_DESC,])->limit(4)->all();

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

        if (is_array($tagModules)) foreach ($tagModules as $tagModule) {
            if (in_array($tagModule->cid,$categoryCid)) {
                $tagModule->type = 'course';
            } elseif (in_array($tagModule->cid,$wordCid)) {
                $tagModule->type = 'word';
            } elseif (in_array($tagModule->cid,$questionCid)) {
                $tagModule->type = 'question';
            } elseif (in_array($tagModule->cid,$informationCid)) {
                $tagModule->type = 'information';
            }
            $tagArticles[] = $tagModule;
        }
        Yii::$app->params['cat'] = '/course';
        return $this->render('course-detail', [
            'model' => $model,
            'courses' => $courses,
            'tagArticles' => $tagArticles,
            'studentProductArticles' => $studentProductArticles,
        ]);
    }

    /**
     * 常见问题详情页
     *
     * @param $id
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionQuestion($id)
    {
        //分类
        $category = Category::findOne(['alias' => 'question']);
        $Descendants = Category::getDescendants($category['id']);
        $cid = ArrayHelper::getColumn($Descendants, 'id');
        $cid[] = $category['id'];
        $model = Article::findOne(['id' => $id, 'type' => Article::ARTICLE, 'status' => Article::ARTICLE_PUBLISHED,'cid' => $cid]);
        if( $model === null ) throw new NotFoundHttpException(yii::t("frontend", "Article id {id} is not exists", ['id' => $id]));
        $data = $this->getArticle($id,$model);
        $data['model'] = $model;
        Yii::$app->params['cat'] = '/question';
        return $this->render('article', $data);
    }

    /**
     * 行业资讯详情页
     *
     * @param $id
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionInformation($id)
    {
        //分类
        $category = Category::findOne(['alias' => 'information']);
        $Descendants = Category::getDescendants($category['id']);
        $cid = ArrayHelper::getColumn($Descendants, 'id');
        $cid[] = $category['id'];
        $model = Article::findOne(['id' => $id, 'type' => Article::ARTICLE, 'status' => Article::ARTICLE_PUBLISHED,'cid' => $cid]);
        if( $model === null ) throw new NotFoundHttpException(yii::t("frontend", "Article id {id} is not exists", ['id' => $id]));
        $data = $this->getArticle($id,$model);
        $data['model'] = $model;
        Yii::$app->params['cat'] = '/information';
        return $this->render('article', $data);

    }

    /**
     * 获取文章的点赞数和浏览数
     *
     * @param $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionViewAjax($id)
    {
        $model = Article::findOne($id);
        if( $model === null ) throw new NotFoundHttpException("None exists article id");
        return [
            'likeCount' => (int)$model->getArticleLikeCount(),
            'scanCount' => $model->scan_count * 100,
            'commentCount' => $model->comment_count,
        ];
    }

    /**
     * 评论
     *
     */
    public function actionComment()
    {
        if (yii::$app->getRequest()->getIsPost()) {
            $commentModel = new Comment();
            if ($commentModel->load(yii::$app->getRequest()->post()) && $commentModel->save()) {
                $avatar = 'https://secure.gravatar.com/avatar?s=50';
                if ($commentModel->email != '') {
                    $avatar = "https://secure.gravatar.com/avatar/" . md5($commentModel->email) . "?s=50";
                }
                $tips = '';
                if (yii::$app->feehi->website_comment_need_verify) {
                    $tips = "<span class='c-approved'>" . yii::t('frontend', 'Comment waiting for approved.') . "</span><br />";
                }
                $commentModel->afterFind();
                return "
                <li class='comment even thread-even depth-1' id='comment-{$commentModel->id}'>
                    <div class='c-avatar'><img src='{$avatar}' class='avatar avatar-108' height='50' width='50'>
                        <div class='c-main' id='div-comment-{$commentModel->id}'><p>{$commentModel->content}</p>
                            {$tips}
                            <div class='c-meta'><span class='c-author'><a href='{$commentModel->website_url}' rel='external nofollow' class='url'>{$commentModel->nickname}</a></span>  (" . yii::t('frontend', 'a minutes ago') . ")</div>
                        </div>
                    </div>";
            } else {
                $temp = $commentModel->getErrors();
                $str = '';
                foreach ($temp as $v) {
                    $str .= $v[0] . "<br>";
                }
                return "<font color='red'>" . $str . "</font>";
            }
        }
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionPassword($id)
    {
        $model = new ArticlePasswordForm();

        if ($model->load(Yii::$app->getRequest()->post()) && $model->checkPassword($id)) {
            return $this->redirect(Url::toRoute(['view', 'id'=>$id]));
        } else {
            return $this->render("password", [
                'model' => $model,
                'article' => Article::findOne($id),
            ]);
        }
    }

    /**
     * 点赞
     *
     * @return int|string
     */
    public function actionLike()
    {
        $aid = yii::$app->getRequest()->get("aid");
        $model = new ArticleMetaLike();
        $model->setLike($aid);
        return intval($model->getLikeCount($aid));

    }

    /**
     * rss订阅
     *
     * @return mixed
     */
    public function actionRss()
    {
        $xml['channel']['title'] = yii::$app->feehi->website_title;
        $xml['channel']['description'] = yii::$app->feehi->seo_description;
        $xml['channel']['lin'] = yii::$app->getUrlManager()->getHostInfo();
        $xml['channel']['generator'] = yii::$app->getUrlManager()->getHostInfo();
        $models = Article::find()->limit(10)->where(['status'=>Article::ARTICLE_PUBLISHED, 'type'=>Article::ARTICLE])->orderBy('id desc')->all();
        foreach ($models as $model){
            $xml['channel']['item'][] = [
                'title' => $model->title,
                'link' => Url::to(['article/view', 'id'=>$model->id]),
                'pubData' => date('Y-m-d H:i:s', $model->created_at),
                'source' => yii::$app->feehi->website_title,
                'author' => $model->author_name,
                'description' => $model->summary,
            ];
        }
        yii::configure(yii::$app->getResponse(), [
            'formatters' => [
                Response::FORMAT_XML => [
                    'class' => XmlResponseFormatter::className(),
                    'rootTag' => 'rss',
                    'version' => '1.0',
                    'encoding' => 'utf-8'
                ]
            ]
        ]);
        yii::$app->getResponse()->format = Response::FORMAT_XML;
        return $xml;
    }

    /**
     *  首页内容
     * @date 2018-09-19
     * @return []
     */
    public function getIndex()
    {
        //获取所有分类
         $categoryArrays = Category::find()->asArray()->all();
         $aliasId = [];
         if (is_array($categoryArrays)) foreach ($categoryArrays as $categoryArray) {
             $aliasId[$categoryArray['alias']] = $categoryArray['id'];
         }
        //热门课程分类(取20条)倒序排序
        $courseDescendants = Category::getDescendants(ArrayHelper::getValue($aliasId,'course'));
        array_multisort(array_column($courseDescendants, 'sort'),SORT_DESC,$courseDescendants);
        $hotCourses = array_slice($courseDescendants,0,20);
        //课程分类id
        $courseCids = ArrayHelper::getColumn($courseDescendants, 'id');
        $courseCids[] = ArrayHelper::getValue($aliasId,'course');

        //热门教程(取6条)倒序排序
        $wordDescendants = Category::getDescendants(ArrayHelper::getValue($aliasId,'word'));
        array_multisort(array_column($wordDescendants, 'sort'),SORT_DESC,$wordDescendants);
        $hotWords = array_slice($wordDescendants,0,6);
        $hotWordCids = ArrayHelper::getColumn($hotWords, 'id');
        $hotWordArticleAlls = Article::find()->where(['status' => Article::ARTICLE_PUBLISHED,'cid' => $hotWordCids,'flag_special_recommend' => Article::HOT_YES])->orderBy('sort ASC,id DESC')->all();
        //教程文档分类id
        $wordCids = ArrayHelper::getColumn($wordDescendants, 'id');
        //获取图文教程(取13条)升序排序
        $wordImageTextDescendants = Category::getDescendants(ArrayHelper::getValue($aliasId,'word_image_text'));
        $wordImageTextCids = ArrayHelper::getColumn($wordImageTextDescendants, 'id');
        $wordImageTextCids[] = ArrayHelper::getValue($aliasId,'word_image_text');

        //获取优秀学员感言(取3条)升序排序，取推荐状态的
        $wordStudentFeelingDescendants = Category::getDescendants(ArrayHelper::getValue($aliasId,'word_student_feeling'));
        $wordStudentFeelingCids = ArrayHelper::getColumn($wordStudentFeelingDescendants, 'id');
        $wordStudentFeelingCids[] = ArrayHelper::getValue($aliasId,'word_student_feeling');
        $wordStudentFeelingArticles = Article::find()->where(['status' => Article::ARTICLE_PUBLISHED,'cid' => $wordStudentFeelingCids,'flag_recommend' => Article::RECOMMEND_YES])->orderBy('sort ASC,id DESC')->limit(3)->all();

        //获取优秀学员作品(取6条)升序排序，取推荐状态的
        $wordStudentProductDescendants = Category::getDescendants(ArrayHelper::getValue($aliasId,'word_student_product'));
        $wordStudentProductCids = ArrayHelper::getColumn($wordStudentProductDescendants, 'id');
        $wordStudentProductCids[] = ArrayHelper::getValue($aliasId,'word_student_product');
        $wordStudentProductArticles = Article::find()->where(['status' => Article::ARTICLE_PUBLISHED,'cid' => $wordStudentProductCids,'flag_recommend' => Article::RECOMMEND_YES])->orderBy('sort ASC,id DESC')->limit(6)->all();

        $wordCidList = array_diff($wordCids,$wordStudentFeelingCids,$wordStudentProductCids);//去掉优秀学员作品和学员感言
        //获取推荐教程
        $recommendWordArticles = Article::find()->where(['status' => Article::ARTICLE_PUBLISHED,'cid' => $wordCidList,'flag_recommend' => Article::RECOMMEND_YES])->orderBy('sort ASC,id DESC')->limit(6)->all();


        /*//获取参考文档(取10条)升序排序
        $wordConsultDescendants = Category::getDescendants(ArrayHelper::getValue($aliasId,'word_consult'));
        $wordConsultCids = ArrayHelper::getColumn($wordConsultDescendants, 'id');
        $wordConsultCids[] = ArrayHelper::getValue($aliasId,'word_consult');
        $wordConsultArticles = Article::find()->where(['status' => Article::ARTICLE_PUBLISHED,'cid' => $wordConsultCids])->orderBy('sort ASC,id DESC')->limit(10)->all();


        //获取就业指导取10条)升序排序
        $wordJobDescendants = Category::getDescendants(ArrayHelper::getValue($aliasId,'word_job'));
        $wordJobCids = ArrayHelper::getColumn($wordJobDescendants, 'id');
        $wordJobCids[] = ArrayHelper::getValue($aliasId,'word_job');
        $wordJobArticles = Article::find()->where(['status' => Article::ARTICLE_PUBLISHED,'cid' => $wordJobCids])->orderBy('sort ASC,id DESC')->limit(10)->all();


        //获取技术干货(取10条)升序排序
        $wordFoodDescendants = Category::getDescendants(ArrayHelper::getValue($aliasId,'word_food'));
        $wordFoodCids = ArrayHelper::getColumn($wordFoodDescendants, 'id');
        $wordFoodCids[] = ArrayHelper::getValue($aliasId,'word_food');
        $wordFoodArticles = Article::find()->where(['status' => Article::ARTICLE_PUBLISHED,'cid' => $wordFoodCids])->orderBy('sort ASC,id DESC')->limit(10)->all();*/


        //获取行业资讯(取20条)升序排序
        $informationDescendants = Category::getDescendants(ArrayHelper::getValue($aliasId,'information'));
        $informationCids = ArrayHelper::getColumn($informationDescendants, 'id');
        $informationCids[] = ArrayHelper::getValue($aliasId,'information');
        $informationArticles = Article::find()->where(['status' => Article::ARTICLE_PUBLISHED,'cid' => $informationCids])->orderBy('sort ASC,id DESC')->limit(20)->all();

        //获取常见问题取20条)升序排序
        $questionDescendants = Category::getDescendants(ArrayHelper::getValue($aliasId,'question'));
        $questionCids = ArrayHelper::getColumn($questionDescendants, 'id');
        $questionCids[] = ArrayHelper::getValue($aliasId,'question');
        $questionArticles = Article::find()->where(['status' => Article::ARTICLE_PUBLISHED,'cid' => $questionCids])->orderBy('sort ASC,id DESC')->limit(20)->all();

        //热门课程(取4条)升序排序
        $hotCourseArticle = Article::find()->where(['status' => Article::ARTICLE_PUBLISHED,'cid' => $courseCids,'flag_special_recommend' => Article::HOT_YES])->orderBy('sort ASC,id DESC')->limit(4)->all();
        $hotCourseArticles = $hotCourseArticle;
        //热门教程(每个分类取15条)升序排序
        $hotWordArticle = [];
        if (is_array($hotWordArticleAlls)) foreach ($hotWordArticleAlls as $hotWordArticleAll) {
            $hotWordArticle[ArrayHelper::getValue($hotWordArticleAll,'cid')][] = $hotWordArticleAll;
        }
        $hotWordName = [];
        $hotWordArticles = [];
        if (is_array($hotWords)) foreach ($hotWords as $hotWord) {
            $hotWordName[ArrayHelper::getValue($hotWord,'id')] = ArrayHelper::getValue($hotWord,'name');
            if (ArrayHelper::getValue($hotWordArticle,ArrayHelper::getValue($hotWord,'id'))) {
                $hotWordArticles[ArrayHelper::getValue($hotWord,'id')] = array_slice(ArrayHelper::getValue($hotWordArticle,ArrayHelper::getValue($hotWord,'id')),0,15);
            }
        }
        //获取图文教程(取13条)升序排序
        $hotWordImageTextArticle = Article::find()->where(['status' => Article::ARTICLE_PUBLISHED,'cid' => $wordImageTextCids])->orderBy('sort ASC,id DESC')->limit(13)->all();
        $hotWordImageTextArticles = $hotWordImageTextArticle;

        //获取推荐课程
        $recommendCourseArticle = [];
        $recommendCourseArticleAlls = Article::find()->where(['status' => Article::ARTICLE_PUBLISHED,'cid' => $courseCids,'flag_recommend' => Article::RECOMMEND_YES])->orderBy('sort ASC,id DESC')->all();
        if (is_array($recommendCourseArticleAlls)) foreach ($recommendCourseArticleAlls as $recommendCourseArticleAll) {
            $recommendCourseArticle[ArrayHelper::getValue($recommendCourseArticleAll,'cid')][] = $recommendCourseArticleAll;
        }
        $recommendCourseName = [];
        $recommendCourseArticles = [];
        if (is_array($courseDescendants)) foreach ($courseDescendants as $courseDescendant) {
            $recommendCourseName[ArrayHelper::getValue($courseDescendant,'id')]['name'] = ArrayHelper::getValue($courseDescendant,'name');
            $recommendCourseName[ArrayHelper::getValue($courseDescendant,'id')]['alias'] = ArrayHelper::getValue($courseDescendant,'alias');
            if (ArrayHelper::getValue($recommendCourseArticle,ArrayHelper::getValue($courseDescendant,'id'))) {
                $recommendCourseArticles[ArrayHelper::getValue($courseDescendant,'id')] = array_slice(ArrayHelper::getValue($recommendCourseArticle,ArrayHelper::getValue($courseDescendant,'id')),0,6);
            }
        }

        //轮播图片
        $ad = Article::getAd(2,'index');
        $adLists = json_decode($ad[0]->value,true);
        array_multisort(array_column($adLists, 'sort'),SORT_ASC,$adLists);
        return [
            'hotCourses' => $hotCourses,
            'hotWordName' => $hotWordName,
            'recommendCourseName' => $recommendCourseName,
            'hotCourseArticles' => $hotCourseArticles,
            'hotWordArticles' => $hotWordArticles,
            'hotWordImageTextArticles' => $hotWordImageTextArticles,
            'recommendCourseArticles' => $recommendCourseArticles,
            'recommendWordArticles' => $recommendWordArticles,
            'wordStudentFeelingArticles' => $wordStudentFeelingArticles,
            'wordStudentProductArticles' => $wordStudentProductArticles,
            /*'wordConsultArticles' => $wordConsultArticles,
            'wordJobArticles' => $wordJobArticles,
            'wordFoodArticles' => $wordFoodArticles,*/
            'questionArticles' => $questionArticles,
            'informationArticles' => $informationArticles,
            'adLists' => $adLists,
        ];
    }

    /**
     *  课程内容
     * @date 2018-09-19
     * @return []
     */
    public function getCourse($cat,$modules,$parents,$descendants)
    {
        if (empty($parents)) {
            $parents[] = ['level' => 1,'alias' => 'course'];
        }
        if ($cat == 'course') {
            $allCategoryCourses = $descendants;
        } else {
            $categoryCourse = Category::findOne(['alias' => 'course']);
            $allCategoryCourses = Category::getDescendants($categoryCourse['id']);
        }
        //推荐课程(取4条)升序排序
        $hotCategory = Category::findOne(['alias' => 'course']);
        $hotDescendants = Category::getDescendants($hotCategory['id']);
        $hotCid = ArrayHelper::getColumn($hotDescendants, 'id');
        $hotCid[] = $hotCategory['id'];
        $hotCourseArticle = Article::find()->where(['status' => Article::ARTICLE_PUBLISHED,'cid' => $hotCid,'flag_recommend' => Article::HOT_YES])->orderBy(['sort' => SORT_ASC, 'created_at' => SORT_DESC, 'id' => SORT_DESC,])->limit(4)->all();
        $hotCourseArticles = $hotCourseArticle;
        //课程列表取6条最新发布的按照升序排序加时间排序
        $newsCourses = array_slice($modules,0,13);

        //热门资讯
        $wordCategory = Category::findOne(['alias' => 'information']);
        $wordDescendants = Category::getDescendants($wordCategory['id']);
        $wordCid = ArrayHelper::getColumn($wordDescendants, 'id');
        $wordCid[] = $wordCategory['id'];
        $wordImageTextDescendants = Article::find()->where(['status' => Article::ARTICLE_PUBLISHED,'cid' => $wordCid])->orderBy(['scan_count' => SORT_DESC, 'id' => SORT_DESC,])->limit(13)->all();
        $courseCategoryId = Category::findOne(['alias' => $cat]);
        $allDescendant = [];
        $lastDescendant = [];
        foreach ($hotDescendants as $descendant) {
            if ($descendant['parent_id'] == ArrayHelper::getValue($courseCategoryId,'id')) {
                $allDescendant[] = $descendant;
            }
            if ($descendant['parent_id'] == ArrayHelper::getValue($courseCategoryId,'parent_id')) {
                $lastDescendant[] = $descendant;
            }
        }
        if (empty($allDescendant)) {
            $allDescendant = $lastDescendant;
        }
        //H5菜单
        $AllCategorys = Category::getCategories();
        $categoryList = [];
        $oneCategory = [];
        $twoCategory = [];
        $threeCategory = [];
        $fourCategory = [];
        foreach ($AllCategorys as $AllCategory) {
            $categoryList[ArrayHelper::getValue($AllCategory,'id')]['parent_id'] = ArrayHelper::getValue($AllCategory,'parent_id');
            $categoryList[ArrayHelper::getValue($AllCategory,'id')]['level'] = ArrayHelper::getValue($AllCategory,'level');
            $categoryList[ArrayHelper::getValue($AllCategory,'id')]['name'] = ArrayHelper::getValue($AllCategory,'name');
            $categoryList[ArrayHelper::getValue($AllCategory,'id')]['alias'] = ArrayHelper::getValue($AllCategory,'alias');
            if (ArrayHelper::getValue($AllCategory,'parent_id') == 0 && ArrayHelper::getValue($AllCategory,'level') == 1) {
                $oneCategory[ArrayHelper::getValue($AllCategory,'alias')] = ArrayHelper::getValue($AllCategory,'id');
            }
            if (ArrayHelper::getValue($AllCategory,'parent_id') > 0) {
                if (ArrayHelper::getValue($AllCategory,'level') == 2) {
                    $twoCategory[ArrayHelper::getValue($AllCategory,'parent_id')][] = ArrayHelper::getValue($AllCategory,'id');
                } elseif (ArrayHelper::getValue($AllCategory,'level') == 3) {
                    $threeCategory[ArrayHelper::getValue($AllCategory,'parent_id')][] = ArrayHelper::getValue($AllCategory,'id');
                } elseif (ArrayHelper::getValue($AllCategory,'level') == 4) {
                    $fourCategory[ArrayHelper::getValue($AllCategory,'parent_id')][] = ArrayHelper::getValue($AllCategory,'id');
                }
            }
        }

        return [
            'newsCourses' => $newsCourses,
            'hotCourseArticles' => $hotCourseArticles,
            'wordImageTextDescendants' => $wordImageTextDescendants,
            'parents' => $parents,
            'branchCourses' => $allDescendant,
            'allCategoryCourses' => $allCategoryCourses,
            'oneCategory' => $oneCategory,
            'twoCategory' => $twoCategory,
            'threeCategory' => $threeCategory,
            'fourCategory' => $fourCategory,
            'categoryList' => $categoryList,
        ];
    }

    /**
     *  文章内容（教程文档、行业资讯、常见问题）
     * @date 2018-09-19
     * @return []
     */
    public function getWord($cat,$modules,$parents,$descendants,$aliasType)
    {
        if (empty($parents)) {
            $parents[] = ['level' => 1,'alias' => 'course'];
        }
        if ($cat == 'course') {
            $allCategoryCourses = $descendants;
        } else {
            $categoryCourse = Category::findOne(['alias' => 'course']);
            $allCategoryCourses = Category::getDescendants($categoryCourse['id']);
        }
        //推荐课程(取4条)升序排序
        $hotCategory = Category::findOne(['alias' => 'course']);
        $hotDescendants = Category::getDescendants($hotCategory['id']);
        $hotCid = ArrayHelper::getColumn($hotDescendants, 'id');
        $hotCid[] = $hotCategory['id'];
        $hotCourseArticle = Article::find()->where(['status' => Article::ARTICLE_PUBLISHED,'cid' => $hotCid,'flag_recommend' => Article::HOT_YES])->orderBy(['sort' => SORT_ASC, 'created_at' => SORT_DESC, 'id' => SORT_DESC])->limit(4)->all();
        //$hotCourseArticles = array_slice($hotCourseArticle,0,3);
        $hotCourseArticles = $hotCourseArticle;
        //课程列表取6条最新发布的按照升序排序加时间排序
        //$newsWords = array_slice($modules,0,13);
        $newsWords = $modules;

        //热门资讯
        $wordCategory = Category::findOne(['alias' => 'information']);
        $wordDescendants = Category::getDescendants($wordCategory['id']);
        $wordCid = ArrayHelper::getColumn($wordDescendants, 'id');
        $wordCid[] = $wordCategory['id'];
        $wordImageTextDescendants = Article::find()->where(['status' => Article::ARTICLE_PUBLISHED,'cid' => $wordCid])->orderBy(['scan_count' => SORT_DESC, 'id' => SORT_DESC])->limit(13)->all();
        $courseCategoryId = Category::findOne(['alias' => $cat]);
        //获取大类下的所有分类
        $aliasCategory = Category::findOne(['alias' => $aliasType]);
        $aliasDescendants = Category::getDescendants($aliasCategory['id']);
        $allDescendant = [];
        $lastDescendant = [];
        foreach ($aliasDescendants as $descendant) {
            if ($descendant['parent_id'] == ArrayHelper::getValue($courseCategoryId,'id')) {
                $allDescendant[] = $descendant;
            }
            if ($descendant['parent_id'] == ArrayHelper::getValue($courseCategoryId,'parent_id')) {
                $lastDescendant[] = $descendant;
            }
        }
        if (empty($allDescendant)) {
            $allDescendant = $lastDescendant;
        }
        //H5菜单
        $AllCategorys = Category::getCategories();
        $categoryList = [];
        $oneCategory = [];
        $twoCategory = [];
        $threeCategory = [];
        $fourCategory = [];
        foreach ($AllCategorys as $AllCategory) {
            $categoryList[ArrayHelper::getValue($AllCategory,'id')]['parent_id'] = ArrayHelper::getValue($AllCategory,'parent_id');
            $categoryList[ArrayHelper::getValue($AllCategory,'id')]['level'] = ArrayHelper::getValue($AllCategory,'level');
            $categoryList[ArrayHelper::getValue($AllCategory,'id')]['name'] = ArrayHelper::getValue($AllCategory,'name');
            $categoryList[ArrayHelper::getValue($AllCategory,'id')]['alias'] = ArrayHelper::getValue($AllCategory,'alias');
            if (ArrayHelper::getValue($AllCategory,'parent_id') == 0 && ArrayHelper::getValue($AllCategory,'level') == 1) {
                $oneCategory[ArrayHelper::getValue($AllCategory,'alias')] = ArrayHelper::getValue($AllCategory,'id');
            }
            if (ArrayHelper::getValue($AllCategory,'parent_id') > 0) {
                if (ArrayHelper::getValue($AllCategory,'level') == 2) {
                    $twoCategory[ArrayHelper::getValue($AllCategory,'parent_id')][] = ArrayHelper::getValue($AllCategory,'id');
                } elseif (ArrayHelper::getValue($AllCategory,'level') == 3) {
                    $threeCategory[ArrayHelper::getValue($AllCategory,'parent_id')][] = ArrayHelper::getValue($AllCategory,'id');
                } elseif (ArrayHelper::getValue($AllCategory,'level') == 4) {
                    $fourCategory[ArrayHelper::getValue($AllCategory,'parent_id')][] = ArrayHelper::getValue($AllCategory,'id');
                }
            }
        }
        return [
            'newsWords' => $newsWords,
            'hotCourseArticles' => $hotCourseArticles,
            'wordImageTextDescendants' => $wordImageTextDescendants,
            'parents' => $parents,
            'branchCourses' => $allDescendant,
            'allCategoryCourses' => $allCategoryCourses,
            'oneCategory' => $oneCategory,
            'twoCategory' => $twoCategory,
            'threeCategory' => $threeCategory,
            'fourCategory' => $fourCategory,
            'categoryList' => $categoryList,
        ];
    }
    /**
     *  文章内容页
     * @date 2018-09-20
     * @return []
     */
    public function getArticle($id,$model)
    {
        $prev = Article::find()
            ->where(['status' => Article::ARTICLE_PUBLISHED,'cid' => $model->cid])
            ->andWhere(['>', 'id', $id])
            ->orderBy("sort asc,created_at desc,id desc")
            ->limit(1)
            ->one();
        $next = Article::find()
            ->where(['status' => Article::ARTICLE_PUBLISHED,'cid' => $model->cid])
            ->andWhere(['<', 'id', $id])
            ->orderBy("sort desc,created_at desc,id asc")
            ->limit(1)
            ->one();//->createCommand()->getRawSql();
        $categoryId = Category::findOne(['alias' => 'course']);
        $categoryIds = Category::getDescendants($categoryId['id']);
        $categoryCid = ArrayHelper::getColumn($categoryIds, 'id');
        $categoryCid[] = $categoryId['id'];
        //相关文章
        $tags = ArrayHelper::getValue($model,'articleTags');
        $tagsArray = ArrayHelper::getColumn($tags,'value');
        $tagAid = (new ArticleMetaTag())->getAidsByTag($tagsArray);
        if (is_array($tagAid)) {
            $tagAid = array_unique($tagAid);
        }
        $tagAid = array_merge(array_diff($tagAid, array($id)));//去掉自己的id
        $tagModules = Article::find()->where(['not in','cid',$categoryCid])->andWhere(['status' => Article::ARTICLE_PUBLISHED,'id' => $tagAid])->orderBy('created_at desc,id desc')->all();
        $tagArticles = [];
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

        //推荐课程(取4条)升序排序
        $recommendCategory = Category::findOne(['alias' => 'course']);
        $recommendDescendants = Category::getDescendants($recommendCategory['id']);
        $recommendCid = ArrayHelper::getColumn($recommendDescendants, 'id');
        $recommendCid[] = $recommendCategory['id'];
        $recommendCourseArticle = Article::find()->where(['status' => Article::ARTICLE_PUBLISHED,'cid' => $recommendCid,'flag_recommend' => Article::HOT_YES])->orderBy(['sort' => SORT_ASC, 'created_at' => SORT_DESC, 'id' => SORT_DESC])->limit(4)->all();
        //$recommendCourseArticles = array_slice($recommendCourseArticle,0,2);
        //大家感兴趣的内容取13条
        $interestedArticles = Article::find()->where(['status' => Article::ARTICLE_PUBLISHED])->orderBy(['scan_count' => SORT_DESC])->limit(13)->all();
        $interestedArticleList = [];
        if (is_array($interestedArticles)) foreach ($interestedArticles as $interestedArticle) {
            if (in_array($interestedArticle->cid,$recommendCid)) {
                $interestedArticle->type = 'course';
            } elseif (in_array($interestedArticle->cid,$wordCid)) {
                $interestedArticle->type = 'word';
            } elseif (in_array($interestedArticle->cid,$questionCid)) {
                $interestedArticle->type = 'question';
            } elseif (in_array($interestedArticle->cid,$informationCid)) {
                $interestedArticle->type = 'information';
            }
            $interestedArticleList[] = $interestedArticle;
        }
        //最近更新的内容取13条
        $newsArticles = Article::find()->where(['status' => Article::ARTICLE_PUBLISHED])->orderBy(['created_at' => SORT_DESC,'id' => SORT_DESC])->limit(13)->all();
        $newsArticleList = [];
        if (is_array($newsArticles)) foreach ($newsArticles as $newsArticle) {
            if (in_array($newsArticle->cid,$recommendCid)) {
                $newsArticle->type = 'course';
            } elseif (in_array($newsArticle->cid,$wordCid)) {
                $newsArticle->type = 'word';
            } elseif (in_array($newsArticle->cid,$questionCid)) {
                $newsArticle->type = 'question';
            } elseif (in_array($newsArticle->cid,$informationCid)) {
                $newsArticle->type = 'information';
            }
            $newsArticleList[] = $newsArticle;
        }

        if (is_array($tagModules)) foreach ($tagModules as $tagModule) {
            if (in_array($tagModule->cid,$recommendCid)) {
                $tagModule->type = 'course';
            } elseif (in_array($tagModule->cid,$wordCid)) {
                $tagModule->type = 'word';
            } elseif (in_array($tagModule->cid,$questionCid)) {
                $tagModule->type = 'question';
            } elseif (in_array($tagModule->cid,$informationCid)) {
                $tagModule->type = 'information';
            }
            $tagArticles[] = $tagModule;
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

    /**
     *  课程内容页
     * @date 2018-09-20
     * @return []
     */
    public function getCourseDetail()
    {
        return [

        ];
    }
}