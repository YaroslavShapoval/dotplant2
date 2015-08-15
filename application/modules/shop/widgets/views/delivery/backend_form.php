<?php
/**
 * Use existent form
 * @var \yii\web\View $this
 * @var \app\modules\shop\models\DeliveryInformation $deliveryInformation
 * @var \app\modules\shop\models\OrderDeliveryInformation $orderDeliveryInformation
 * @var boolean $immutable
 * @var string $action
 * @var \yii\bootstrap\ActiveForm $form
 * @var array $additional
 *
 * TODO
 * In the order total price are taken into account only data from SpecialPriceObject
 * and only `shipping_option_id` from this form.
 * @see SpecialPriceObject
 */

    echo $form->field($orderDeliveryInformation, 'shipping_option_id')
        ->dropDownList(\app\components\Helper::getModelMap(\app\modules\shop\models\ShippingOption::className(), 'id', 'name', true, true),
        ['readonly' => $immutable]
    );
    echo $form->field($orderDeliveryInformation, 'shipping_price')->textInput(['readonly' => $immutable]);
    echo $form->field($orderDeliveryInformation, 'shipping_price_total')->textInput(['readonly' => $immutable]);
    echo $form->field($orderDeliveryInformation, 'planned_delivery_date')->textInput(['readonly' => $immutable]);
//    echo $form->field($orderDeliveryInformation, 'planned_delivery_time')->textInput(['readonly' => $immutable]);
    echo $form->field($orderDeliveryInformation, 'planned_delivery_time_range')->textInput(['readonly' => $immutable]);
    /** @var \app\properties\AbstractModel $abstractModel */
    $abstractModel = $orderDeliveryInformation->getAbstractModel();
    $abstractModel->setArrayMode(false);
    foreach ($abstractModel->attributes() as $attr) {
        echo $form->field($abstractModel, $attr)->textInput(['readonly' => $immutable]);
    }
