<?php
namespace application\modules\discount\services\query;

use application\modules\discount\models\tables\DiscountActivity;
use application\modules\discount\services\CommonService;

class DiscountActivityQueryService extends CommonService
{
    public static function getActivityList()
    {
        $model = DiscountActivity::model()->findAll('seller_uin = ' . self::getUin());
        if(!empty($model)){
            $data = \CHtml::listData($model,'activity_id','activity_name');
        }else{
            $data = array();
        }

        return $data;
    }
}