<?php
$this->breadcrumbs=array(
	'Suggestions',
);

$this->menu=array(
	array('label'=>'Create Suggestion', 'url'=>array('create')),
	array('label'=>'Manage Suggestion', 'url'=>array('admin')),
);
?>

<h1>Suggestions</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
