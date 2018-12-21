<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-07 12:06
 */
namespace frontend\models;

use common\models\FriendlyLink as CommonFriendLink;

class FriendlyLink extends CommonFriendLink
{
    static public function getFriendLink()
    {
        return $links = FriendlyLink::find()->where(['status' => FriendlyLink::DISPLAY_YES])->orderBy("sort asc, id asc")->asArray()->all();
    }
}