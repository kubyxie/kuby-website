<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-23 12:08
 */

namespace backend\controllers;

use yii;
use backend\models\form\SettingWebsiteForm;
use backend\models\form\SettingSmtpForm;
use common\models\Options;
use common\libs\Constants;
use yii\base\Model;
use yii\web\Response;
use backend\actions\DeleteAction;
use backend\widgets\ActiveForm;
use backend\models\Article;
use yii\swiftmailer\Mailer;
use yii\web\BadRequestHttpException;
use yii\web\UnprocessableEntityHttpException;
use backend\models\Upload;
use yii\web\UploadedFile;
use yii\helpers\Url;
use yii\web\XmlResponseFormatter;
use yii\helpers\ArrayHelper;
use common\models\Category;

/**
 * Setting controller
 */
class SettingController extends \yii\web\Controller
{

    public function actions()
    {
        return [
            "delete" => [
                "class" => DeleteAction::className(),
                "modelClass" => Options::className(),
            ]
        ];
    }

    /**
     * 网站设置
     *
     * @return string
     */
    public function actionWebsite()
    {
        $model = new SettingWebsiteForm();
        if (yii::$app->getRequest()->getIsPost()) {
            if ($model->load(yii::$app->getRequest()->post()) && $model->validate() && $model->setWebsiteConfig()) {
                yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0] . '<br>';
                }
                yii::$app->getSession()->setFlash('error', $err);
            }
        }

        $model->getWebsiteSetting();
        return $this->render('website', [
            'model' => $model
        ]);

    }

    /**
     * 自定义设置
     *
     * @return string
     */
    public function actionCustom()
    {
        $settings = Options::find()->where(['type' => Options::TYPE_CUSTOM])->orderBy("sort")->indexBy('id')->all();

        if (Model::loadMultiple($settings, yii::$app->request->post()) && Model::validateMultiple($settings)) {
            foreach ($settings as $setting) {
                $setting->save(false);
            }
            yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
        }
        $options = new Options();
        $options->loadDefaultValues();

        return $this->render('custom', [
            'settings' => $settings,
            'model' => $options,
        ]);
    }

    /**
     * 增加自定义设置项
     *
     * @return array
     * @throws UnprocessableEntityHttpException
     */
    public function actionCustomCreate()
    {
        $model = new Options();
        $model->type = Options::TYPE_CUSTOM;
        if ($model->load(yii::$app->getRequest()->post()) && $model->save()) {
            yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
            return [];
        } else {
            $errors = $model->getErrors();
            $err = '';
            foreach ($errors as $v) {
                $err .= $v[0] . '<br>';
            }
            yii::$app->getResponse()->format = Response::FORMAT_JSON;
            throw new UnprocessableEntityHttpException($err);
        }
    }

    /**
     * 修改自定义设置项
     *
     * @param string $id
     * @return array
     * @throws UnprocessableEntityHttpException
     */
    public function actionCustomUpdate($id = '')
    {
        $model = Options::findOne(['id' => $id]);
        if (yii::$app->getRequest()->getIsPost()) {
            if ($model->load(yii::$app->getRequest()->post()) && $model->save()) {
                yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
                return [];
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0] . '<br>';
                }
                yii::$app->getResponse()->format = Response::FORMAT_JSON;
                throw new UnprocessableEntityHttpException($err);
            }
        } else {
            yii::$app->getResponse()->format = Response::FORMAT_HTML;
            echo '<div class="" id="editForm">';
            echo '<div class="ibox-content">';
            $form = ActiveForm::begin(['options' => ['name' => 'edit']]);
            echo $form->field($model, 'name')->textInput();
            echo $form->field($model, 'input_type')->dropDownList(Constants::getInputTypeItems());
            echo $form->field($model, 'tips')->textInput();
            echo $form->field($model, 'autoload')->dropDownList(Constants::getYesNoItems());
            echo $form->field($model, 'value')->textInput();
            echo $form->field($model, 'sort')->textInput();
            echo $form->defaultButtons();
            ActiveForm::end();
            echo '</div>';
            echo '</div>';
        }
    }

    /**
     * 邮件smtp设置
     *
     * @return string
     */
    public function actionSmtp()
    {
        $model = new SettingSmtpForm();
        if (yii::$app->getRequest()->getIsPost()) {
            if ($model->load(yii::$app->getRequest()->post()) && $model->validate() && $model->setSmtpConfig()) {
                yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= $v[0] . '<br>';
                }
                yii::$app->getSession()->setFlash('error', $err);
            }
        }

        $model->getSmtpConfig();
        return $this->render('smtp', [
            'model' => $model
        ]);

    }

    /**
     * 发送测试邮件确认smtp设置是否正确
     *
     * @return mixed
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionTestSmtp()
    {
        $model = new SettingSmtpForm();
        yii::$app->getResponse()->format = Response::FORMAT_JSON;
        if ($model->load(yii::$app->getRequest()->post()) && $model->validate()) {
            $mailer = yii::createObject([
                'class' => Mailer::className(),
                'useFileTransport' => false,
                'transport' => [
                    'class' => 'Swift_SmtpTransport',
                    'host' => $model->smtp_host,
                    'username' => $model->smtp_username,
                    'password' => $model->smtp_password,
                    'port' => $model->smtp_port,
                    'encryption' => $model->smtp_encryption,

                ],
                'messageConfig' => [
                    'charset' => 'UTF-8',
                    'from' => [$model->smtp_username => $model->smtp_nickname]
                ],
            ]);
            return $mailer->compose()
                ->setFrom($model->smtp_username)
                ->setTo($model->smtp_username)
                ->setSubject('Email SMTP test ' . yii::$app->name)
                ->setTextBody('Email SMTP config works successful')
                ->send();
        } else {
            $error = '';
            foreach ($model->getErrors() as $item) {
                $error .= $item[0] . "<br/>";
            }
            throw new BadRequestHttpException( $error );
        }
    }

    /**
     * 网站地图
     *
     * @return string
     */
    public function actionMap()
    {
        $model = new Upload();

        if (Yii::$app->request->isPost) {
            $model->xmlFile = UploadedFile::getInstance($model, 'xmlFile');
            if ($model->upload()) {
                // 文件上传成功
                return '文件上传成功';
            }
        }

        return $this->render('map', ['model' => $model]);

    }

    /**
     * sitemap.xml生成
     *
     * @return mixed
     */
    public function actionCreateSitemap()
    {
        //课程分类
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

        $site = Yii::$app->params['web']['url'];

        $dom = new \DOMDocument('1.0','utf-8');
        $dom->formatOutput = true;
        $object = $dom->createElement('urlset');
        $dom->appendChild($object);
        $type1 = $dom->createAttribute('xmlns');
        $object->appendChild($type1);
        $type1_value = $dom->createTextNode('http://www.sitemaps.org/schemas/sitemap/0.9');
        $type1->appendChild($type1_value);
        $type2 = $dom->createAttribute('xmlns:mobile');
        $object->appendChild($type2);
        $type2_value = $dom->createTextNode('http://www.baidu.com/schemas/sitemap-mobile/1/');
        $type2->appendChild($type2_value);
        //首页
        $item = $dom->createElement('url');
        $object->appendChild($item);
        $itemMobile = $dom->createElement('mobile:mobile');
        $item->appendChild($itemMobile);
        $itemMobileType = $dom->createAttribute('type');
        $itemMobile->appendChild($itemMobileType);
        $itemMobileTypeValue = $dom->createTextNode('pc,mobile');
        $itemMobileType->appendChild($itemMobileTypeValue);

        $itemLoc = $dom->createElement('loc');
        $item->appendChild($itemLoc);
        $itemPriority = $dom->createElement('priority');
        $item->appendChild($itemPriority);
        $itemLastmod = $dom->createElement('lastmod');
        $item->appendChild($itemLastmod);
        $itemChangefreq = $dom->createElement('changefreq');
        $item->appendChild($itemChangefreq);
        $LocValue = $dom->createTextNode($site);
        $itemLoc->appendChild($LocValue);
        $priorityValue = $dom->createTextNode(1.0);
        $itemPriority->appendChild($priorityValue);
        $lastmodValue = $dom->createTextNode(date('Y-m-d',time()));
        $itemLastmod->appendChild($lastmodValue);
        $changefreqValue = $dom->createTextNode('Daily');
        $itemChangefreq->appendChild($changefreqValue);
        //分类
        $CategoryModels = Category::find()->orderBy('id desc')->all();
        foreach ($CategoryModels as $CategoryModel) {
            $locUrl = $site.'/'.$CategoryModel->alias;
            $item = $dom->createElement('url');
            $object->appendChild($item);
            $itemMobile = $dom->createElement('mobile:mobile');
            $item->appendChild($itemMobile);
            $itemMobileType = $dom->createAttribute('type');
            $itemMobile->appendChild($itemMobileType);
            $itemMobileTypeValue = $dom->createTextNode('pc,mobile');
            $itemMobileType->appendChild($itemMobileTypeValue);

            $itemLoc = $dom->createElement('loc');
            $item->appendChild($itemLoc);
            $itemPriority = $dom->createElement('priority');
            $item->appendChild($itemPriority);
            $itemLastmod = $dom->createElement('lastmod');
            $item->appendChild($itemLastmod);
            $itemChangefreq = $dom->createElement('changefreq');
            $item->appendChild($itemChangefreq);
            $LocValue = $dom->createTextNode($locUrl);
            $itemLoc->appendChild($LocValue);
            $priorityValue = $dom->createTextNode(0.7);
            $itemPriority->appendChild($priorityValue);
            $lastmodValue = $dom->createTextNode(date('Y-m-d',time()));
            $itemLastmod->appendChild($lastmodValue);
            $changefreqValue = $dom->createTextNode('Daily');
            $itemChangefreq->appendChild($changefreqValue);
        }
        //文章
        $models = Article::find()->where(['status'=>Article::ARTICLE_PUBLISHED])->orderBy('id desc')->all();
        foreach ($models as $model){
            $locUrl = '';
            if ($model->type == Article::ARTICLE) {
                if (in_array($model->cid,$courseCid)) {
                    $type = 'course';
                } elseif (in_array($model->cid,$wordCid)) {
                    $type = 'word';
                } elseif (in_array($model->cid,$questionCid)) {
                    $type = 'question';
                } elseif (in_array($model->cid,$informationCid)) {
                    $type = 'information';
                }
                $locUrl = $site.'/'.$type.'/'.$model->id;
            } elseif ($model->type == Article::SINGLE_PAGE) {
                $type = 'page';
                $locUrl = $site.'/'.$type.'/'.$model->sub_title;
            }
            $item = $dom->createElement('url');
            $object->appendChild($item);
            $itemMobile = $dom->createElement('mobile:mobile');
            $item->appendChild($itemMobile);
            $itemMobileType = $dom->createAttribute('type');
            $itemMobile->appendChild($itemMobileType);
            $itemMobileTypeValue = $dom->createTextNode('pc,mobile');
            $itemMobileType->appendChild($itemMobileTypeValue);

            $itemLoc = $dom->createElement('loc');
            $item->appendChild($itemLoc);
            $itemPriority = $dom->createElement('priority');
            $item->appendChild($itemPriority);
            $itemLastmod = $dom->createElement('lastmod');
            $item->appendChild($itemLastmod);
            $itemChangefreq = $dom->createElement('changefreq');
            $item->appendChild($itemChangefreq);
            $LocValue = $dom->createTextNode($locUrl);
            $itemLoc->appendChild($LocValue);
            $priorityValue = $dom->createTextNode(0.50);
            $itemPriority->appendChild($priorityValue);
            $lastmodValue = $dom->createTextNode(date('Y-m-d',time()));
            $itemLastmod->appendChild($lastmodValue);
            $changefreqValue = $dom->createTextNode('Daily');
            $itemChangefreq->appendChild($changefreqValue);
        }
        $modi = $dom->saveXML();         //生成xml文档
        $rootPath = yii::getAlias('@map');
        file_put_contents($rootPath.'/sitemap.xml',$modi);     /* 将对象保存到sitemap.xml文档中 */
        return '生成xml成功';
    }

}
