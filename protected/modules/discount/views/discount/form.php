<?php
echo $form;
?>
<script src="js/lib/jquery/jquery.datetimepicker.js"></script>
<script type="text/javascript"\>
	$('#application_modules_discount_models_tables_DiscountActivity_begin_time, #application_modules_discount_models_tables_DiscountActivity_end_time').datetimepicker({
		lang:'ch',
		step:5,
		format:'Y-m-d H:i:s',
	});
	
</script>