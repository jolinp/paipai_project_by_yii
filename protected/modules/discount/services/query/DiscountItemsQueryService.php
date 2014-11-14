<?php
namespace application\modules\discount\services\query;

use application\modules\discount\models\tables\GetDiscountItem;
use application\modules\discount\models\tables\Items;
use application\modules\discount\services\CommonService;

class DiscountItemsQueryService extends CommonService
{
    public function queryDiscountItems()
    {
        $data = array();
        $discountItems = GetDiscountItem::model()->findAll('activity_id = ' . $this->getModelAttribute('activity_id'));

        if (!empty($discountItems)) {
            foreach ($discountItems as $item) {
                $itemDetail = Items::model()->findByPk($item->s_item_id);
                $data[] = array(
                    'itemCode' => $itemDetail->item_code,
                    'itemName' => $itemDetail->item_name,
                    'picLink' => $itemDetail->pic_link,
                    'itemPrice' => round($itemDetail->item_price / 100, 2),
                    'buyLimit' => $item->dw_buy_limit,
                    'itemDiscount' => round($item->dw_item_discount / 1000, 2),
                    'discountPrice' => round($itemDetail->item_price * ($item->dw_item_discount / 10000) / 100, 2),
                );
            }
        }

        $dataProvider = new \CArrayDataProvider($data, array(
            'keyField' => 'itemCode',
            'sort' => array(
                'attributes' => array(
                    'itemCode', 'buyLimit', 'itemDiscount',
                ),
            ),
            'pagination' => array(
                'pageSize' => 10,
            ),
        ));

        return $dataProvider;
    }

    public function queryAllItems()
    {
        $criteria = new \CDbCriteria();
        $criteria->addCondition('seller_uin = ' . $this->getUin());

        return new \CActiveDataProvider('application\modules\discount\models\tables\Items', array(
            'criteria' => $criteria,
            'pagination' => array('pageSize' => 18),
        ));
    }

    public function queryItemsIn(array $itemCodes)
    {
        $data = array();
        $criteria = new \CDbCriteria();
        $criteria->addInCondition('item_code', $itemCodes);
        $items = Items::model()->findAll($criteria);

        if (!empty($items)) {
            foreach ($items as $item) {
                $data[] = array(
                    'itemCode' => $item->item_code,
                    'itemName' => $item->item_name,
                    'picLink' => $item->pic_link,
                    'itemPrice' => round($item->item_price / 100, 2),
                    'buyLimit' => 0,
                    'itemDiscount' => 0,
                    'discountPrice' => 0,
                );
            }
        }

        $dataProvider = new \CArrayDataProvider($data, array(
            'keyField' => 'itemCode',
            'sort' => array(
                'attributes' => array(
                    'itemCode', 'buyLimit', 'itemDiscount',
                ),
            ),
            'pagination' => false,
        ));

        return $dataProvider;
    }
}