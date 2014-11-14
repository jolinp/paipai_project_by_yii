<?php
namespace application\modules\discount\models\tables;

use application\components\ActiveRecord;

/**
 * This is the model class for table "discount_item".
 *
 * The followings are the available columns in table 'discount_item':
 * @property string $s_item_id
 * @property string $activity_id
 * @property integer $dw_buy_limit
 * @property integer $dw_item_discount
 */
class GetDiscountItem extends ActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'discount_item';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('s_item_id', 'required'),
            array('dw_buy_limit, dw_item_discount', 'numerical', 'integerOnly' => true),
            array('s_item_id, activity_id', 'length', 'max' => 60),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('s_item_id, activity_id, dw_buy_limit, dw_item_discount', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            's_item_id' => 'S Item',
            'activity_id' => 'Activity',
            'dw_buy_limit' => 'Dw Buy Limit',
            'dw_item_discount' => 'Dw Item Discount',
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

        $criteria = new \CDbCriteria;

        $criteria->compare('s_item_id', $this->s_item_id, true);
        $criteria->compare('activity_id', $this->activity_id, true);
        $criteria->compare('dw_buy_limit', $this->dw_buy_limit);
        $criteria->compare('dw_item_discount', $this->dw_item_discount);

        return new \CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return GetDiscountItem the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    protected function beforeSave()
    {
        $this->api_time = date('Y-m-d H:i:s');
        return parent::beforeSave();
    }

    function findUniqueRecord($apiResponse)
    {
        $model = $this->find("s_item_id = '{$apiResponse["s_item_id"]}'");
        $object = empty($model) ? new self : $model;
        return $object;
    }
}
