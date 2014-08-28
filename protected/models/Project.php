<?php

Yii::import('application.models._base.BaseProject');

class Project extends BaseProject
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}