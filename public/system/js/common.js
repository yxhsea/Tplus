$(function(){
    //实例化提醒组件
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "progressBar": true,
        "positionClass": "toast-top-full-width",
        "onclick": null,
        "showDuration": "400",
        "hideDuration": "1000",
        "timeOut": "1500",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
    //调用参数  success 成功 info 信息 warning 警告 error 错误
    // toastr['success']( "后台菜单更新成功，正在为您跳转到列表");

    //列表多选框
    $(".i-checks").iCheck({checkboxClass:"icheckbox_square-green",radioClass:"iradio_square-green",});

    //ajax get请求
   // $('.ajax-get').click(function(){
    $("body").on('click','.ajax-get',function(){    
        var target;
        var that = this;
        if ( $(this).hasClass('confirm') ) {
            if(!confirm('确认要执行该操作吗?')){
                return false;
            }
        }
        if ( (target = $(this).attr('href')) || (target = $(this).attr('url')) ) {
            $.get(target).success(function(data){
                if (data.status==1) {
                    if (data.url) {
                        toastr['success'](data.info+' ,页面即将自动跳转~');
                    }else{
                        toastr['success'](data.info);
                    }
                    setTimeout(function(){
                        if (data.url) {
                            location.href=data.url;
                        }else{
                            location.reload();
                        }
                    },1500);
                }else{
                    toastr['error'](data.info);
                    setTimeout(function(){
                        if (data.url) {
                            location.href=data.url;
                        }
                    },1500);
                }
            });

        }
        return false;
    });

    //ajax post submit请求
    $('.ajax-post').click(function(){
        var target,query,form;
        var target_form = $(this).attr('target-form');
        var that = this;
        var nead_confirm=false;
        if( ($(this).attr('type')=='submit') || (target = $(this).attr('href')) || (target = $(this).attr('url')) ){
            form = $('.'+target_form);

            if ($(this).attr('hide-data') === 'true'){//无数据时也可以使用的功能
                form = $('.hide-data');
                query = form.serialize();
            }else if (form.get(0)==undefined){
                return false;
            }else if ( form.get(0).nodeName=='FORM' ){
                if ( $(this).hasClass('confirm') ) {
                    if(!confirm('确认要执行该操作吗?')){
                        return false;
                    }
                }
                if($(this).attr('url') !== undefined){
                    target = $(this).attr('url');
                }else{
                    target = form.get(0).action;
                }

                query = form.serialize();
            }else if( form.get(0).nodeName=='INPUT' || form.get(0).nodeName=='SELECT' || form.get(0).nodeName=='TEXTAREA') {
                form.each(function(k,v){
                    if(v.type=='checkbox' && v.checked==true){
                        nead_confirm = true;
                    }
                })
                if ( nead_confirm && $(this).hasClass('confirm') ) {
                    if(!confirm('确认要执行该操作吗?')){
                        return false;
                    }
                }
                query = form.serialize();
            }else{
                if ( $(this).hasClass('confirm') ) {
                    if(!confirm('确认要执行该操作吗?')){
                        return false;
                    }
                }
                query = form.find('input,select,textarea').serialize();
            }
            // $(that).addClass('disabled').attr('autocomplete','off').prop('disabled',true);
            $.post(target,query).success(function(data){
                if (data.status==1) {
                    if (data.url) {
                        toastr['success'](data.info+' ,页面即将自动跳转~');
                    }else{
                        toastr['success'](data.info);
                    }
                    setTimeout(function(){
                        if (data.url) {
                            location.href=data.url;
                        }else{
                            location.reload();
                        }
                    },1500);
                }else{
                    toastr['error'](data.info);
                    setTimeout(function(){
                        if (data.url) {
                            location.href=data.url;
                        }
                    },1500);
                }
            });
        }
        return false;
    });

    //按钮返回上一页
    $(".btn-back").click(function(){

        history.go(-1);
    });

    //全选的实现
    $('.check-all').on('ifChecked', function(event){
        $(".ids").iCheck("check");
    });
    //反选实现
    $(".check-all").on('ifUnchecked',function(event){
        $(".ids").iCheck("uncheck");
    });
    //点击排序
    $('.list_sort').click(function(){
        var url = $(this).attr('url');
        var ids = $('.ids:checked');
        var param = '';
        if(ids.length > 0){
            var str = new Array();
            ids.each(function(){
                str.push($(this).val());
            });
            param = str.join(',');
        }

        if(param == ''){
            toastr['info']('请选择您要排序的数据!');
            return false;
        }

        url=url.split(".html");
        if(url[0] != undefined && url[0] != ''){
            window.location.href = url[0] + '/ids/' + param;
        }
    });

    //搜索功能
    $("#search").click(function() {
        var url = $(this).attr('url');
        var query = $('.search-input').val();
        url=url.split(".html");
        if(url[0] != undefined && url[0] != ''){
            window.location.href = url[0] + '/'+$('.search-input').attr('name')+'/' + query;
        }
    });

    //回车搜索
    $(".search-input").keyup(function(e) {
        if (e.keyCode === 13) {
            $("#search").click();
            return false;
        }
    });


});



//判定当前访问为移动端还是PC端
function IsPC()
{
    var userAgentInfo = navigator.userAgent;
    var Agents = new Array("Android", "iPhone", "SymbianOS", "Windows Phone", "iPad", "iPod");
    var flag = true;
    for (var v = 0; v < Agents.length; v++) {
        if (userAgentInfo.indexOf(Agents[v]) > 0) { flag = false; break; }
    }
    return flag;
}