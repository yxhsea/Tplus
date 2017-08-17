<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:50:"D:\phpStudy\WWW\Tplus\plugs\FocusImg\view/add.html";i:1473492378;}*/ ?>
	<div class="wrapper wrapper-content animated fadeIn">
		<div class="ibox float-e-margins">
				<div class="ibox-title">
                        <h5>创建轮播分类</h5>
                 </div>
			<div class="ibox-content">

				<div class="row">
					<div class="col-sm-12">
						<div class="tabs-container">
						
							<div class="tab-content">
									<div class="ibox-content" style="border:none">
										<form class="form-horizontal" action="<?php echo plugUrl('FocusImg/FocusCat/addCat'); ?>" method="post" >
											
											<div class="form-group">
												<label class=".col-xs-6 col-sm-2 control-label text-right">分类名称 </label>
												<div class=".col-xs-6 col-sm-10">
													<input type="text" class="form-control" value="" name="title" placeholder="请输入分类的名称" />
												</div>
											</div>
											
											<div class="form-group">
												<label class=".col-xs-6 col-sm-2 control-label text-right">分类标识</label>
												<div class=".col-xs-6 col-sm-10">
													<input type="text" class="form-control" value="" name="name" placeholder="请输入分类的标识" />
												</div>	
											</div>
											

											<div class="form-group">
												<div class="col-sm-2"></div>
												<div class="col-sm-4">
													<input type="hidden" name="id" value="" /> 
													<button class="btn btn-primary ajax-post" type="submit" target-form="form-horizontal">保存内容</button>

													<button class="btn btn-white btn-back" type="button" >返回</button>
												</div>
											</div>
										</form>
									</div>
								
							</div>

						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
	
