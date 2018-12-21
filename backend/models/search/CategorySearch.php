<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace backend\models\search;

use backend\behaviors\TimeSearchBehavior;
use backend\components\search\SearchEvent;
use common\models\Category;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class CategorySearch extends Category
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'alias', 'remark'], 'string'],
            [['created_at', 'updated_at'], 'string'],
            [
                [
                    'id',
                    'parent_id',
                    'sort',
                ],
                'integer',
            ],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function behaviors()
    {
        return [
            TimeSearchBehavior::className()
        ];
    }

    /**
     * @param $params
     * @param int $type
     * @return \yii\data\ActiveDataProvider
     */
    public function search($params)
    {
        $query = Category::find()->select([]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'sort' => SORT_ASC,
                    'id' => SORT_DESC,
                ]
            ]
        ]);
        $this->load($params);
        if (! $this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['id' => $this->id])
            ->andFilterWhere(['parent_id' => $this->parent_id])
            ->andFilterWhere(['sort' => $this->sort])
            ->andFilterWhere(['like', 'alias', $this->alias])
            ->andFilterWhere(['like', 'remark', $this->remark]);
        $this->trigger(SearchEvent::BEFORE_SEARCH, new SearchEvent(['query'=>$query]));
        return $dataProvider;
    }

}