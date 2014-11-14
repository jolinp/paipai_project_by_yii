<?php
namespace application\components;

use CActiveRecord;
use Yii;

abstract class ActiveRecord extends \ExtendedActiveRecord
{
    public static function model($className = null)
    {
        if (is_null($className))
            $className = get_called_class();
        return parent::model($className);
    }

    abstract function findUniqueRecord($apiResponse);
}
