<div class="view">

	<?php echo GxHtml::encode($data->getAttributeLabel('id')); ?>:
	<?php echo GxHtml::link(GxHtml::encode($data->id), array('view', 'id' => $data->id)); ?>
	<br />

	<?php echo GxHtml::encode($data->getAttributeLabel('project_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->project)); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('feature_date')); ?>:
	<?php echo GxHtml::encode($data->feature_date); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('feature_where')); ?>:
	<?php echo GxHtml::encode($data->feature_where); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('show_count')); ?>:
	<?php echo GxHtml::encode($data->show_count); ?>
	<br />

</div>