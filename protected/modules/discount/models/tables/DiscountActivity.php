<?php
namespace application\modules\discount\models\tables;

use application\modules\discount\models\Activity;

/**
 * This is the model class for table "dicount_activity".
 *
 * The followings are the available columns in table 'dicount_activity':
 * @property integer $id
 * @property string $seller_uin
 * @property string $begin_time
 * @property string $end_time
 * @property string $activity_name
 * @property string $create_time
 * @property string $api_time
 * @property string $item_num
 * @property string $activity_id
 */
class DiscountActivity extends Activity
{
    public function tableName()
    {
        return 'discount_activity';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('activity_name, begin_time, end_time', 'required'),
            array('activity_id, item_num', 'numerical', 'integerOnly' => true),
            array('seller_uin', 'length', 'max' => 20),
            array('activity_name', 'length', 'max' => 300),
            array('begin_time, end_time, create_time, api_time', 'safe'),
            array('id, seller_uin, begin_time, end_time, activity_name, create_time, api_time', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'seller_uin' => 'QQ号',
            'begin_time' => '开始时间',
            'end_time' => '结束时间',
            'activity_name' => '活动名称',
            'create_time' => '创建时间',
            'api_time' => 'API更新时间',
            'item_num' => '宝贝个数',
            'activity_id' => '活动编号',
        );
    }

    public function search()
    {
        $criteria = new \CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->addCondition("seller_uin = '{$this->seller_uin}'");
        $criteria->compare('begin_time', $this->begin_time, true);
        $criteria->compare('end_time', $this->end_time, true);
        $criteria->compare('activity_name', $this->activity_name, true);
        $criteria->compare('create_time', $this->create_time, true);
        $criteria->compare('api_time', $this->api_time, true);

        return new \CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    protected function beforeSave()
    {
        $this->api_time = date('Y-m-d H:s:i');
        return parent::beforeSave();
    }

    public function findUniqueRecord($apiResponse)
    {
        $model = $this->find("seller_uin = '{$apiResponse["seller_uin"]}' AND activity_id = '{$apiResponse["activity_id"]}'");
        $object = empty($model) ? new self : $model;
        return $object;
    }

}
