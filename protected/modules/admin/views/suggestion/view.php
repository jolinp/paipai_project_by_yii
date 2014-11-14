<?php
$this->breadcrumbs=array(
	'Suggestions'=>array('index'),
	$model->seq,
);

$this->menu=array(
	array('label'=>'List Suggestion', 'url'=>array('index')),
	array('label'=>'Create Suggestion', 'url'=>array('create')),
	array('label'=>'Update Suggestion', 'url'=>array('update', 'id'=>$model->seq)),
	array('label'=>'Delete Suggestion', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->seq),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Suggestion', 'url'=>array('admin')),
);
?>

<h1>View Suggestion #<?php echo $model->seq; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'seq',
		'nick',
		'status',
		'action_time',
		'action',
		'message',
		'entry_time',
	),
)); ?>
