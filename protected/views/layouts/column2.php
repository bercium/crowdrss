<?php /* @var $this Controller */ ?>
<?php $this->beginContent('//layouts/main'); ?>

	<div class="row pb30">
    <div class="column pt30 medium-8">
      <?php echo $content; ?>
    </div>
    <div class="column pt30 medium-3">
      <?php
        $this->beginWidget('zii.widgets.CPortlet', array(
          'title'=>'Operations',
        ));
        $this->widget('zii.widgets.CMenu', array(
          'items'=>$this->menu,
          'htmlOptions'=>array('class'=>'operations'),
        ));
        $this->endWidget();
      ?>
    </div>
  </div>

<?php $this->endContent(); ?>