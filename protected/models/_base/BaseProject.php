<?php

/**
 * This is the model base class for the table "project".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "Project".
 *
 * Columns in table "project" available as properties of the model,
 * followed by relations of table "project" available as properties of the model.
 *
 * @property integer $id
 * @property integer $platform_id
 * @property integer $orig_category_id
 * @property string $title
 * @property string $description
 * @property string $image
 * @property string $link
 * @property string $start
 * @property string $end
 * @property string $location
 * @property string $creator
 * @property integer $creator_created
 * @property integer $creator_backed
 * @property string $goal
 * @property integer $type_of_funding
 * @property double $rating
 * @property string $time_added
 *
 * @property FeedClickLog[] $feedClickLogs
 * @property FeedOpenLog[] $feedOpenLogs
 * @property FeedRate[] $feedRates
 * @property Platform $platform
 * @property OrigCategory $origCategory
 */
abstract class BaseProject extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'project';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'Project|Projects', $n);
	}

	public static function representingColumn() {
		return 'title';
	}

	public function rules() {
		return array(
			array('platform_id, orig_category_id, title, description, image, link', 'required'),
      array('time_added', 'default', 'value' => date('Y-m-d H:i:s'), 'setOnEmpty' => true, 'on' => 'insert'),        
			array('platform_id, orig_category_id, creator_created, creator_backed, type_of_funding', 'numerical', 'integerOnly'=>true),
			array('rating', 'numerical'),
			array('title, image, link, location, creator', 'length', 'max'=>255),
			array('description', 'length', 'max'=>1000),
			array('goal', 'length', 'max'=>20),
			array('start, end', 'safe'),
			array('start, end, location, creator, creator_created, creator_backed, goal, type_of_funding, rating', 'default', 'setOnEmpty' => true, 'value' => null),
			array('id, platform_id, orig_category_id, title, description, image, link, start, end, location, creator, creator_created, creator_backed, goal, type_of_funding, rating, time_added', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'feedClickLogs' => array(self::HAS_MANY, 'FeedClickLog', 'project_id'),
			'feedOpenLogs' => array(self::HAS_MANY, 'FeedOpenLog', 'project_id'),
			'feedRates' => array(self::HAS_MANY, 'FeedRate', 'project_id'),
			'platform' => array(self::BELONGS_TO, 'Platform', 'platform_id'),
			'origCategory' => array(self::BELONGS_TO, 'OrigCategory', 'orig_category_id'),
		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'platform_id' => null,
			'orig_category_id' => null,
			'title' => Yii::t('app', 'Title'),
			'description' => Yii::t('app', 'Description'),
			'image' => Yii::t('app', 'Image'),
			'link' => Yii::t('app', 'Link'),
			'start' => Yii::t('app', 'Start'),
			'end' => Yii::t('app', 'End'),
			'location' => Yii::t('app', 'Location'),
			'creator' => Yii::t('app', 'Creator'),
			'creator_created' => Yii::t('app', 'Creator Created'),
			'creator_backed' => Yii::t('app', 'Creator Backed'),
			'goal' => Yii::t('app', 'Goal'),
			'type_of_funding' => Yii::t('app', 'Type Of Funding'),
			'rating' => Yii::t('app', 'Rating'),
			'time_added' => Yii::t('app', 'Time Added'),
			'feedClickLogs' => null,
			'feedOpenLogs' => null,
			'feedRates' => null,
			'platform' => null,
			'origCategory' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('platform_id', $this->platform_id);
		$criteria->compare('orig_category_id', $this->orig_category_id);
		$criteria->compare('title', $this->title, true);
		$criteria->compare('description', $this->description, true);
		$criteria->compare('image', $this->image, true);
		$criteria->compare('link', $this->link, true);
		$criteria->compare('start', $this->start, true);
		$criteria->compare('end', $this->end, true);
		$criteria->compare('location', $this->location, true);
		$criteria->compare('creator', $this->creator, true);
		$criteria->compare('creator_created', $this->creator_created);
		$criteria->compare('creator_backed', $this->creator_backed);
		$criteria->compare('goal', $this->goal, true);
		$criteria->compare('type_of_funding', $this->type_of_funding);
		$criteria->compare('rating', $this->rating);
		$criteria->compare('time_added', $this->time_added, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}