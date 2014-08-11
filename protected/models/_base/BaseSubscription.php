<?php

/**
 * This is the model base class for the table "subscription".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "Subscription".
 *
 * Columns in table "subscription" available as properties of the model,
 * followed by relations of table "subscription" available as properties of the model.
 *
 * @property integer $id
 * @property string $hash
 * @property string $email
 * @property string $platform
 * @property string $category
 * @property string $exclude_orig_category
 * @property integer $rss
 * @property integer $daily_digest
 * @property integer $weekly_digest
 * @property integer $rating
 * @property string $time_created
 * @property string $time_updated
 *
 * @property FeedClickLog[] $feedClickLogs
 * @property FeedOpenLog[] $feedOpenLogs
 * @property FeedRate[] $feedRates
 */
abstract class BaseSubscription extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'subscription';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'Subscription|Subscriptions', $n);
	}

	public static function representingColumn() {
		return 'hash';
	}

	public function rules() {
		return array(
			array('hash, email', 'required'),
      array('time_created, time_updated', 'default', 'value' => date('Y-m-d H:i:s'), 'setOnEmpty' => true, 'on' => 'insert'),			
			array('rss, daily_digest, weekly_digest, rating', 'numerical', 'integerOnly'=>true),
			array('hash', 'length', 'max'=>100),
			array('email, platform, category, exclude_orig_category', 'length', 'max'=>255),
			array('platform, category, exclude_orig_category, rss, daily_digest, weekly_digest, rating', 'default', 'setOnEmpty' => true, 'value' => null),
			array('id, hash, email, platform, category, exclude_orig_category, rss, daily_digest, weekly_digest, rating, time_created, time_updated', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'feedClickLogs' => array(self::HAS_MANY, 'FeedClickLog', 'subscription_id'),
			'feedOpenLogs' => array(self::HAS_MANY, 'FeedOpenLog', 'subscription_id'),
			'feedRates' => array(self::HAS_MANY, 'FeedRate', 'subscription_id'),
		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'hash' => Yii::t('app', 'Hash'),
			'email' => Yii::t('app', 'Email'),
			'platform' => Yii::t('app', 'Platform'),
			'category' => Yii::t('app', 'Category'),
			'exclude_orig_category' => Yii::t('app', 'Exclude Orig Category'),
			'rss' => Yii::t('app', 'Rss'),
			'daily_digest' => Yii::t('app', 'Daily Digest'),
			'weekly_digest' => Yii::t('app', 'Weekly Digest'),
			'rating' => Yii::t('app', 'Rating'),
			'time_created' => Yii::t('app', 'Time Created'),
			'time_updated' => Yii::t('app', 'Time Updated'),
			'feedClickLogs' => null,
			'feedOpenLogs' => null,
			'feedRates' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('hash', $this->hash, true);
		$criteria->compare('email', $this->email, true);
		$criteria->compare('platform', $this->platform, true);
		$criteria->compare('category', $this->category, true);
		$criteria->compare('exclude_orig_category', $this->exclude_orig_category, true);
		$criteria->compare('rss', $this->rss);
		$criteria->compare('daily_digest', $this->daily_digest);
		$criteria->compare('weekly_digest', $this->weekly_digest);
		$criteria->compare('rating', $this->rating);
		$criteria->compare('time_created', $this->time_created, true);
		$criteria->compare('time_updated', $this->time_updated, true);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}