<?php

namespace app\modules\shop\models;

use app\modules\shop\components\DiscountInterface;
use app\modules\shop\components\SpecialPriceProductInterface;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "{{%discount}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $appliance
 * @property double $value
 * @property integer $value_in_percent
 * @property double $apply_order_price_lg
 */
class Discount extends \yii\db\ActiveRecord implements SpecialPriceProductInterface
{

    public $options = [];


    private static  $allDiscounts = [];

    public function setPriceProduct(Product &$product, Order $order = null)
    {
        self::getAllDiscounts();
        if (isset(self::$allDiscounts['products'])) {
            foreach (self::$allDiscounts['products'] as $discount) {
                $discountFlag = false;
                foreach ($this->getTypes() as $discountType) {
                    $discountTypeObject = new $discountType->class;
                    if ($discountTypeObject instanceof DiscountInterface) {
                        $discountFlag = $discountTypeObject->checkDiscount($discount, $product, $order);
                        if ($discountFlag === false)
                            break;
                    }
                }
                if ($discountFlag === true) {
                    $product->total_price = $discount->getDiscountPrice($product->total_price);
                }
            }
        }
    }


    public static function getAllDiscounts()
    {
        $discounts = Discount::find()->where(['active'=>1])->all();
        foreach ($discounts as $discount) {
            self::$allDiscounts[$discount->appliance][] = $discount;
        }
        return self::$allDiscounts;
    }

    public function getDiscountPrice($price)
    {
        if (intval($this->value_in_percent) === 1) {
            $price *= (100 - $this->value) /100;
        } else {
            $price -= $this->value;
        }


        return $price;
    }

    public function getTypes()
    {
        return DiscountType::findAll(['active'=>1]);
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%discount}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'appliance', 'value'], 'required'],
            [['appliance'], 'string'],
            [['value', 'apply_order_price_lg'], 'number'],
            [['value_in_percent'], 'integer'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    public function afterDelete()
    {
        foreach ($this->getTypes() as $type) {
            $className = $type->class;
            $className::deleteAll(['discount_id'=>$this->id]);
        }

        return parent::afterDelete();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'appliance' => Yii::t('app', 'Appliance'),
            'value' => Yii::t('app', 'Value'),
            'value_in_percent' => Yii::t('app', 'Value In Percent'),
            'apply_order_price_lg' => Yii::t('app', 'Apply Order Price Lg'),
        ];
    }


    /**
     * Search tasks
     * @param $params
     * @return ActiveDataProvider
     */
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
        $query->andFilterWhere(['like', 'appliance', $this->appliance]);


        return $dataProvider;
    }
}
