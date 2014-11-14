<?php
namespace application\modules\discount\services\command;

use application\modules\discount\models\tables\GetDiscountItem;
use application\modules\discount\models\tables\Items;
use application\modules\discount\services\CommonService;
use application\behaviors\InvokeApiBehavior;

class ItemsCommandService extends CommonService
{
    public $isNewItem;

    public function __construct(\CModel $model)
    {
        parent::__construct($model);
    }

    public function init()
    {
        Items::model()->deleteAll('seller_uin = ' . $this->getUin());

        $totalNum = $this->downloadData(1);
        $pageCount = (int)(($totalNum + 30 - 1) / 30);
        for ($p = 2; $p <= $pageCount; $p++) {
            $this->downloadData($p);
        }
    }

    protected function downloadData($pageIndex)
    {
        $ApiClassName = 'SellerSearchItemList';
        $this->_params = array(
            'SellerUin' => $this->getUin(),
            'PageSize' => 30,
            'PageIndex' => $pageIndex,
        );
        $this->setRootName('itemList');
        $this->setAfterInvoke('batchToDb'); //下载后处理方式
        $this->invokeApi($ApiClassName, $this->_params);

        return $this->getApiResult('countTotal');
    }

    public function initForDiscount()
    {
        $this->deleteByActivityId();

        $ApiClassName = 'GetLtdItem';
        $this->setRootName('cBoLtdItems');
        $this->setAfterInvoke('batchToDb'); //下载后处理方式
        $this->invokeApi($ApiClassName, $this->_params);

        /*$result = $this->getApiResult();
        print_r($result);*/
    }

    public function adjustItem()
    {
        $ApiClassName = 'SetLtdItem';
        //$this->setAfterInvoke('SAVE_DB'); //下载后处理方式
        $this->invokeApi($ApiClassName, $this->_params);

        $result = $this->getApiResult();

        if(empty($result)){
            return array(
                'status' => 'success'
            );
        }else{
            return array(
                'status' => 'failure',
                'msg' => $result['errorCode'].$result['errorMessage']
            );
        };

    }

    public function reduceItem()
    {
        $ApiClassName = 'SetLtdItem';
        $this->_params['ReqType'] = 2;
        $this->invokeApi($ApiClassName, $this->_params);

        return $this->noErrors();
    }

    public function deleteByActivityId()
    {
        GetDiscountItem::model()->deleteAll('activity_id = ' . $this->getModelAttribute('activity_id'));
    }
}