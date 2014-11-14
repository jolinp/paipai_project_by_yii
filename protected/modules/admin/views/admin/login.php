<style>

    *{
        margin: 0;
        padding: 0;
    }

        /*===制作login表单面板的样式==*/
    #login{
        background-color: #fff;
        background-image: -webkit-gradient(linear, left top, left bottom, from(#fff), to(#eee));
        background-image: -webkit-linear-gradient(top, #fff, #eee);
        background-image: -moz-linear-gradient(top, #fff, #eee);
        background-image: -ms-linear-gradient(top, #fff, #eee);
        background-image: -o-linear-gradient(top, #fff, #eee);
        background-image: linear-gradient(top, #fff, #eee);
        filter: progid:DXImageTransform.Microsoft.gradient(GradientType=0, startColorstr=#fff, endColorstr=#eee);
        -ms-filter: "progid:DXImageTransform.Microsoft.gradient (GradientType=0, startColorstr=#fff, endColorstr=#eee)";
        height: 240px;
        width: 400px;
        margin: -150px 0 0 -230px;
        padding: 30px;
        position: absolute;
        top: 50%;
        left: 50%;
        z-index: 0;
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
        border-radius: 3px;
        -webkit-box-shadow:0 0 2px rgba(0, 0, 0, 0.2),0 1px 1px rgba(0, 0, 0, .2),0 3px 0 #fff,0 4px 0 rgba(0, 0, 0, .2),0 6px 0 #fff,0 7px 0 rgba(0, 0, 0, .2);
        -moz-box-shadow:0 0 2px rgba(0, 0, 0, 0.2),0 1px 1px rgba(0, 0, 0, .2),0 3px 0 #fff,0 4px 0 rgba(0, 0, 0, .2),0 6px 0 #fff,0 7px 0 rgba(0, 0, 0, .2);
        box-shadow:0 0 2px rgba(0, 0, 0, 0.2),0 1px 1px rgba(0, 0, 0, .2),0 3px 0 #fff,0 4px 0 rgba(0, 0, 0, .2),0 6px 0 #fff,0 7px 0 rgba(0, 0, 0, .2);
    }
    #login:before {
        content: '';
        position: absolute;
        z-index: -1;
        border: 1px dashed #ccc;
        top: 5px;
        bottom: 5px;
        left: 5px;
        right: 5px;
        -moz-box-shadow: 0 0 0 1px #fff;
        -webkit-box-shadow: 0 0 0 1px #fff;
        box-shadow: 0 0 0 1px #fff;
    }
        /*--------------------*/
    fieldset	{
        border: 0;
        padding: 0;
        margin: 0;
    }
        /*＝＝＝制作Input输入框效果＝＝＝*/
    #inputs input{
        background: #f1f1f1 url(http://www.red-team-design.com/wp-content/uploads/2011/09/login-sprite.png) no-repeat;
        padding: 15px 15px 15px 30px;
        margin: 0 0 10px 0;
        width: 353px; /* 353 + 2 + 45 = 400 */
        border: 1px solid #ccc;
        -moz-border-radius: 5px;
        -webkit-border-radius: 5px;
        border-radius: 5px;
        -moz-box-shadow: 0 1px 1px #ccc inset, 0 1px 0 #fff;
        -webkit-box-shadow: 0 1px 1px #ccc inset, 0 1px 0 #fff;
        box-shadow: 0 1px 1px #ccc inset, 0 1px 0 #fff;
    }
    #username	{
        background-position: 5px -2px !important;
    }
    #password	{
        background-position: 5px -52px !important;
    }
    #inputs input:focus	{
        background-color: #fff;
        border-color: #e8c291;
        outline: none;
        -moz-box-shadow: 0 0 0 1px #e8c291 inset;
        -webkit-box-shadow: 0 0 0 1px #e8c291 inset;
        box-shadow: 0 0 0 1px #e8c291 inset;
    }
        /*＝＝制作Buttons效果＝＝*/
    #actions{
        margin: 25px 0 0 0;
    }
    #submit	{
        background-color: #ffb94b;
        background-image: -webkit-gradient(linear, left top, left bottom, from(#fddb6f), to(#ffb94b));
        background-image: -webkit-linear-gradient(top, #fddb6f, #ffb94b);
        background-image: -moz-linear-gradient(top, #fddb6f, #ffb94b);
        background-image: -ms-linear-gradient(top, #fddb6f, #ffb94b);
        background-image: -o-linear-gradient(top, #fddb6f, #ffb94b);
        background-image: linear-gradient(top, #fddb6f, #ffb94b);
        filter: progid:DXImageTransform.Microsoft.gradient(GradientType=0, startColorstr=#fddb6f, endColorstr=#ffb94b);
        -ms-filter: "progid:DXImageTransform.Microsoft.gradient (GradientType=0, startColorstr=#fddb6f, endColorstr=#ffb94b)";
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
        border-radius: 3px;
        text-shadow: 0 1px 0 rgba(255,255,255,0.5);
        -moz-box-shadow: 0 0 1px rgba(0, 0, 0, 0.3), 0 1px 0 rgba(255, 255, 255, 0.3) inset;
        -webkit-box-shadow: 0 0 1px rgba(0, 0, 0, 0.3), 0 1px 0 rgba(255, 255, 255, 0.3) inset;
        box-shadow: 0 0 1px rgba(0, 0, 0, 0.3), 0 1px 0 rgba(255, 255, 255, 0.3) inset;
        border-width: 1px;
        border-style: solid;
        border-color: #d69e31 #e3a037 #d5982d #e3a037;
        float: center;
        height: 35px;
        padding: 0;
        width: 120px;
        cursor: pointer;
        font: bold 15px Arial, Helvetica;
        color: #8f5a0a;
    }
    #submit:hover,
    #submit:focus		{
        background-color: #fddb6f;
        background-image: -webkit-gradient(linear, left top, left bottom, from(#ffb94b), to(#fddb6f));
        background-image: -webkit-linear-gradient(top, #ffb94b, #fddb6f);
        background-image: -moz-linear-gradient(top, #ffb94b, #fddb6f);
        background-image: -ms-linear-gradient(top, #ffb94b, #fddb6f);
        background-image: -o-linear-gradient(top, #ffb94b, #fddb6f);
        background-image: linear-gradient(top, #ffb94b, #fddb6f);
        filter: progid:DXImageTransform.Microsoft.gradient(GradientType=0, startColorstr=#ffb94b, endColorstr=#fddb6f);
        -ms-filter: "progid:DXImageTransform.Microsoft.gradient (GradientType=0, startColorstr=#ffb94b, endColorstr=#fddb6f)";
    }
    #submit:active {
        outline: none;
        -moz-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.5) inset;
        -webkit-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.5) inset;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.5) inset;
    }
    #submit::-moz-focus-inner	{
        border: none;
    }
    #actions a	{
        color: #3151A2;
        float: right;
        line-height: 35px;
        margin-left: 10px;
    }
</style>

<div class="login-form">

<div class="iTxt">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'login-form',	
)); ?>


<!--    <h1>Log In</h1>-->
    <fieldset id="inputs">
        <input id="username" name="LoginForm[nick]" type="text" placeholder="用户名" autofocus required><br>
        <input id="password" name="LoginForm[screkey]" type="password" placeholder="密码" required>
    </fieldset>
    <fieldset id="actions">
        <input type="submit" id="submit" value="Log in">
    </fieldset>




<?php $this->endWidget(); ?>
</div>
</div><!-- form -->
