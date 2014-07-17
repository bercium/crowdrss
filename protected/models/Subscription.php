<?php

Yii::import('application.models._base.BaseSubscription');

class Subscription extends BaseSubscription
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}