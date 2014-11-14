<?php
namespace application\modules\discount\controllers;

use application\modules\discount\models\tables\GetDiscountItem;
use application\modules\discount\models\tables\Items;
use application\modules\discount\services\command\ItemsCommandService;
use application\modules\discount\models\tables\SetDiscountItem;
use application\modules\discount\services\query\DiscountItemsQueryService;

class ItemController extends \ExtendedController
{
    public function actionList()
    {
        $item = new Items();
        $service = new DiscountItemsQueryService($item);
        $dataProvider = $service->queryAllItems();

        $this->render('list', array('dataProvider' => $dataProvider));
    }

    public function actionAdjust(SetDiscountItem $setDiscountItem)
    {
        try {
            $setDiscountItem->checkIsExist();
            $service = new ItemsCommandService($setDiscountItem);
            $result = $service->adjustItem();
            echo \CJSON::encode($result);
        } catch (\Exception $e) {

        }
    }

    public function actionReduce(SetDiscountItem $setDiscountItem)
    {
        try {
            $service = new ItemsCommandService($setDiscountItem);
            if ($service->reduceItem()) {
                if(GetDiscountItem::model()->deleteByPk($setDiscountItem->itemCode)){
                    echo \CJSON::encode(array('status'=> 'success'));
                }else{
                    echo \CJSON::encode(array('status'=> 'failure', 'msg' => '删除失败！'));
                }
            }else{
                echo \CJSON::encode(array('status'=> 'failure', 'msg' => 'API调用失败！'));
            };
        } catch (\Exception $e) {
            $e->getMessage();
        }
    }

    public function actionBatch_add()
    {
        $itemCodes = array_keys($_POST['items']);
        $item = new Items();
        $service = new DiscountItemsQueryService($item);
        $dataProvider = $service->queryItemsIn($itemCodes);

        $this->render('/discount/items_list', array('dataProvider' => $dataProvider));
    }
}