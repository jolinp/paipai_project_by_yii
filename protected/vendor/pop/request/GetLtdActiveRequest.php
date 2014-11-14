<?php
namespace application\vendor\pop\request;

class GetLtdActiveRequest extends ApiRequest
{
    function getApiMethodName()
    {
        return '/yingxiao/getLtdActive.xhtml';
    }

    function getRelatedObj()
    {
        return 'application\modules\discount\models\tables\DiscountActivity';
    }

    function getRelatedTable()
    {
        return "discount_activity";
    }

    public function setActivityId($string)
    {
        $this->params['activityId'] = $string;
    }
}