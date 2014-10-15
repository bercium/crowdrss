<div class="form">


<?php $form = $this->beginWidget('GxActiveForm', array(
	'id' => 'project-featured-form',
	'enableAjaxValidation' => false,
));
?>

	<p class="note">
		<?php echo Yii::t('app', 'Fields with'); ?> <span class="required">*</span> <?php echo Yii::t('app', 'are required'); ?>.
	</p>

	<?php echo $form->errorSummary($model); ?>

		<div class="row">
		<?php echo $form->labelEx($model,'project_id'); ?>
		<?php echo $form->dropDownList($model, 'project_id', GxHtml::listDataEx(Project::model()->findAllAttributes(null, true," time_added > DATE_ADD(NOW(),INTERVAL -10 DAY) ORDER BY id DESC"))); ?>
		<?php echo $form->error($model,'project_id'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'feature_date'); ?>
		<?php echo $form->textField($model, 'feature_date'); ?>
		<?php echo $form->error($model,'feature_date'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'feature_where'); ?>
    <small>1- daily digest, 2 - weeky digest</small>
		<?php echo $form->textField($model, 'feature_where'); ?>
		<?php echo $form->error($model,'feature_where'); ?>
		</div><!-- row -->
		<div class="row">
		<?php echo $form->labelEx($model,'active'); ?>
		<?php echo $form->textField($model, 'active'); ?>
		<?php echo $form->error($model,'active'); ?>
		</div><!-- row -->
    <?php /* ?>
		<div class="row">
		<?php echo $form->labelEx($model,'show_count'); ?>
		<?php echo $form->textField($model, 'show_count'); ?>
		<?php echo $form->error($model,'show_count'); ?>
		</div><!-- row -->
    <?php */ ?>

<?php
echo GxHtml::submitButton(Yii::t('app', 'Save'));
$this->endWidget();
?>
</div><!-- form -->

 <script>
$(function() {
  $( "#birds" ).autocomplete({
    source: "<?php echo Yii::app()->createUrl('ProjectFeatured/autocomplete'); ?>",
    minLength: 2,
    select: function( event, ui ) {
    
    //ui.item.value
    //ui.item.id
    //ui.item.label
    }
  });
});
</script>