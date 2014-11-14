<?php
namespace application\modules\discount\models\tables;

/**
 * This is the model class for table "reach_activity_content".
 *
 * The followings are the available columns in table 'reach_activity_content':
 * @property integer $id
 * @property string $uin
 * @property string $activity_id
 * @property integer $cost_flag
 * @property integer $cost_money
 * @property integer $favorable_flag
 * @property integer $free_money
 * @property integer $free_rebate
 * @property string $present_name
 * @property string $present_url
 * @property integer $barter_money
 * @property string $barter_name
 * @property string $barter_url
 * @property integer $batch_id
 * @property integer $face_value
 */
class ReachActivityContent extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'reach_activity_content';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('cost_flag, cost_money, favorable_flag, free_money, free_rebate, barter_money, batch_id, face_value', 'numerical', 'integerOnly'=>true),
			array('uin', 'length', 'max'=>20),
			array('activity_id', 'length', 'max'=>60),
			array('present_name, present_url, barter_name, barter_url', 'length', 'max'=>500),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, uin, activity_id, cost_flag, cost_money, favorable_flag, free_money, free_rebate, present_name, present_url, barter_money, barter_name, barter_url, batch_id, face_value', 'safe', 'on'=>'search'),
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
			'uin' => 'QQ',
			'activity_id' => '活动ID',
			'cost_flag' => '消费标记',
			'cost_money' => '满金额和件数',
			'favorable_flag' => '优惠标记',
			'free_money' => '减免金额',
			'free_rebate' => '扣费率',
			'present_name' => '赠品名称',
			'present_url' => '赠品图片地址',
			'barter_money' => '换购金额',
			'barter_name' => '换购商品名称',
			'barter_url' => '换购商品图地址',
			'batch_id' => '店铺优惠劵批次号',
			'face_value' => '店铺优惠劵面值',
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
		$criteria->compare('uin',$this->uin,true);
		$criteria->compare('activity_id',$this->activity_id,true);
		$criteria->compare('cost_flag',$this->cost_flag);
		$criteria->compare('cost_money',$this->cost_money);
		$criteria->compare('favorable_flag',$this->favorable_flag);
		$criteria->compare('free_money',$this->free_money);
		$criteria->compare('free_rebate',$this->free_rebate);
		$criteria->compare('present_name',$this->present_name,true);
		$criteria->compare('present_url',$this->present_url,true);
		$criteria->compare('barter_money',$this->barter_money);
		$criteria->compare('barter_name',$this->barter_name,true);
		$criteria->compare('barter_url',$this->barter_url,true);
		$criteria->compare('batch_id',$this->batch_id);
		$criteria->compare('face_value',$this->face_value);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ReachActivityContent the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
