<?php
/* @var $this SiteController */

$this->pageTitle=Yii::app()->name;



?>

<a href ="<?php echo Yii::app()->createUrl('platform'); ?>">Dodaj platforme</a><br />
<a href ="<?php echo Yii::app()->createUrl('category'); ?>">Dodaj kategorije</a><br />
<a href ="<?php echo Yii::app()->createUrl('project'); ?>">Dodaj projekte</a><br />

