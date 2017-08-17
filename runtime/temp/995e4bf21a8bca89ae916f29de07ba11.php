<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:53:"D:\phpStudy\WWW\Tplus/app/system\view\index\main.html";i:1501570841;s:54:"D:\phpStudy\WWW\Tplus/app/system\view\base\common.html";i:1487660130;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>T+ 最清爽的后台框架</title>
    <meta name="keywords" content="T+ 最清爽的后台框架">
    <meta name="description" content="T+ 最清爽的后台框架">
    <link rel="shortcut icon" href="./favicon.ico">
    <link href="<?php echo $_css; ?>/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="<?php echo $_css; ?>/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <link href="<?php echo $_css; ?>/animate.min.css" rel="stylesheet">
    <link href="<?php echo $_css; ?>/main.css" rel="stylesheet" />
    <link href="<?php echo $_css; ?>/style.min862f.css?v=4.1.0" rel="stylesheet" />
    <link href="<?php echo $_css; ?>/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="<?php echo $_css; ?>/plugins/toastr/toastr.min.css" rel="stylesheet">
    <link href="<?php echo $_css; ?>/plugins/sweetalert/sweetalert.css" rel="stylesheet">

    <script src="<?php echo $_js; ?>/jquery.min.js?v=2.1.4"></script>
     
    <script src="<?php echo $_js; ?>/bootstrap.min.js?v=3.3.6"></script>
    <script src="<?php echo $_js; ?>/plugins/iCheck/icheck.min.js"></script>
    <script src="<?php echo $_js; ?>/plugins/toastr/toastr.min.js"></script>
    <script src="<?php echo $_js; ?>/plugins/sweetalert/sweetalert.min.js"></script>
    <script src="<?php echo $_js; ?>/common.js"></script>
    <script src="<?php echo $_js; ?>/main.js"></script>
    <script src="<?php echo $_js; ?>/modernizr-2.8.0.min.js"></script>
    <script src="<?php echo $_js; ?>/video.js"></script>
    <script src="<?php echo $_js; ?>/jquery.autocompleter.js"></script>
   <script src="<?php echo $_js; ?>/plugins/layer/layer.min.js"></script>
</head>

<body class="gray-bg">
    
<body class="gray-bg">
	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="row">
			<div class="col-sm-4">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5>更新日志</h5>
					</div>
					<div class="ibox-content no-padding">
						<div class="panel-body">
							<div class="panel-group" id="version">
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title">
											<a data-toggle="collapse" data-parent="#version" href="#v11" class="collapsed" aria-expanded="false">v2.2</a><code class="pull-right">2017.08.01</code>
										</h4>
									</div>
									<div id="v22" class="panel-collapse collapse in" aria-expanded="true" >
										<div class="panel-body">
											<ol>
												<li>修复安装功能；</li>
												<li>新增php生成二维码的插件，具体用法见plugs/qrcode目录下的readme.md；</li>
												<li>将Public目录名称改成小写的public；</li>
												<li>新增微信支付插件，具体用法见plugs/wechatpay目录下的readme.md；</li>
												<li>新增基于聚合数据的短信插件，具体用法见plugs/sms目录下的readme.md；</li>
												<li>新增钩子的控制器类；</li>
												<li>新增清理缓存的功能。</li>
											</ol>
										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title">
											<a data-toggle="collapse" data-parent="#version" href="#v11" class="collapsed" aria-expanded="false">v2.1</a><code class="pull-right">2017.01.13</code>
										</h4>
									</div>
									<div id="v21" class="panel-collapse collapse in" aria-expanded="true" >
										<div class="panel-body">
											<ol>
												<li>新增安装功能；</li>
												<li>修复插件问题；</li>
												<li>优化编辑器、轮播、单多图上传插件安装卸载；</li>
												<li>新增微信登录、表单构造器插件；</li>
												<li>所有插件默认不安装；</li>
												<li>优化目录结构，兼容linux；</li>
												<li>模板路径和插件请都小写；</li>
											</ol>
										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<h5 class="panel-title">
											<a data-toggle="collapse" data-parent="#version" href="#v40" class="collapsed" aria-expanded="false">v2.0</a><code class="pull-right">2017.01.06</code>
										</h5>
									</div>
									<div id="v20" class="panel-collapse collapse in" aria-expanded="true" >
										<div class="panel-body">

												<div class="alert alert-success">升级Thinkphp到最新版本5.0.4</div>
											<span class="label label-danger">新增修复删除优化：</span>
											<ol>
													<li>新增Tpuls类，前台后台控制器都必须继承Tpuls；</li>
													<li>为能平滑升级框架版本，移除底层扩展代码；</li>
													<li>移除导航管理，放弃内容管理功能；</li>
													<li>优化前端代码，移除冗余代码；</li>
													<li>修复二级菜单无法显示图标问题；</li>
													<li>PC端强制显示菜单(不收缩)；</li>
													<li>隐藏删除admin用户操作，防止误删；</li>
													<li>优化访问授权选择方式，可以全选；</li>
													<li>完善Auth体系、增加删除授权；</li>
													<li>菜单增加可填参数选项，授权可以精准到参数<br/>
														(不含插件，插件只能整体授权。不能精确授权到里面的方法)；</li>
													<li>插件可自由选择父级ID，以及font_class图标；</li>
													<li>卸载插件时会自动移出菜单；</li>
											</ol>
												<span class="label label-warning">操作方法：</span>
											<ol>
												<li>has判断移除类型<br />
													<code>$this->request->has("参数",'类型');</code>
													<br />修改为<br />
													<code>$this->request->has("参数");</code>
												</li>
												<li> <code>$this->request->param()</code>
													可以自动识别get post put，建议使用param
												</li>
											</ol>
										</div>
									</div>
								</div>
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title">
											<a data-toggle="collapse" data-parent="#version" href="#v11" class="collapsed" aria-expanded="false">v1.0</a><code class="pull-right">2016.08.04</code>
										</h4>
									</div>
									<div id="v10" class="panel-collapse collapse in" aria-expanded="true" >
										<div class="panel-body">
											<ol>
												<li>Tplus正式发布。（基于Thinkphp RC4.0）</li>
											</ol>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5>系统信息</h5>
					</div>
					<div class="ibox-content">
						<table class="table table-striped">
							<thead>
								<tr>
									<td>T+ 轻内容管理系统版本：</td>
									<td><?php echo TCMS_VERSION; ?></td>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>服务器操作系统:</td>
									<td><?php echo PHP_OS; ?></td>
								</tr>
								<tr>
									<td>ThinkPHP版本:</td>
									<td><?php echo THINK_VERSION; ?></td>
								</tr>
								<tr>
									<td>运行环境:</td>
									<td><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
								</tr>
								<tr>
									<td>MYSQL版本:</td>
									 <td><?php echo $mysql_v; ?></td> 	
								</tr>
								<tr>
									<td>上传限制:</td>
									<td><?php echo ini_get('upload_max_filesize'); ?></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5>产品团队</h5>
					</div>
					<div class="ibox-content">
						<table class="table table-striped">
							<thead>
								<tr>
									<td>总策划：</td>
									<td>张文强</td>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>产品设计及研发团队:</td>
									<td>张文强、宋家林、杨神岩，彭涛，邬历，高明浩</td>
									
								</tr>
								<tr>
									<td>官方网址:</td>
									<td><a href="http://wwww.baidu.com" target="_blank">wwww.baidu.com</a></td>
								</tr>
								<tr>
									<td>官方QQ群:</td>
									<td><a target="_blank" href="http://jq.qq.com/?_wv=1027&k=29JWDfm"><img border="0" src="http://pub.idqqimg.com/wpa/images/group.png" alt="T+技术交流" title="T+技术交流"></a></td>
								</tr>
								<tr>
									<td>BUG反馈:</td>
									<td><a href="http://wwww.baidu.com" target="_blank">T+ 后台框架讨论区</a></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

 
</body>

</html>