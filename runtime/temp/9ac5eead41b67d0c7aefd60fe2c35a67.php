<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:51:"D:\phpStudy\WWW\Tplus\plugs/focusimg/adminlist.html";i:1473492378;}*/ ?>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5><?php echo $title; ?></h5>
                    </div>
                    <div class="ibox-content">
                        <div class="row">

                            <div class="col-sm-9 m-b-xs">
                                 <a class="btn btn-primary" href="<?php echo plugUrl('FocusImg/FocusCat/addCat',['id'=>1]); ?>"><i class="fa fa-plus"></i> &nbsp;新 增</a>
                            
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group search-form">
                                    <input type="text" placeholder="请输入关键词" name='title' value="<?php echo input('get.title'); ?>" class="search-input input-sm form-control">
                                    <span class="input-group-btn">
                                        <button type="button" id="search" url="<?php echo url('Addons/adminlist',['name'=>'FocusImg']); ?>" class="btn btn-sm btn-primary">搜索</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>
                                            <div class="icheckbox_square-green" style="position: relative;">
                                                <input type="checkbox" class="i-checks check-all" id="check-all"  style="position: absolute; opacity: 0;"/>
                                            </div>
                                        </th>
                                        <script>

                                        </script>
                                        <th>ID</th>
                                        <th>分类标题</th>
                                        <th>分类标识</th>
                                        <th>创建时间</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(is_array($list) || $list instanceof \think\Collection): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                    <tr>
                                        <td>
                                            <div class="icheckbox_square-green" style="position: relative;">
                                                <input type="checkbox" class="i-checks ids" name="ids[]" value="<?php echo $vo['id']; ?>" style="position: absolute; opacity: 0;"/>
                                            </div>
                                        </td>
                                       <td><?php echo $vo['id']; ?></td>
                                       <td><a href="<?php echo plugUrl('FocusImg/FocusPic/index',['catid'=>$vo['id']]); ?>"><?php echo $vo['title']; ?></a></td>
                                       <td><?php echo $vo['name']; ?></td>
                                       <td><?php echo date('Y-m-d H:i:s',$vo['create_time']); ?></td>
                                       <td>
                                            <a href="<?php echo plugUrl('FocusImg/FocusCat/edit',['id'=>$vo['id']]); ?>">
                                                <i class="fa fa-wrench text-navy"></i>
                                                编辑
                                            </a>

                                            <a class="confirm ajax-get" href="<?php echo plugUrl('FocusImg/FocusCat/del',['id'=>$vo['id']]); ?>">
                                                <i class="fa fa-trash text-danger"></i>
                                                删除
                                            </a>

                                       </td>
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
