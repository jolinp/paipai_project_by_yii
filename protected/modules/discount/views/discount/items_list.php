<?php
$this->renderPartial('/discount/_batch_element');

$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'discount-items-grid',
    'dataProvider' => $dataProvider,
    'columns' => array(
        array(
            'class' => 'CCheckBoxColumn',
            'selectableRows' => 1000,
        ),
        array(
            'header' => '宝贝标题',
            'type' => 'raw',
            'value' => 'CHtml::link($data["itemName"],"http://item.wanggou.com/".$data["itemCode"])',
        ),
        array(
            'header' => '图片',
            'type' => 'raw',
            'value' => 'CHtml::image($data["picLink"].".3.jpg","",array("width"=>"80"))',
            'htmlOptions' => array('width' => 150),
        ),
        array(
            'header' => '价格',
            'value' => '$data["itemPrice"]."元"',
            'htmlOptions' => array('width' => 100),
        ),
        array(
            'header' => '折扣',
            'type' => 'raw',
            'value' => 'CHtml::textField("itemDiscount",$data["itemDiscount"],array("size"=>3,"class"=>"discount-value"))."折"',
            'htmlOptions' => array('width' => 100),
        ),
        array(
            'header' => '折后价',
            'value' => '$data["discountPrice"]."元"',
            'htmlOptions' => array('width' => 100),
        ),
        array(
            'header' => '限购数',
            'type' => 'raw',
            'value' => 'CHtml::textField("buyLimit",$data["buyLimit"],array("size"=>5,"class"=>"limit-value"))."件"',
            'htmlOptions' => array('width' => 100),
        ),
        array(
            'class' => 'CButtonColumn',
            'template' => '{adjust}{reduce}',
            'buttons' => array(
                'adjust' => array(
                    'label' => '保存',
                    'url' => 'Yii::app()->controller->createUrl("/discount/item/adjust", array("itemCode" => $data["itemCode"]))',
                    'click' => 'js:function(){setItem(this);return false;}',
                    'imageUrl' => Yii::app()->baseUrl . '/images/accept.png'
                ),
                'reduce' => array(
                    'label' => '删除',
                    'url' => 'Yii::app()->controller->createUrl("/discount/item/reduce", array("itemCode" => $data["itemCode"]))',
                    'imageUrl' => Yii::app()->baseUrl . '/images/cancel.png',
                    'click' => 'js:function(){setItem(this);return false;}',
                ),
            ),
        ),
    ),
));

