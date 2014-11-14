<?php
$this->breadcrumbs=array(
	'Suggestions'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Suggestion', 'url'=>array('index')),
	array('label'=>'Manage Suggestion', 'url'=>array('admin')),
);
?>

<h1>Create Suggestion</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>