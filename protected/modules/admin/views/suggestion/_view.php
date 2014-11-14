<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('seq')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->seq), array('view', 'id'=>$data->seq)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('nick')); ?>:</b>
	<?php echo CHtml::encode($data->nick); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('status')); ?>:</b>
	<?php echo CHtml::encode($data->status); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('action_time')); ?>:</b>
	<?php echo CHtml::encode($data->action_time); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('action')); ?>:</b>
	<?php echo CHtml::encode($data->action); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('message')); ?>:</b>
	<?php echo CHtml::encode($data->message); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('entry_time')); ?>:</b>
	<?php echo CHtml::encode($data->entry_time); ?>
	<br />


</div>