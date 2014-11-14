<div class="item-view">
    <div class="item-actived">点击图片选择</div>
    <div class="item-selected">已选择</div>
    <div class="item-added">已有其它折扣</div>
    <div class="item-pad">
        <?php echo CHtml::image($data->pic_link.".3.jpg", '', array('class' => 'item-pic','id'=>$data->item_code));?>
    </div>
    <span class="item-name"><?php echo $data->item_name;?></span>
</div>