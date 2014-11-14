<?php
namespace application\models\tables;

use application\components\ActiveRecord;
/**
 * This is the model class for table "items".
 *
 * The followings are the available columns in table 'items':
 * @property string $seller_uin
 * @property string $seller_name
 * @property string $item_code
 * @property string $item_name
 * @property string $pic_link
 * @property string $item_state
 * @property string $state_desc
 * @property string $properties
 * @property string $item_price
 * @property string $market_price
 * @property string $category_id
 * @property integer $class_id
 * @property string $create_time
 * @property string $last_modify_time
 * @property integer $visit_count
 * @property integer $sold_total_count
 * @property integer $total_buy_count
 * @property integer $sold_total_times
 * @property integer $seller_pay_freight
 * @property string $api_time
 */
class DItems extends \ExtendedActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'items';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('item_code', 'required'),
			array('class_id, visit_count, sold_total_count, total_buy_count, sold_total_times, seller_pay_freight', 'numerical', 'integerOnly'=>true),
			array('seller_uin, item_state', 'length', 'max'=>20),
			array('seller_name, item_code, item_name, category_id', 'length', 'max'=>60),
			array('pic_link', 'length', 'max'=>500),
			array('state_desc, properties', 'length', 'max'=>250),
			array('item_price, market_price', 'length', 'max'=>10),
			array('create_time, last_modify_time, api_time', 'safe'),
			array('seller_uin, seller_name, item_code, item_name, pic_link, item_state, state_desc, properties, item_price, market_price, category_id, class_id, create_time, last_modify_time, visit_count, sold_total_count, total_buy_count, sold_total_times, seller_pay_freight, api_time', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		);
	}

	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('seller_uin',$this->seller_uin,true);
		$criteria->compare('seller_name',$this->seller_name,true);
		$criteria->compare('item_code',$this->item_code,true);
		$criteria->compare('item_name',$this->item_name,true);
		$criteria->compare('pic_link',$this->pic_link,true);
		$criteria->compare('item_state',$this->item_state,true);
		$criteria->compare('state_desc',$this->state_desc,true);
		$criteria->compare('properties',$this->properties,true);
		$criteria->compare('item_price',$this->item_price,true);
		$criteria->compare('market_price',$this->market_price,true);
		$criteria->compare('category_id',$this->category_id,true);
		$criteria->compare('class_id',$this->class_id);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('last_modify_time',$this->last_modify_time,true);
		$criteria->compare('visit_count',$this->visit_count);
		$criteria->compare('sold_total_count',$this->sold_total_count);
		$criteria->compare('total_buy_count',$this->total_buy_count);
		$criteria->compare('sold_total_times',$this->sold_total_times);
		$criteria->compare('seller_pay_freight',$this->seller_pay_freight);
		$criteria->compare('api_time',$this->api_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Items the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    protected function beforeSave()
    {
        $this->api_time = date('Y-m-d');
        return parent::beforeSave();
    }
}
