<?php
$this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'discount-activity-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'columns' => array(
		array(
			'header'=>'<img src="themes/default/images/tableArrows.png">',
			'type'=>'html',
			'value'=>'\'<span class="checker"><input type="checkbox" name="checkRow" class="opacity0"></span>\'',
			'htmlOptions'=>array('width'=>'35px','align'=>'center')
		),
        'activity_id',
        'activity_name',
        array(
			'name'=>'item_num',
			'htmlOptions'=>array('width'=>'75px')
		),
        'begin_time',
        'end_time',
        'create_time',
        array(
            'class' => 'CButtonColumn',
            //'template'=>'{view}{update}{deleted}',
            'buttons' => array(
                'view' => array(
                    'label' => '查看宝贝',
                    'url' => 'Yii::app()->controller->createUrl("items",array("activity_id"=>$data->activity_id))',
                    'click' => 'js:function(){return false;}',
                ),
                'update' => array(
                    'label' => '修改活动',
                    'click' => 'js:function(){return false;}',
                ),
                'delete' => array(
                    'label' => '删除活动',
                    'click' => 'function(){}',
                ),
            ),
        ),
    ),
));