<?php
namespace application\modules\discount\models;

use application\components\ActiveRecord;

class Activity extends ActiveRecord
{
    public $beginTime;

    public $endTime;

    function findUniqueRecord($apiResponse){

    }
}