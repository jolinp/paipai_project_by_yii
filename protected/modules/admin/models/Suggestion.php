<?php

/**
 * This is the model class for table "suggestion".
 *
 * The followings are the available columns in table 'suggestion':
 * @property string $seq
 * @property string $nick
 * @property string $status
 * @property string $action_time
 * @property string $action
 * @property string $message
 * @property string $entry_time
 */
class Suggestion extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Suggestion the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'suggestion';
    }

    public function getStatusOptions()
    {
        return array(
            'init' => '初始',
            'accept' => '采纳',
            'reject' => '拒绝',
            'ignore' => '忽略',
            'finish' => '完成',
        );
    }

    public function getStatusText()
    {
        $statusOptions = $this->getStatusOptions();
        return isset($statusOptions[$this->status]) ?
            $statusOptions[$this->status] :
            "unknown status ({$this->status})";
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('nick', 'length', 'max' => 50),
            array('status', 'length', 'max' => 6),
            array('action', 'length', 'max' => 100),
            array('action_time, message, entry_time', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('seq, nick, status, action_time, action, message, entry_time', 'safe', 'on' => 'search'),
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
            'seq' => '序号',
            'nick' => '店铺名',
            'status' => '状态',
            'action_time' => '操作时间',
            'action' => 'Action',
            'message' => '建议',
            'entry_time' => '录入时间',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('seq', $this->seq, true);
        $criteria->compare('nick', $this->nick, true);
        $criteria->compare('status', $this->status, true);
        $criteria->compare('action_time', $this->action_time, true);
        $criteria->compare('action', $this->action, true);
        $criteria->compare('message', $this->message, true);
        $criteria->compare('entry_time', $this->entry_time, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }
}