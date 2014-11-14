<!doctype html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <link rel="icon" href="favicon.ico" type="image/x-icon"/>
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
    <link rel="stylesheet" type="text/css" href="css/base.css">
    <link rel="stylesheet" type="text/css" href="css/layout.css">
    <link rel="stylesheet" type="text/css" href="themes/default/default.css">
    <link rel="stylesheet" type="text/css" href="themes/default/style.css">
    <link rel="stylesheet" type="text/css" href="js/lib/jquery/jquery.datetimepicker.css">
    <link rel="stylesheet" type="text/css" href="js/lib/zDialog/themes/blue.css">
    <?php
    $cs = Yii::app()->clientScript;
    $cs->registerCoreScript('jquery');
    ?>
    <!--[if lt IE 9]>
    <script type="text/javascript" src="js/lib/html5.js"></script>
    <![endif]-->
    <!--[if IE 6]>
    <script type="text/javascript" src="js/lib/others/DD_belatedPNG.js"></script>
    <script type="text/javascript">
        DD_belatedPNG.fix('.da-mai img, .logo, .header_right, .tab-link, .create-active, em');
    </script>
    <![endif]-->
</head>
<body>
<header class="header">
    <div class="logo">
        <a class="da-mai" href="http://www.da-mai.com" target="_blank"><img src="themes/default/images/logo.png"></a>
    </div>
    <div class="header_right"></div>
    <div class="user-info-box">
        <ul class="user-info clearfix">
            <li class="user-li">
                <a class="user-link"><em class="z-icon z-help"></em>帮助</a>
            </li>
            <li class="user-li">
                <a class="user-link" href="<?php echo Yii::app()->controller->createUrl('auth/logout') ?>"><em
                        class="z-icon z-exit"></em>退出</a>
            </li>
            <li class="user-li">
                <a class="user-link"><em class="z-icon z-user"></em>
                    <?php
                    $user = \application\components\ActiveUser::getInstance();
                    echo $user->getUin();
                    ?>
                </a>
            </li>
        </ul>
    </div>
</header>

<section class="top-menu clearfix">
    <div class="top-left">
        <div class="date"><em class="z-icon z-date"></em>2014年01月14日 星期二</div>
    </div>
    <div class="top-right">
        <ul class="tab">
            <!--<li><a class="tab-link">进行中的<span class="red">(2)</span></a></li>
            <li><a class="tab-link">未开始的<span class="red">(2)</span></a></li>-->
            <li><a class="tab-link tab-active">全部活动<!--<span class="red">(6)</span>--></a></li>
        </ul>
        <a id="download-data" class="create-active"
           title="<?php echo Yii::app()->controller->createUrl('/discount/init/init_all'); ?>"><em
                class="z-icon z-retweet"></em>初始化数据</a>
    </div>
</section>

<section id="main" class="main clearfix">
    <section id="right" class="right">
        <div class="breadcrumb">
            <div class="selecter-all">
                <span class="checker select-all"><input type="checkbox" name="checkRow" class="opacity0"></span>
            </div>
            <div class="breadcrumb-text">
                <span>全部活动</span>
            </div>
        </div>
        <div id="right-content"> <?php echo $content; ?> </div>
        <!--<table class="list" cellpadding="0" border="0">
            <thead>
                <tr>
                    <th width="45"><img src="themes/default/images/tableArrows.png"></th>
                    <th>活动名称</th>
                    <th>活动时间</th>
                    <th>奖励</th>
                    <th>游戏</th>
                    <th>创建时间</th>
                    <th>状态</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <span class="checker"><input type="checkbox" name="checkRow" class="opacity0"></span>
                    </td>
                    <td><div class="td" title="活动名称：喜迎新春">喜迎新春</div></td>
                    <td><div class="td limit" title="开始时间：2014/01/30 00:00&#10结束时间：2014/02/16 00:00">2014/01/30 00:00-2014/02/16 00:00</div></td>
                    <td><div class="td" title="奖励：优惠券">优惠券</div></td>
                    <td><div class="td" title="游戏：大家来找茬">大家来找茬</div></td>
                    <td><div class="td" title="创建时间：2014/01/10">2014/01/10</div></td>
                    <td><div class="td" title="状态：未开始">未开始</div></td>
                </tr>
                <tr>
                    <td>
                        <span class="checker"><input type="checkbox" name="checkRow" class="opacity0"></span>
                    </td>
                    <td><div class="td" title="活动名称：喜迎新春">喜迎新春</div></td>
                    <td><div class="td limit" title="开始时间：2014/01/30 00:00&#10结束时间：2014/02/16 00:00">2014/01/30 00:00-2014/02/16 00:00</div></td>
                    <td><div class="td" title="奖励：优惠券">优惠券</div></td>
                    <td><div class="td" title="游戏：大家来找茬">大家来找茬</div></td>
                    <td><div class="td" title="创建时间：2014/01/10">2014/01/10</div></td>
                    <td><div class="td" title="状态：未开始">未开始</div></td>
                </tr>
                <tr>
                    <td>
                        <span class="checker"><input type="checkbox" name="checkRow" class="opacity0"></span>
                    </td>
                    <td><div class="td" title="活动名称：喜迎新春">喜迎新春</div></td>
                    <td><div class="td limit" title="开始时间：2014/01/30 00:00&#10结束时间：2014/02/16 00:00">2014/01/30 00:00-2014/02/16 00:00</div></td>
                    <td><div class="td" title="奖励：优惠券">优惠券</div></td>
                    <td><div class="td" title="游戏：大家来找茬">大家来找茬</div></td>
                    <td><div class="td" title="创建时间：2014/01/10">2014/01/10</div></td>
                    <td><div class="td" title="状态：未开始">未开始</div></td>
                </tr>
                <tr>
                    <td>
                        <span class="checker"><input type="checkbox" name="checkRow" class="opacity0"></span>
                    </td>
                    <td><div class="td" title="活动名称：喜迎新春">喜迎新春</div></td>
                    <td><div class="td limit" title="开始时间：2014/01/30 00:00&#10结束时间：2014/02/16 00:00">2014/01/30 00:00-2014/02/16 00:00</div></td>
                    <td><div class="td" title="奖励：优惠券">优惠券</div></td>
                    <td><div class="td" title="游戏：大家来找茬">大家来找茬</div></td>
                    <td><div class="td" title="创建时间：2014/01/10">2014/01/10</div></td>
                    <td><div class="td" title="状态：未开始">未开始</div></td>
                </tr>
                <tr>
                    <td>
                        <span class="checker"><input type="checkbox" name="checkRow" class="opacity0"></span>
                    </td>
                    <td><div class="td" title="活动名称：喜迎新春">喜迎新春</div></td>
                    <td><div class="td limit" title="开始时间：2014/01/30 00:00&#10结束时间：2014/02/16 00:00">2014/01/30 00:00-2014/02/16 00:00</div></td>
                    <td><div class="td" title="奖励：优惠券">优惠券</div></td>
                    <td><div class="td" title="游戏：大家来找茬">大家来找茬</div></td>
                    <td><div class="td" title="创建时间：2014/01/10">2014/01/10</div></td>
                    <td><div class="td" title="状态：未开始">未开始</div></td>
                </tr>
                <tr>
                    <td>
                        <span class="checker"><input type="checkbox" name="checkRow" class="opacity0"></span>
                    </td>
                    <td><div class="td" title="活动名称：喜迎新春">喜迎新春</div></td>
                    <td><div class="td limit" title="开始时间：2014/01/30 00:00&#10结束时间：2014/02/16 00:00">2014/01/30 00:00-2014/02/16 00:00</div></td>
                    <td><div class="td" title="奖励：优惠券">优惠券</div></td>
                    <td><div class="td" title="游戏：大家来找茬">大家来找茬</div></td>
                    <td><div class="td" title="创建时间：2014/01/10">2014/01/10</div></td>
                    <td><div class="td" title="状态：未开始">未开始</div></td>
                </tr>
            </tbody>
        </table>-->
    </section>
    <aside id="left" class="left">
        <ul class="menu">
            <li><a name="left-menu" class="link link-active" href="javascript:;"
                   url="<?php echo $this->createUrl("/discount/discount/list"); ?>"><em
                        class="z-icon z-dot"></em>管理活动</a></li>
            <li><a name="left-menu" class="link" href="javascript:;"
                   url="<?php echo $this->createUrl("/discount/discount/form"); ?>"><em
                        class="z-icon z-dot"></em>新建活动</a></li>
            <li><a name="left-menu" class="link" href="javascript:;"
                   url="<?php echo $this->createUrl("/discount/item/list"); ?>"><em class="z-icon z-dot"></em>管理宝贝</a>
            </li>
        </ul>
    </aside>
</section>

<footer class="footer">
    <span class="copy">&copy;2014  广州大麦信息科技有限公司版权所有</span>
</footer>

<script type="text/javascript" src="js/lib/zDialog/zDialog.js"></script>
<script type="text/javascript">

    $Global = {};
    $Global.urls = {
        baseUrl: '<?php echo Yii::app()->baseUrl;?>',
        index: '<?php echo $this->createUrl("/discount/discount/list");?>'
    };

    function loadScript(url, callback) {
        var script = document.createElement('script');
        script.type = 'text/javascript';

        if (script.readyState) {	//IE
            script.onreadystatechange = function () {
                if (script.readyState == 'loaded' ||
                    script.readyState == 'complete') {
                    script.onreadystatechange = null;
                    callback();
                }
                ;
            };
        } else {
            script.onload = function () {
                callback();
            };
        }
        ;

        script.src = url;
        document.getElementsByTagName('head')[0].appendChild(script);
    }
    ;

    var initialize;
    loadScript('js/app/config.js', function () {
        initialize = new Initialize();
    });
    /*loadScript('js/lib/jquery/jquery-1.10.2.min.js', function(){
     loadScript('js/app/config.js', function(){
     var initialize = new Initialize();
     });
     }); */
</script>
</body>
</html>
