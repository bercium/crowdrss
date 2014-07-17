<div class="wide form">

<?php $form = $this->beginWidget('GxActiveForm', array(
	'action' => Yii::app()->createUrl($this->route),
	'method' => 'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model, 'id'); ?>
		<?php echo $form->textField($model, 'id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'platform_id'); ?>
		<?php echo $form->dropDownList($model, 'platform_id', GxHtml::listDataEx(Category::model()->findAllAttributes(null, true)), array('prompt' => Yii::t('app', 'All'))); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'category_id'); ?>
		<?php echo $form->dropDownList($model, 'category_id', GxHtml::listDataEx(Category::model()->findAllAttributes(null, true)), array('prompt' => Yii::t('app', 'All'))); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'title'); ?>
		<?php echo $form->textField($model, 'title', array('maxlength' => 255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'description'); ?>
		<?php echo $form->textField($model, 'description', array('maxlength' => 1000)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'image'); ?>
		<?php echo $form->textField($model, 'image', array('maxlength' => 255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'link'); ?>
		<?php echo $form->textField($model, 'link', array('maxlength' => 255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'start'); ?>
		<?php echo $form->textField($model, 'start'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'end'); ?>
		<?php echo $form->textField($model, 'end'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'location'); ?>
		<?php echo $form->textField($model, 'location', array('maxlength' => 255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'creator'); ?>
		<?php echo $form->textField($model, 'creator', array('maxlength' => 255)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'creator_created'); ?>
		<?php echo $form->textField($model, 'creator_created'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'creator_backed'); ?>
		<?php echo $form->textField($model, 'creator_backed'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'goal'); ?>
		<?php echo $form->textField($model, 'goal', array('maxlength' => 20)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'type_of_funding'); ?>
		<?php echo $form->textField($model, 'type_of_funding'); ?>
	</div>

	<div class="row buttons">
		<?php echo GxHtml::submitButton(Yii::t('app', 'Search')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->
