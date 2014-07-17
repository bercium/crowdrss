<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'category-form',
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
		<?php echo $form->labelEx($model,'platform_id'); ?>
		<?php echo $form->dropDownList($model, 'platform_id', GxHtml::listDataEx(Platform::model()->findAllAttributes(null, true))); ?>
		<?php echo $form->error($model,'platform_id'); ?>
		</div><!-- row -->

    <?php /* ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('projects')); ?></label>
		<?php echo $form->checkBoxList($model, 'projects', GxHtml::encodeEx(GxHtml::listDataEx(Project::model()->findAllAttributes(null, true)), false, true)); ?>
		<label><?php echo GxHtml::encode($model->getRelationLabel('projects1')); ?></label>
		<?php  echo $form->checkBoxList($model, 'projects1', GxHtml::encodeEx(GxHtml::listDataEx(Project::model()->findAllAttributes(null, true)), false, true)); ?>
    <?php */ ?>
<?php
echo GxHtml::submitButton(Yii::t('app', 'Save'));
$this->endWidget();
?>
</div><!-- form -->