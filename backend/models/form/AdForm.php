<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-12-05 12:47
 */

namespace backend\models\form;

use common\helpers\Util;
use yii;
use common\libs\Constants;

class AdForm extends \Common\models\Options
{
    public $ad;

    public $link;

    public $desc;

    public $target = Constants::TARGET_BLANK;

    public $created_at;

    public $updated_at;

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => yii::t('app', 'Sign'),
            'input_type' => yii::t('app', 'Ad Type'),
            'tips' => yii::t('app', 'Description'),
            'ad' => yii::t('app', 'Ad'),
            'link' => yii::t('app', 'Jump Link'),
            'desc' => yii::t('app', 'Ad Explain'),
            'autoload' => yii::t('app', 'Status'),
            'sort' => yii::t('app', 'Sort'),
            'target' => yii::t('app', 'Target'),
            'created_at' => yii::t('app', 'Created At'),
            'updated_at' => yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'unique'],
            [
                ['name'],
                'match',
                'pattern' => '/^[a-zA-Z][0-9_]*/',
                'message' => yii::t('app', 'Must begin with alphabet and can only includes alphabet,_,and number')
            ],
            [['name', 'tips', 'input_type'], 'required'],
            [['sort', 'autoload'], 'integer'],
            [[ 'link', 'target', 'desc'], 'string'],
            [['ad'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif, webp'],
        ];
    }

    public function beforeSave($insert)
    {
        $this->type = self::TYPE_AD;
        if( $this->input_type == Constants::AD_TEXT ){
            $oldInput = $this->getOldAttribute('input_type');
            if( $oldInput != Constants::AD_TEXT ){//删除旧广告文件
                $text = $this->ad;
                Util::handleModelSingleFileUploadAbnormal($this, 'ad', '@uploads/setting/ad/', $this->getOldAttribute('ad'), ['deleteOldFile'=>true]);
                $this->ad = $text;
            }
        }else {
            Util::handleModelSingleFileUploadAbnormal($this, 'ad', '@uploads/setting/ad/', $this->getOldAttribute('ad'));
        }

        $value = [
            'ad' => $this->ad,
            'link' => $this->link,
            'target' => $this->target,
            'desc' => $this->desc,
            'created_at' => $insert ? time() : $this->created_at,
            'updated_at' => time(),
        ];
        $this->value = json_encode($value);
        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        $value = json_decode($this->value);
        if( $this->input_type !== Constants::AD_TEXT){
            /** @var $cdn \feehi\cdn\TargetAbstract */
            $cdn = yii::$app->get('cdn');
            $this->ad = $cdn->getCdnUrl($value->ad);
        }else{
            $this->ad = $value->ad;
        }
        $this->link = $value->link;
        $this->desc = $value->desc;
        $this->target = $value->target;
        $this->updated_at = $value->updated_at;
        $this->created_at = $value->created_at;
        $this->setOldAttributes([
            'id' => $this->id,
            'name' => $this->name,
            'value' => $this->value,
            'input_type' => $this->input_type,
            'autoload' => $this->autoload,
            'tips' => $this->tips,
            'sort' => $this->sort,
            'ad' => $value->ad,
            'link' => $value->link,
            'desc' => $value->desc,
            'target' => $value->target,
            'created_at' => $value->created_at,
            'updated_at' => $value->updated_at,
        ]);
    }

    public function afterDelete()
    {
        if( $this->input_type != Constants::AD_TEXT ){
            $file = yii::getAlias('@frontend/web') . $this->ad;
            if( file_exists($file) && is_file($file) ) unlink($file);
        }
    }
}