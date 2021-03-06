<?php

/**
 * This is the model base class for the table "project_featured".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "ProjectFeatured".
 *
 * Columns in table "project_featured" available as properties of the model,
 * followed by relations of table "project_featured" available as properties of the model.
 *
 * @property integer $id
 * @property integer $project_id
 * @property string $feature_date
 * @property integer $feature_where
 * @property integer $show_count
 * @property integer $active
 *
 * @property Project $project
 */
abstract class BaseProjectFeatured extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'project_featured';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'ProjectFeatured|ProjectFeatureds', $n);
	}

	public static function representingColumn() {
		return 'feature_date';
	}

	public function rules() {
		return array(
			array('project_id, feature_date, feature_where', 'required'),
			array('project_id, feature_where, show_count, active', 'numerical', 'integerOnly'=>true),
			array('show_count', 'default', 'setOnEmpty' => true, 'value' => null),
			array('show_count, active', 'default', 'setOnEmpty' => true, 'value' => 0),
			array('id, project_id, feature_date, feature_where, show_count, active', 'safe', 'on'=>'search'),
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
			'feature_date' => Yii::t('app', 'Feature Date'),
			'feature_where' => Yii::t('app', 'Feature Where'),
			'show_count' => Yii::t('app', 'Show Count'),
			'project' => null,
       'active' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('project_id', $this->project_id);
		$criteria->compare('feature_date', $this->feature_date, true);
		$criteria->compare('feature_where', $this->feature_where);
		$criteria->compare('show_count', $this->show_count);
		$criteria->compare('active', $this->active);
    

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}