<?php
namespace application\modules\discount\models\tables;
use application\modules\discount\models\Activity;

/**
 * This is the model class for table "reach_activity".
 *
 * The followings are the available columns in table 'reach_activity':
 * @property integer $id
 * @property string $seller_uin
 * @property string $begin_time
 * @property string $end_time
 * @property string $activity_desc
 * @property string $activity_id
 * @property string $create_time
 * @property string $api_time
 */
class ReachActivity extends Activity
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'reach_activity';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('seller_uin', 'length', 'max'=>20),
			array('activity_desc', 'length', 'max'=>500),
			array('activity_id', 'length', 'max'=>60),
			array('begin_time, end_time, create_time, api_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, seller_uin, begin_time, end_time, activity_desc, activity_id, create_time, api_time', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'seller_uin' => '店主QQ',
			'begin_time' => '开始时间',
			'end_time' => '结束时间',
			'activity_desc' => '活动描述',
			'activity_id' => '活动ID',
			'create_time' => '创建时间',
			'api_time' => 'Api Time',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('seller_uin',$this->seller_uin,true);
		$criteria->compare('begin_time',$this->begin_time,true);
		$criteria->compare('end_time',$this->end_time,true);
		$criteria->compare('activity_desc',$this->activity_desc,true);
		$criteria->compare('activity_id',$this->activity_id,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('api_time',$this->api_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ReachActivity the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
