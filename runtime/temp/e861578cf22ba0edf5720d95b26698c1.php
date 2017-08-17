<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:56:"D:\phpStudy\WWW\Tplus/app/system\view\base\tip_info.html";i:1484208953;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>提示页面</title>
    <!--[if lt IE 9]>
    <meta http-equiv="refresh" content="0;ie.html" />
    <![endif]-->
    <link href="<?php echo $_css; ?>/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="<?php echo $_css; ?>/animate.min.css" rel="stylesheet">
    <link href="<?php echo $_css; ?>/style.min862f.css?v=4.1.0" rel="stylesheet">
    <script>if(window.top !== window.self){ window.top.location = window.location;}</script>
</head>

<body class="gray-bg">

<div class="lock-word animated fadeInDown">
</div>
<div class="middle-box text-center lockscreen animated fadeInDown">
    <div style="width:220px;">
        <?php switch ($code) {case 1:?>
        <div class="m-b-md">
            <img alt="image" class="img-circle circle-border" src="<?php echo $_img; ?>/ok.jpg">
        </div>
        <h3><?php echo(strip_tags($msg));?></h3>
        <?php break;case 0:?>
        <div class="m-b-md">
            <img alt="image" class="img-circle circle-border" src="<?php echo $_img; ?>/err.jpg">
        </div>
        <h3><?php echo(strip_tags($msg));?></h3>
        <?php break;} ?>


        <p>页面自动 <a id="href" href="<?php echo($url);?>">跳转</a> 等待时间： <b id="wait"><?php echo($wait);?></b></p>

        <a href="<?php echo($url);?>" class="btn btn-primary block full-width">回到首页</a>
    </div>
</div>
<script type="text/javascript">
    (function(){
        var wait = document.getElementById('wait'),
                href = document.getElementById('href').href;
        var interval = setInterval(function(){
            var time = --wait.innerHTML;
            if(time <= 0) {
                location.href = href;
                clearInterval(interval);
            };
        }, 1000);
    })();
</script>
</body>
</html>
