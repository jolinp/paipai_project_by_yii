<?php
namespace application\modules\discount\models\tables;

class SetDiscountItem extends \CFormModel
{
    public $reqType;

    public $activityId;

    public $itemCode;

    public $buyLimit;

    public $discount;

    public function rules()
    {
        return array(
            array('reqType, activityId, itemCode, buyLimit, discount', 'safe'),
        );
    }

    public function checkIsExist()
    {
        $exist = GetDiscountItem::model()->exists('s_item_id = ? AND activity_id = ?', array($this->itemCode, $this->activityId));
        if ($exist) {
            $this->reqType = 3;
        } else {
            $this->reqType = 1;
        }
    }
}
