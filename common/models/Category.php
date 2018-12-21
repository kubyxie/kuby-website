<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace common\models;

use Yii;
use common\helpers\FamilyTree;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use common\helpers\Util;

;

/**
 * This is the model class for table "{{%category}}".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $name
 * @property string $alias
 * @property integer $sort
 * @property string $remark
 * @property string $created_at
 * @property string $updated_at
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%category}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sort', 'parent_id', 'created_at', 'updated_at'], 'integer'],
            [['sort'], 'compare', 'compareValue' => 0, 'operator' => '>='],
            [['parent_id'], 'default', 'value' => 0],
            [['name', 'alias', 'remark'], 'string', 'max' => 255],
            [['alias'],  'match', 'pattern' => '/^[a-zA-Z0-9_]+$/', 'message' => yii::t('app', 'Only includes alphabet,_,and number')],
            ['alias','isTrue'],
            [['name', 'alias'], 'required'],
            [['cover'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif, webp'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'parent_id' => Yii::t('app', 'Parent Category Id'),
            'name' => Yii::t('app', 'Name'),
            'alias' => Yii::t('app', 'Alias'),
            'sort' => Yii::t('app', 'Sort'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'cover' => Yii::t('app', 'Cover'),
            'remark' => Yii::t('app', 'Remark'),
        ];
    }

    /**
     * 判断别名否存在
     * @inheritdoc
     */
    public function isTrue($attribute, $params)
    {
        $isTrue = self::find()->where(['<>','id',$this->id])->andWhere(['alias' => $this->alias])->one();
        if ($isTrue) {
            $this->addError($attribute, "别名已存在，请从新输入");
        }

    }
    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    protected static function _getCategories()
    {
        return self::find()->orderBy("sort asc,parent_id asc")->asArray()->all();
    }

    /**
     * @return array
     */
    public static function getCategories()
    {
        $categories = self::_getCategories();
        $familyTree = new FamilyTree($categories);
        $array = $familyTree->getDescendants(0);
        return ArrayHelper::index($array, 'id');
    }

    /**
     * @return array
     */
    public static function getCategoriesName()
    {
        $categories = self::getCategories();
        $data = [];
        foreach ($categories as $k => $category){
            if( isset($categories[$k+1]['level']) && $categories[$k+1]['level'] == $category['level'] ){
                $name = ' ├' . $category['name'];
            }else{
                $name = ' └' . $category['name'];
            }
            if( end($categories) == $category ){
                $sign = ' └';
            }else{
                $sign = ' │';
            }
            $data[$category['id']] = str_repeat($sign, $category['level']-1) . $name;
        }
        return $data;
    }

    /**
     * @return array
     */
    public static function getMenuCategories()
    {
        $categories = self::getCategories();
        $familyTree = new FamilyTree($categories);
        $data = [];
        foreach ($categories as $k => $v){
            $parents = $familyTree->getAncectors($v['id']);
            $url = '';
            if(!empty($parents)){
                $parents = array_reverse($parents);
                foreach ($parents as $parent) {
                    $url .= '/' . $parent['alias'];
                }
            }
            $url .= '/'.$v['alias'];
            if( isset($categories[$k+1]['level']) && $categories[$k+1]['level'] == $v['level'] ){
                $name = ' ├' . $v['name'];
            }else{
                $name = ' └' . $v['name'];
            }
            if( end($categories) == $v ){
                $sign = ' └';
            }else{
                $sign = ' │';
            }
            $data[$url] = str_repeat($sign, $v['level']-1) . $name;
        }
        return $data;
    }

    /**
     * @param $id
     * @return array
     */
    public static function getDescendants($id)
    {
        $familyTree = new FamilyTree(self::_getCategories());
        return $familyTree->getDescendants($id);
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        $categories = self::find()->orderBy("sort asc,parent_id asc")->asArray()->all();
        $familyTree = new FamilyTree( $categories );
        $subs = $familyTree->getDescendants($this->id);
        if (! empty($subs)) {
            $this->addError('id', yii::t('app', 'Allowed not to be deleted, sub level exsited.'));
            return false;
        }
        if (Article::findOne(['cid' => $this->id]) != null) {
            $this->addError('id', yii::t('app', 'Allowed not to be deleted, some article belongs to this category.'));
            return false;
        }
        return parent::beforeDelete();
    }

    /**
     * @inheritdoc
     */
    public function afterValidate()
    {
        if (! $this->getIsNewRecord() ) {
            if( $this->id == $this->parent_id ) {
                $this->addError('parent_id', yii::t('app', 'Cannot be themself sub.'));
                return false;
            }
            $familyTree = new FamilyTree(self::_getCategories());
            $descendants = $familyTree->getDescendants($this->id);
            $descendants = ArrayHelper::getColumn($descendants, 'id');
            if( in_array($this->parent_id, $descendants) ){
                $this->addError('parent_id', yii::t('app', 'Cannot be themselves descendants sub'));
                return false;
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        Util::handleModelSingleFileUpload($this, 'cover', $insert, '@cover');
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        self::_generateUrlRules();
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }

    private static function _generateUrlRules()
    {
        $categories = self::getCategories();
        $familyTree = new FamilyTree($categories);
        $data = [];
        foreach ($categories as $v){
            $parents = $familyTree->getAncectors($v['id']);
            $url = '';
            if(!empty($parents)){
                $parents = array_reverse($parents);
                foreach ($parents as $parent) {
                    $url .= '/' . $parent['alias'];
                }
            }
            $url .= '/<cat:' . $v['alias'] . '>';
            $data[$url] = 'article/index';
        }
        $json = json_encode($data);
        $path = yii::getAlias('@frontend/runtime/cache');
        if( !file_exists($path) ) FileHelper::createDirectory($path);
        file_put_contents($path . '/category.txt', $json);
    }

    public static function getUrlRules()
    {
        $file = yii::getAlias('@frontend/runtime/cache/category.txt');
        if( !file_exists($file) ){
            self::_generateUrlRules();
        }
        return json_decode(file_get_contents($file), true);
    }

    public function getParent()
    {
        return $this->hasOne(self::className(), ['id' => 'parent_id']);
    }

    //根据分类类型获取分类标识属于哪个模块
    public static function getCategoryType($cid,$type)
    {
        $cidType = '';
        if ($type == Article::ARTICLE) {
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
            if (in_array($cid, $courseCid)) {
                $cidType = 'course';
            } elseif (in_array($cid, $wordCid)) {
                $cidType = 'word';
            } elseif (in_array($cid, $questionCid)) {
                $cidType = 'question';
            } elseif (in_array($cid, $informationCid)) {
                $cidType = 'information';
            }
        } elseif ($type == Article::SINGLE_PAGE) {
            $cidType = 'page';

        }
        return $cidType;
    }

}
