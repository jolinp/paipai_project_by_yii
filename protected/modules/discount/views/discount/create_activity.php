<form method="post" action="/paipai/public/index.php?r=discount/discount/create" id="activity-form">
  <fieldset>
    <legend>表单</legend>
    <div class="row field_activity_name">
      <label class="required" for="application_modules_discount_models_tables_DiscountActivity_activity_name">活动名称 <span class="required">*</span></label>
      <input type="text" maxlength="300" id="application_modules_discount_models_tables_DiscountActivity_activity_name" name="application_modules_discount_models_tables_DiscountActivity[activity_name]" class="activity_name">
    </div>
    <div class="row field_begin_time">
      <label class="required" for="application_modules_discount_models_tables_DiscountActivity_begin_time">开始时间 <span class="required">*</span></label>
      <input type="text" id="application_modules_discount_models_tables_DiscountActivity_begin_time" name="application_modules_discount_models_tables_DiscountActivity[begin_time]" class="begin_time">
    </div>
    <div class="row field_end_time">
      <label class="required" for="application_modules_discount_models_tables_DiscountActivity_end_time">结束时间 <span class="required">*</span></label>
      <input type="text" id="application_modules_discount_models_tables_DiscountActivity_end_time" name="application_modules_discount_models_tables_DiscountActivity[end_time]" class="end_time">
    </div>
    <div class="row buttons">
      <input type="submit" value="提交" name="login">
    </div>
  </fieldset>
</form>
<script src="js/lib/jquery/jquery.datetimepicker.js"></script>
<script type="text/javascript"\>
	$('#application_modules_discount_models_tables_DiscountActivity_begin_time, #application_modules_discount_models_tables_DiscountActivity_end_time').datetimepicker({
		lang:'ch',
		step:5,
		format:'Y-m-d H:i:s',
	});
	
</script>