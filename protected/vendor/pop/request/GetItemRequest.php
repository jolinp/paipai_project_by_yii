<?php
namespace application\vendor\pop\request;

class GetItemRequest extends ApiRequest
{
    //public $params = array();

    function getApiMethodName()
    {
        return '/item/getItem.xhtml';
    }

    function getRelatedObj()
    {
        return 'application\modules\discount\models\tables\Items';
    }

    function getRelatedTable()
    {
        return 'items';
    }

    public function setItemCode($string)
    {
        $this->params['itemCode'] = $string;
    }

    public function setItemLocalCode($string)
    {
        $this->params['itemLocalCode'] = $string;
    }

    public function setPureData($number)
    {
        $this->params['pureData'] = $number;
    }
}