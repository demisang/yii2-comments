<?php

namespace demi\comments\backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use demi\comments\common\models\Comment;

/**
 * CommentSearch represents the model behind the search form about `common\models\Comment`.
 */
class CommentSearch extends Comment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'material_type', 'material_id', 'user_id', 'user_ip', 'parent_id', 'language_id', 'is_replied', 'is_approved', 'is_deleted'], 'integer'],
            [['text', 'user_name', 'user_email', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Comment::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'material_type' => $this->material_type,
            'material_id' => $this->material_id,
            'user_id' => $this->user_id,
            'user_ip' => $this->user_ip,
            'parent_id' => $this->parent_id,
            'language_id' => $this->language_id,
            'is_replied' => $this->is_replied,
            'is_approved' => $this->is_approved,
            'is_deleted' => $this->is_deleted,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'text', $this->text])
            ->andFilterWhere(['like', 'user_name', $this->user_name])
            ->andFilterWhere(['like', 'user_email', $this->user_email]);

        return $dataProvider;
    }
}
