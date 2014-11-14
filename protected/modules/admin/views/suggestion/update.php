<?php
$this->breadcrumbs=array(
	'Suggestions'=>array('index'),
	$model->seq=>array('view','id'=>$model->seq),
	'Update',
);

$this->menu=array(
	array('label'=>'List Suggestion', 'url'=>array('index')),
	array('label'=>'Create Suggestion', 'url'=>array('create')),
	array('label'=>'View Suggestion', 'url'=>array('view', 'id'=>$model->seq)),
	array('label'=>'Manage Suggestion', 'url'=>array('admin')),
);
?>

<h1>Update Suggestion <?php echo $model->seq; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>