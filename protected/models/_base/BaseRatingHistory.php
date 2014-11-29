<?php

/**
 * This is the model base class for the table "rating_history".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "RatingHistory".
 *
 * Columns in table "rating_history" available as properties of the model,
 * followed by relations of table "rating_history" available as properties of the model.
 *
 * @property integer $id
 * @property integer $project_id
 * @property string $data
 * @property string $time_rated
 *
 * @property Project $project
 */
abstract class BaseRatingHistory extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'rating_history';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'RatingHistory|RatingHistories', $n);
	}

	public static function representingColumn() {
		return 'data';
	}

	public function rules() {
		return array(
			array('project_id, data', 'required'),
			array('project_id', 'numerical', 'integerOnly'=>true),
      array('time_rated', 'default', 'value' => date('Y-m-d H:i:s'), 'setOnEmpty' => true, 'on' => 'insert'),      
			array('id, project_id, data, time_rated', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
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
			'data' => Yii::t('app', 'Data'),
			'time_rated' => Yii::t('app', 'Time Rated'),
			'project' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('project_id', $this->project_id);
		$criteria->compare('data', $this->data, true);
		$criteria->compare('time_rated', $this->time_rated, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}