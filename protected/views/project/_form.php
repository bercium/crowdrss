<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'project-form',
	'enableAjaxValidation' => false,
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

		<div class="row">
		<?php echo $form->labelEx($model,'platform_id'); ?>
		<?php echo $form->dropDownList($model, 'platform_id', GxHtml::listDataEx(Platform::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'platform_id'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'orig_category_id'); ?>
		<?php echo $form->dropDownList($model, 'orig_category_id', GxHtml::listDataEx(OrigCategory::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'orig_category_id'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model, 'title', array('maxlength' => 255)); ?>
		<?php echo $form->error($model,'title'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textField($model, 'description', array('maxlength' => 1000)); ?>
		<?php echo $form->error($model,'description'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'image'); ?>
		<?php echo $form->textField($model, 'image', array('maxlength' => 255)); ?>
		<?php echo $form->error($model,'image'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'link'); ?>
		<?php echo $form->textField($model, 'link', array('maxlength' => 255)); ?>
		<?php echo $form->error($model,'link'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'start'); ?>
		<?php echo $form->textField($model, 'start'); ?>
		<?php echo $form->error($model,'start'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'end'); ?>
		<?php echo $form->textField($model, 'end'); ?>
		<?php echo $form->error($model,'end'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'location'); ?>
		<?php echo $form->textField($model, 'location', array('maxlength' => 255)); ?>
		<?php echo $form->error($model,'location'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'creator'); ?>
		<?php echo $form->textField($model, 'creator', array('maxlength' => 255)); ?>
		<?php echo $form->error($model,'creator'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'creator_created'); ?>
		<?php echo $form->textField($model, 'creator_created'); ?>
		<?php echo $form->error($model,'creator_created'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'creator_backed'); ?>
		<?php echo $form->textField($model, 'creator_backed'); ?>
		<?php echo $form->error($model,'creator_backed'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'goal'); ?>
		<?php echo $form->textField($model, 'goal', array('maxlength' => 20)); ?>
		<?php echo $form->error($model,'goal'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'type_of_funding'); ?>
		<?php echo $form->textField($model, 'type_of_funding'); ?>
		<?php echo $form->error($model,'type_of_funding'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'time_added'); ?>
		<?php echo $form->textField($model, 'time_added'); ?>
		<?php echo $form->error($model,'time_added'); ?>
		</div><!-- row -->


<?php
echo GxHtml::submitButton(Yii::t('app', 'Save'));
$this->endWidget();
?>
</div><!-- form -->