<?php /* @var $this Controller */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <link rel="icon" href="favicon.ico" type="image/x-icon"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
    <link rel="stylesheet" type="text/css" href="css/base.css">
    <link rel="stylesheet" type="text/css" href="css/layout.css">
    <link rel="stylesheet" type="text/css" href="themes/default/default.css">
    <link rel="stylesheet" type="text/css" href="themes/default/style.css">
    <?php
    $cs = Yii::app()->clientScript;
    $cs->registerCoreScript('jquery');
    ?>
    <!--[if lt IE 9]>
    <script type="text/javascript" src="js/lib/html5.js"></script>
    <![endif]-->

    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>
<?php echo $content;?>

<!--script type="text/javascript" src="js/lib/jquery/jquery-1.10.2.min.js"></script-->
<script type="text/javascript" src="js/app/config.js" defer async></script>
<script type="text/javascript" src="js/lib/plugs.js" defer async></script>
<script type="text/javascript">

    $Global = {};
    $Global.urls = {
        baseUrl: '<?php echo Yii::app()->baseUrl;?>',
        index: '<?php echo $this->createUrl("/discount/discount/form");?>'
    };
    $Global.activeId = 11111;

</script>
<script type="text/javascript" src="js/app/app.js" defer async></script>

</body>
</html>

