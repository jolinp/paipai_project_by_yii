<?php
namespace application\modules\discount\services;

use application\behaviors\InvokeApiBehavior;
use application\components\ActiveRecord;

class CommonService extends \CComponent
{
    protected $_params = array();

    protected $_model;

    public static function getUin()
    {
        return \Yii::app()->session->get('ActiveUser');
    }

    public function getAccessToken()
    {
        return \Yii::app()->session->get('AccessToken');
    }

    public function __construct(\CModel $model)
    {
        $this->_model = $model;
        $behavior = new InvokeApiBehavior();
        $this->attachBehavior('invokeApi', $behavior);
        $this->_params = $this->getParamsFrom($model);
    }

    protected function getParamsFrom(\CModel $model)
    {
        $new_attributes = array();
        foreach ($model->attributes as $key => $value) {
            $new_key = \fGrammar::camelize($key, true);
            $new_attributes[$new_key] = $value;
        }
        return $new_attributes;
    }

    protected function getModelAttribute($attributeName)
    {
        return $this->_model->{$attributeName};
    }
}