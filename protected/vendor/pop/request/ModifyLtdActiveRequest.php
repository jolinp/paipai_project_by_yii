<?php
namespace application\vendor\pop\request;

class ModifyLtdActiveRequest extends ApiRequest
{

    function getApiMethodName()
    {
        return "/yingxiao/modifyLtdActive.xhtml";
    }

    function getRelatedObj()
    {
        // TODO: Implement getRelatedObj() method.
    }

    function getRelatedTable()
    {
        // TODO: Implement getRelatedTable() method.
    }

    public function setActivityId($string)
    {
        $this->params['activityId'] = $string;
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