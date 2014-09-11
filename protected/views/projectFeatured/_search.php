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
		<?php echo $form->label($model, 'project_id'); ?>
		<?php echo $form->dropDownList($model, 'project_id', GxHtml::listDataEx(Project::model()->findAllAttributes(null, true)), array('prompt' => Yii::t('app', 'All'))); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'feature_date'); ?>
		<?php echo $form->textField($model, 'feature_date'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'feature_where'); ?>
		<?php echo $form->textField($model, 'feature_where'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model, 'show_count'); ?>
		<?php echo $form->textField($model, 'show_count'); ?>
	</div>

	<div class="row buttons">
		<?php echo GxHtml::submitButton(Yii::t('app', 'Search')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->
