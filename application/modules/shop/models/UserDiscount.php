<?php

namespace app\modules\shop\models;

use app\modules\shop\components\DiscountInterface;
use app\modules\shop\components\DiscountProductInterface;
use app\modules\shop\components\FilterInterface;
use Yii;

/**
 * This is the model class for table "{{%user_discount}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $discount_id
 */
class UserDiscount extends \yii\db\ActiveRecord implements DiscountInterface
{
    public function checkDiscount(Discount $discount, Product $product = null, Order $order = null)
    {
        $result = false;
        if (intval(self::find()->where(['discount_id'=>$discount->id])->count()) === 0)
        {
            $result = true;
        } elseif (
            $order !== null &&
            intval(self::find()->where(['discount_id'=>$discount->id, 'user_id'=>$order->user_id])->count()) === 1
        ) {
            $result = true;
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_discount}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'discount_id'], 'required'],
            [['user_id', 'discount_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'discount_id' => Yii::t('app', 'Discount ID'),
        ];
    }
}
