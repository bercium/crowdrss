<?php

/**
 * This is the model base class for the table "feed_click_log".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "FeedClickLog".
 *
 * Columns in table "feed_click_log" available as properties of the model,
 * followed by relations of table "feed_click_log" available as properties of the model.
 *
 * @property integer $id
 * @property integer $project_id
 * @property integer $subscription_id
 * @property string $time_clicked
 *
 * @property Subscription $subscription
 * @property Project $project
 */
abstract class BaseFeedClickLog extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'feed_click_log';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'FeedClickLog|FeedClickLogs', $n);
	}

	public static function representingColumn() {
		return 'time_clicked';
	}

	public function rules() {
		return array(
			array('project_id, subscription_id', 'required'),
      array('time_clicked', 'default', 'value' => date('Y-m-d H:i:s'), 'setOnEmpty' => true, 'on' => 'insert'),
        
			array('project_id, subscription_id', 'numerical', 'integerOnly'=>true),
			array('id, project_id, subscription_id, time_clicked', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'subscription' => array(self::BELONGS_TO, 'Subscription', 'subscription_id'),
			'project' => array(self::BELONGS_TO, 'Project', 'project_id'),
		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'project_id' => null,
			'subscription_id' => null,
			'time_clicked' => Yii::t('app', 'Time Clicked'),
			'subscription' => null,
			'project' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('project_id', $this->project_id);
		$criteria->compare('subscription_id', $this->subscription_id);
		$criteria->compare('time_clicked', $this->time_clicked, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}