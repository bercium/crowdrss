<?php

Yii::import('application.models._base.BaseRatingHistory');

class RatingHistory extends BaseRatingHistory
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}