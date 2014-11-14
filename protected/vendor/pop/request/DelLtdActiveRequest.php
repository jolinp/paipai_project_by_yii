<?php
namespace application\vendor\pop\request;

class DelLtdActiveRequest extends ApiRequest
{

    function getApiMethodName()
    {
        return "/yingxiao/delLtdActive.xhtml";
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
}