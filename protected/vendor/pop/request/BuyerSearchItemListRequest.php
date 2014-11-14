<?php
namespace application\vendor\pop\request;

use application\vendor\pop\request\ApiRequest;

class BuyerSearchItemListRequest extends ApiRequest
{
    function getApiMethodName()
    {
        return '/item/buyerSearchItemList.xhtml';
    }

    function getRelatedObj()
    {
        return 'application\modules\discount\models\tables\Items';
    }

    function getRelatedTable()
    {
        return 'items';
    }

    public function setSellerUin($number)
    {
        $this->params['sellerUin'] = $number;
    }

    public function setItemState($number)
    {
        $this->params['itemState'] = $number;
    }

    public function setPageIndex($number)
    {
        $this->params['pageIndex'] = $number;
    }

    public function setPageSize($number)
    {
        $this->params['pageSize'] = $number;
    }
}