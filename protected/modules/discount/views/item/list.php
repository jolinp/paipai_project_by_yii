<script type="text/javascript">
    $(document).ready(function () {
        $("#step-2").click(function (e) {
            var itemObject = $("body").data("items");
			if(itemObject==null || $.isEmptyObject(itemObject)){
				
				zDialog.alert({
					width:400,
					height:160,
					shade: true,
					message: '请选择宝贝!',
					title: '警告'
				});
				return false;
			}else{
				leftMenu.reNewPost(
					leftMenu.selector,
					"<?php echo Yii::app()->controller->createUrl('/discount/item/batch_add'); ?>",
					{items: itemObject, YII_CSRF_TOKEN: "<?php echo \Yii::app()->request->csrfToken;?>"}
				);
			};
        });
    });
</script>
<div class="z-next-btn">
    <?php echo CHtml::button('下一步', array('id' => 'step-2'));?>
</div>
<?php
$this->widget('zii.widgets.CListView', array(
    'id' => 'items-list-view',
    'dataProvider' => $dataProvider,
    'template' => '{summary}{pager}{items} ',
    'itemView' => '_item',
    'afterAjaxUpdate' => 'afterPageItemsList',
));
?>
