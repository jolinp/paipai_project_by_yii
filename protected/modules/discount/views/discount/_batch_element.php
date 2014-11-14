<script type="text/javascript">
    function batchAdjust(changeSelector, valueSelector) {
        $(changeSelector).change(function (e) {
            var value = $(changeSelector).val();
            $("#discount-items-grid tr :checkbox[checked='checked']").each(function (index, element) {
                $(this).parents("tr").find(valueSelector).val(value);
            });
        });
    }
	function batchSetItem(clickSelector,findSelector){
		$(clickSelector).click(function(e) {
            $("#discount-items-grid tr :checkbox[checked='checked']").each(function (index, element) {
                var saveButton = $(this).parents("tr").find(findSelector);
                setItemAll(saveButton);
            });
        });
	}
    $(document).ready(function (e) {
        batchAdjust("#batch-discount-value", ".discount-value");
        batchAdjust("#batch-buy-limit-value", ".limit-value");
		batchSetItem("#batch-item-adjust",".adjust");
		batchSetItem("#batch-item-reduce",".reduce");
    });
</script>
<div class="z-form-next">
<?php
$dataList = \application\modules\discount\services\query\DiscountActivityQueryService::getActivityList();
$sessionActivityId = \application\components\ActiveUser::getInstance()->getActivityId();
$activityId = isset($sessionActivityId) ? $sessionActivityId : '';
echo CHtml::label('当前活动为：', 'activity_name');
echo CHtml::dropDownList('activity_name', $activityId, $dataList, array(
    'id' => 'activity_name_list',
    'empty' => '——请选择活动——',
    'title' => Yii::app()->controller->createUrl('/discount/discount/change_id'),
));
echo CHtml::label('折扣：', 'batch_discount');
echo CHtml::textField('batch_discount', '', array('size' => 10, 'id' => 'batch-discount-value'));
echo CHtml::label('限购数：', 'batch_buy_limit');
echo CHtml::textField('batch_buy_limit', '', array('size' => 10, 'id' => 'batch-buy-limit-value'));

echo CHtml::button('保存', array('id' => 'batch-item-adjust'));
echo CHtml::button('删除', array('id' => 'batch-item-reduce'));
?>
</div>