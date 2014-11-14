<?php
namespace application\models\tables;

use application\components\ActiveRecord;

/**
 * 此对象为：授权
 * This is the model class for table "auth_sessions".
 *
 * The followings are the available columns in table 'auth_sessions':
 * @property string $uin
 * @property string $access_token
 * @property string $sign
 * @property string $create_time
 * @property string $refresh_time
 */
class AuthSession extends \ExtendedActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'auth_sessions';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('uin', 'required'),
            array('uin', 'length', 'max' => 20),
            array('access_token, sign', 'length', 'max' => 255),
            array('create_time, refresh_time', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('uin, access_token, sign, create_time, refresh_time', 'safe', 'on' => 'search'),
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
            'uin' => 'QQ号',
            'access_token' => 'AccessToken',
            'sign' => 'Sign',
            'create_time' => '创建时间',
            'refresh_time' => '刷新时间',
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

        $criteria = new CDbCriteria;

        $criteria->compare('uin', $this->uin, true);
        $criteria->compare('access_token', $this->access_token, true);
        $criteria->compare('sign', $this->sign, true);
        $criteria->compare('create_time', $this->create_time, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return AuthSession the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    protected function beforeSave()
    {
        $this->refresh_time = date('Y-m-d H:i:s');
        return parent::beforeSave();
    }

/*========================================================*/

    public function refresh()
    {
        $this->isNewRecord = $this->hasAccessToken() ? false : true;
        if (!$this->save()) {
            return $this->getErrorsText();
        }
        return true;
    }

    public function hasAccessToken()
    {
        return $this->exists("uin = '{$this->uin}'");
    }
}
