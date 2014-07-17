<div class="view">

	<?php echo GxHtml::encode($data->getAttributeLabel('id')); ?>:
	<?php echo GxHtml::link(GxHtml::encode($data->id), array('view', 'id' => $data->id)); ?>
	<br />

	<?php echo GxHtml::encode($data->getAttributeLabel('platform_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->platform)); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('category_id')); ?>:
		<?php echo GxHtml::encode(GxHtml::valueEx($data->category)); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('title')); ?>:
	<?php echo GxHtml::encode($data->title); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('description')); ?>:
	<?php echo GxHtml::encode($data->description); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('image')); ?>:
	<?php echo GxHtml::encode($data->image); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('link')); ?>:
	<?php echo GxHtml::encode($data->link); ?>
	<br />
	<?php /*
	<?php echo GxHtml::encode($data->getAttributeLabel('start')); ?>:
	<?php echo GxHtml::encode($data->start); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('end')); ?>:
	<?php echo GxHtml::encode($data->end); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('location')); ?>:
	<?php echo GxHtml::encode($data->location); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('creator')); ?>:
	<?php echo GxHtml::encode($data->creator); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('creator_created')); ?>:
	<?php echo GxHtml::encode($data->creator_created); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('creator_backed')); ?>:
	<?php echo GxHtml::encode($data->creator_backed); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('goal')); ?>:
	<?php echo GxHtml::encode($data->goal); ?>
	<br />
	<?php echo GxHtml::encode($data->getAttributeLabel('type_of_funding')); ?>:
	<?php echo GxHtml::encode($data->type_of_funding); ?>
	<br />
	*/ ?>

</div>