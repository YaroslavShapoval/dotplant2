<?php

namespace app\modules\core\models;

use yii\data\ActiveDataProvider;
use Yii;

/**
 * This is the model class for table "content_block".
 *
 * @property integer $id
 * @property string $name
 * @property string $key
 * @property string $value
 * @property integer $preload
 */
class ContentBlock extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'content_block';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['value'], 'string'],
            [['preload'], 'integer'],
            [['key'], 'unique'],
            [['name', 'key'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Name'),
            'key' => Yii::t('app','Key'),
            'value' => Yii::t('app','Value'),
            'preload' => Yii::t('app', 'Preload'),
        ];
    }
    public function search($params)
    {
        /* @var $query \yii\db\ActiveQuery */
        $query = self::find();
        $dataProvider = new ActiveDataProvider(
            [
                'query' => $query,
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]
        );
        if (!($this->load($params))) {
            return $dataProvider;
        }
        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'key', $this->key]);
        $query->andFilterWhere(['preload' => $this->preload]);
        return $dataProvider;
    }
}