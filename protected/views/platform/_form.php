<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'platform-form',
	'enableAjaxValidation' => false,
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

		<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model, 'name', array('maxlength' => 255)); ?>
		<?php echo $form->error($model,'name'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'download'); ?>
		<?php echo $form->checkBox($model, 'download'); ?>
		<?php echo $form->error($model,'download'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'sort'); ?>
		<?php echo $form->textField($model, 'sort'); ?>
		<?php echo $form->error($model,'sort'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'active'); ?>
		<?php echo $form->checkBox($model, 'active'); ?>
		<?php echo $form->error($model,'active'); ?>
		</div><!-- row -->


<?php
echo GxHtml::submitButton(Yii::t('app', 'Save'));
$this->endWidget();
?>
</div><!-- form -->