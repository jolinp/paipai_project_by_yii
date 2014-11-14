<!doctype html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <title>大麦电商 - 通用软件界面模版</title>
</head>
<link rel="icon" href="favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
<link rel="stylesheet" type="text/css" href="css/base.css">
<link rel="stylesheet" type="text/css" href="css/layout.css">
<link rel="stylesheet" type="text/css" href="themes/default/default.css">
<!--[if lt IE 9]>
<script type="text/javascript" src="js/lib/html5.js"></script>
<![endif]-->
<body>
<header class="header">
    <div class="header_layout clearfix">
        <div class="logo"></div>
        <ul class="user-info">
            <li class="user-li">
                <a class="user-link"><em class="z-icon z-help"></em>帮助</a>
            </li>
            <li class="user-li">
                <a class="user-link"><em class="z-icon z-exit"></em>退出</a>
            </li>
            <li class="user-li">
                <a class="user-link"><em class="z-icon z-user"></em>测试帐号</a>
            </li>
        </ul>
    </div>
</header>

<section class="breadcrumbs clearfix">
    <div class="breadcrumbs-left">
        <div class="date">2014年01月14日 星期二</div>
        <div class="left-div">
            <div class="z-div"></div>
        </div>
    </div>
    <div class="breadcrumbs-right">
        <div class="breadcrumbs-tool"></div>
        <div class="breadcrumbs-bar"></div>
    </div>
</section>

<section id="main" class="main clearfix">
    <section class="right">
        <div id="right-content">
            <?php echo $content; ?>
        </div>

    </section>
    <aside id="left" class="left">
        <ul class="menu">
            <li><a name="left-menu" class="link link-active" href="javascript:;" url="<?php echo $this->createUrl("/discount/discount/form");?>">新建活动</a></li>
            <li><a name="left-menu" class="link" href="javascript:;" url="<?php echo $this->createUrl("/discount/discount/list");?>">管理活动</a></li>
            <li><a name="left-menu" class="link" href="javascript:;" url="<?php echo $this->createUrl("/discount/discount/items");?>">管理宝贝</a></li>
        </ul>
    </aside>
</section>

<footer class="footer">
    <span class="copy">&copy;2014  广州大麦信息科技有限公司版权所有</span>
</footer>

<script type="text/javascript" src="js/lib/jquery/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="js/app/config.js" defer async></script>
<script type="text/javascript" src="js/lib/plugs.js" defer async></script>
<script type="text/javascript">

    $Global = {};
    $Global.urls = {
        baseUrl:'<?php echo Yii::app()->baseUrl;?>',
        index:'<?php echo $this->createUrl("/discount/discount/form");?>'
    };

</script>
<script type="text/javascript" src="js/app/app.js" defer async></script>

</body>
</html>
