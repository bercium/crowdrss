<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'outside-links-form',
	'enableAjaxValidation' => false,
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

		<div class="row">
		<?php echo $form->labelEx($model,'category'); ?>
		<?php echo $form->textField($model, 'category', array('maxlength' => 128)); ?>
		<?php echo $form->error($model,'category'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'sub_category'); ?>
		<?php echo $form->textField($model, 'sub_category', array('maxlength' => 128)); ?>
		<?php echo $form->error($model,'sub_category'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model, 'title', array('maxlength' => 256)); ?>
		<?php echo $form->error($model,'title'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'link'); ?>
		<?php echo $form->textField($model, 'link', array('maxlength' => 256)); ?>
		<?php echo $form->error($model,'link'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'position'); ?>
		<?php echo $form->textField($model, 'position'); ?>
		<?php echo $form->error($model,'position'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'keywords'); ?>
		<?php echo $form->textField($model, 'keywords', array('maxlength' => 500)); ?>
		<?php echo $form->error($model,'keywords'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'active'); ?>
		<?php echo $form->textField($model, 'active'); ?>
		<?php echo $form->error($model,'active'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'time_created'); ?>
		<?php echo $form->textField($model, 'time_created'); ?>
		<?php echo $form->error($model,'time_created'); ?>
		</div><!-- row -->


<?php
echo GxHtml::submitButton(Yii::t('app', 'Save'));
$this->endWidget();
?>
</div><!-- form -->