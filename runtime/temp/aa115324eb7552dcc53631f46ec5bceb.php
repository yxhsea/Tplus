<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:55:"D:\phpStudy\WWW\Tplus/app/system\view\addons\index.html";i:1501234396;s:54:"D:\phpStudy\WWW\Tplus/app/system\view\base\common.html";i:1487660130;}*/ ?>
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
    
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>插件列表</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-sm-9 m-b-xs">
                                 <a class="btn btn-primary" href="<?php echo url('create'); ?>"><i class="fa fa-plus"></i> &nbsp;创建插件</a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>名 称</th>
                                        <th>标 识</th>
                                        <th>描 述</th>
                                        <th>状 态</th>
                                        <th>作 者</th>
                                        <th>版 本</th>
                                        <th>操 作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(is_array($list) || $list instanceof \think\Collection): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                    <tr>
                                        <td>
                                           <?php echo $vo['title']; ?>
                                        </td>
                                        <td> <?php echo $vo['name']; ?></td>
                                        <td><?php echo $vo['description']; ?></td>
                                        <td><?php if(isset($vo['status']) == false): ?>
                                                未安装
                                            <?php else: if($vo['status'] == 1): ?>
                                                    启用
                                                <?php else: ?>
                                                    禁用
                                                <?php endif; endif; ?></td>
                                        <td><?php echo $vo['author']; ?></td>
                                        <td><?php echo $vo['version']; ?></td>
                                        <td>
                                            <?php if(empty($vo['uninstall']) || ($vo['uninstall'] instanceof \think\Collection && $vo['uninstall']->isEmpty())): 
                                                    $class  = get_plugs_class($vo['name']);
                                                    if(!class_exists($class)){
                                                        $has_config = 0;
                                                    }else{
                                                        $addon = new $class();
                                                        $has_config = count($addon->getConfig());
                                                    }
                                                if ($has_config){ ?>
                                                    <a href="<?php echo url('config',array('id'=>$vo['id'])); ?>">
                                                        <i class="fa fa-wrench text-navy"></i>
                                                        设置
                                                    </a>
                                                <?php } if ($vo['status'] >=0){ if($vo['status'] == '0'): ?>
                                                   
                                                    <a href="<?php echo url('setStatus',['module'=>'Addons','ids'=>$vo['id'],'status'=>1]); ?>" class="ajax-get">
                                                        <i class="fa fa-check text-navy"></i>
                                                        启用
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?php echo url('setStatus',['module'=>'Addons','ids'=>$vo['id'],'status'=>0]); ?>" class="ajax-get">
                                                        <i class="fa fa-ban text-danger"></i>
                                                        禁用
                                                    </a>
                                                <?php endif; } ?>
                                                
                                                    <a class="confirm ajax-get" href="<?php echo url('uninstall?id='.$vo['id']); ?>">
                                                         <i class="fa fa-trash text-danger"></i>
                                                        卸载
                                                    </a>
                                                
                                            <?php else: ?>
                                                 <a class="ajax-get" href="<?php echo url('install?addon_name='.$vo['name']); ?>">
                                                    <i class="fa fa-plus text-navy"></i>
                                                安装
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; endif; else: echo "" ;endif; ?>   
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 m-b-xs">
                                <div class="btn-group center-block">
                                    <?php echo $page; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
   
</body>

</html>