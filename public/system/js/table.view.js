            function jsurl(url,param){
                var url = url.split(".");
                return url[0]+param;
            }
            var TableView = function TableView (node,edit_type){
                this.node = $("#"+node);
                this.data = '';
                this.param = [];
                this.edit_type = edit_type; //1属性列表中 2商品添加中
                this.AddData = function (data){
                    this.ToView(data);

                }; 

                this.ToView = function (data){
                    //console.log(this.create_th(data));
                    this.toNode(this.create_table(this.create_th(data)+this.create_td(data)));
                };

                this.create_th = function(data){
                    var th ='<tr>';
                    
                    for(var i = 0; i < data.length; i++){
                        if(this.edit_type == 1){
                            if(data[i]['id']){
                            var delurl = jsurl("{:url('delend',['tpl_id'=>input('tpl_id')])}",'/id/'+data[i]['id']);
                            var editurl = jsurl("{:url('editend',['tpl_id'=>input('tpl_id')])}",'/id/'+data[i]['id']);
                             th+='<th>'+data[i].title+' <a style="float:right;margin-right:10px;" class="confirm ajax-get" href="'+delurl+'"><i class="fa fa-trash text-danger"></i>删除</a> <a style="float:right;margin-right:10px;" href="'+editurl+'"><i class="fa fa-wrench text-navy"></i>修改</a></th>';
                         }else{
                            th+='<th>'+data[i].title+'</th>';
                         }
                        }else{
                            th+='<th>'+data[i].title+'</th>';
                        }
                        
                    }
                    return th+'</tr>';
                };
                //创建表格主体
                this.create_td = function(data){
                    var td ='<tr>';
                    for(var i = 0; i < data.length; i++){
                        td+='<td rowspan="'+data[i].rowspan+'" class="'+data[i].attr_name+'"><input type="hidden" name="'+data[i].attr_name+'[]" class="attr_value" /><input type="hidden" class="attr_id" name="'+data[i].attr_name+'_attr_id" value="'+data[i].id+'" /><span>'+data[i].title+'</span></td>';
                        if(data[i].value_number>0){
                            this.td_tree(data,i,data[i].value_number);
                        }
                    }
                    var firstTd = td+'</tr>';
                    //this.forData(firstTd);
                    //console.log(this.param);
                    return this.forData(firstTd);
                };

                this.td_tree = function(data,k,value_number){
                    var arr = [];
                    for(var s = 1;s<=value_number-1;s++){
                        var td1='<tr>';
                        for(var i = 0; i < data.length; i++){
                        
                            if(i>=k){
                                td1+='<td rowspan="'+data[i].rowspan+'" class="'+data[i].attr_name+'"><input type="hidden" class="attr_value" name="'+data[i].attr_name+'[]" /><input class="attr_id" type="hidden" name="'+data[i].attr_name+'_attr_id" value="'+data[i].id+'" /><span>'+data[i].title+'</span></td>';
                            }
                        
                        }
                        arr.push([td1+'</tr>']);
                    }
                        this.param.push(arr);
                    
                };

                this.create_table = function(content){
                    var table = '<table class="table table-striped table-bordered">';
                    return table+content+'</table>';
                }

                this.toNode = function(content){
                    this.node.html(content);
                };

                this.forData = function(firstTd){
                    //console.log(this.param);
                    var html = firstTd;
                    var firstg= false;
                    var td = '';
                    for(var i = 0; i < this.param.length;i++){
                        var param = this.param[i];
                        for(var k = 0; k < param.length;k++){
                            
                            var param1 = param[k];
                            for(var s = 0; s < param1.length;s++){
                                if(i == (this.param.length-1)){
                                    if(firstg == false){
                                        html+=this.daoxu();
                                        firstg = true;
                                    }
                                    
                                }else if(i == 0){
                                    
                                    td+=param1[s]+this.daoxu();
                                }else{
                                    
                                    /*for(var a = 0;a < this.param[this.param.length-1].length;a++){
                                        var data1 = this.param[this.param.length-1][a];
                                        for(var b = 0; b < data1.length;b++){
                                            param1[s]+=data1[b];

                                        }
                                    }
                                    td+=param1[s];*/
                                }
                            }   
                        }
                    }

                     
                    return html+td;
                };
                this.daoxu = function(){
                    var zhongzhuan = '';
                    var zhongzhuan1 ='';
                    for(var i = this.param.length-1;i>=0;i--){
                        if(i==(this.param.length-1)){
                            zhongzhuan+=this.forson(this.param[i]);

                        }
                        
                        if(i!=0){
                            var param1= this.param[i];
                            for(var s= 0;s<param1.length;s++){
                                //console.log(param1);
                                
                                //console.log(i);
                                if(i < (this.param.length-1)){
                                    zhongzhuan1+=this.forson(param1[s])+this.getSon(i);

                                }
                            }
                        }
                    }
                    //console.log();

                    return zhongzhuan+zhongzhuan1
                };

                this.forson = function(data){
                    var guo = '';
                    for(var i = 0; i< data.length;i++){
                        guo+=data[i];
                    }
                    return guo;
                };

                this.getSon = function(k){
                    var zhongzhuan = '';
                    for(var i = this.param.length-1;i>=0;i--){
                        if(i > k){
                            //console.log(this.param[i]);
                            var params = this.param[i];
                            for(var s = 0; s< params.length;s++){
                                zhongzhuan+=this.forson(params[s]);
                            }
                        }
                    }

                    return zhongzhuan;
                    //console.log(zhongzhuan);
                };
            }; 