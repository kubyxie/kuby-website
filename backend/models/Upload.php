<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-11 11:30
 */

namespace backend\models;
use yii\base\Model;
use yii\web\UploadedFile;
use yii;
class Upload extends Model
{
    /**
     * @var UploadedFile
     */
    public $xmlFile;

    public function rules()
    {
        return [
            [['xmlFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'xml'],
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $this->xmlFile->saveAs(yii::getAlias('@map').'/' . $this->xmlFile->baseName . '.' . $this->xmlFile->extension);
            return true;
        } else {
            return false;
        }
    }
}