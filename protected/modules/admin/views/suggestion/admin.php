<?php
/** @var $model Suggestion */
?>
<h1>优化建议管理</h1>


<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'suggestion-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'columns' => array(
        'seq',
        'nick',
        array(
            'name' => 'status',
//            'filter' => CHtml::dropDownList('Suggestion[status]', $model->status, CMap::mergeArray(array('' => ''), $model->getStatusOptions())),
            'filter' =>   $model->getStatusOptions(),
            'value' => '$data->statusText'
        ),
        'action_time',
        //'action',
        'message',
        /*
        'entry_time',
        */
        array(
            'class' => 'CButtonColumn',
        ),
    ),
)); ?>
