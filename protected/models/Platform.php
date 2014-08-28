<?php

Yii::import('application.models._base.BasePlatform');

class Platform extends BasePlatform
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}