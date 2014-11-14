<?php
namespace application\vendor\pop\request;

class SetLtdItemRequest extends ApiRequest
{

    function getApiMethodName()
    {
        return '/yingxiao/setLtdItem.xhtml';
    }

    function getRelatedObj()
    {
        return 'application\modules\discount\models\tables\DiscountItem';
    }

    function getRelatedTable()
    {
        return 'discount_item';
    }

    public function setReqType($number)
    {
        $this->params['reqType'] = $number;
    }

    public function setActivityId($string)
    {
        $this->params['activityId'] = $string;
    }

    public function setItemCode($string)
    {
        $this->params['itemCode'] = $string;
    }

    public function setBuyLimit($number)
    {
        $this->params['buyLimit'] = $number;
    }

    public function setDiscount($number)
    {
        $this->params['discount'] = $number;
    }
}