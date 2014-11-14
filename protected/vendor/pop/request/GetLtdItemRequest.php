<?php
namespace application\vendor\pop\request;

class GetLtdItemRequest extends ApiRequest
{
    function getApiMethodName()
    {
        return "/yingxiao/getLtdItem.xhtml";
    }

    function getRelatedObj()
    {
        return 'application\modules\discount\models\tables\GetDiscountItem';
    }

    function getRelatedTable()
    {
        return 'discount_item';
    }

    public function setActivityId($string)
    {
        $this->params['activityId'] = $string;
    }
}