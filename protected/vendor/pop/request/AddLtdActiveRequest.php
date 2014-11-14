<?php
namespace application\vendor\pop\request;

class AddLtdActiveRequest extends ApiRequest
{
    function getApiMethodName()
    {
        return "/yingxiao/addLtdActive.xhtml";
    }

    function getRelatedObj()
    {
        return 'application\modules\discount\models\tables\DiscountActivity';
    }

    function getRelatedTable()
    {
        return "discount_activity";
    }

    public function setBeginTime($string)
    {
        $this->params['beginTime'] = $string;
    }

    public function setEndTime($string)
    {
        $this->params['endTime'] = $string;
    }

    public function setActivityName($string)
    {
        $this->params['activityName'] = $string;
    }
}