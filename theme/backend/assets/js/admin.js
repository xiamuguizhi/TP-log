$.fn.serializeObject = function() {
  var o = {};
  var a = this.serializeArray();
  $.each(a, function() {
      if (o[this.name] !== undefined) {
          if (!o[this.name].push) {
              o[this.name] = [o[this.name]];
          }
          o[this.name].push(this.value || '');
      } else {
          o[this.name] = this.value || '';
      }
  });
  return o;
}; 


/*
* type              请求的方式  默认为post
* url               发送请求的地址
* param             发送请求的参数
* dataType          返回JSON数据  默认为JSON格式数据
* callBack          请求的回调函数
*/
(function(){
    function AjaxRequest(opts){
        this._base_param  = {};
        this._baseUrl     = "";
        this.headers      = opts.headers || {};
        this.type         = opts.type || "post";
        this.url          = this._baseUrl+opts.url;
        this.param        = opts.param || {};
        this.headers      = opts.headers || {};
        this.async        = opts.async || false;
        this.dataType     = opts.dataType || "json";
        this.callBack     = opts.callBack;
        this.is_original_data = opts.is_original_data || false;
        this.initParam();
        this.init();
    }

    AjaxRequest.prototype = {
        //参数初始化
        initParam:function(){
            Object.assign(this.param,this.param,this._base_param);
        },
        //初始化
        init: function(){
            this.sendRequest();
        },
        //渲染loader
        showLoader: function(){
            layer.open({type:3}); 
        },
        //隐藏loader
        hideLoader: function(){
            layer.closeAll('loading');
        },
        //提示消息
        toast:function(type=1,title="",content=""){
            layer.open({
                type:type,
                title:title,
                content:content
            });
        },
        //发送请求
        sendRequest: function(){
            var self = this;
            $.ajax({
                type: this.type,
                url: this.url,
                data: this.param,
                async:this.async,
                headers:this.headers,
                dataType: this.dataType,
                beforeSend: this.showLoader(),
                success: function(res){
                    self.hideLoader();
                    if (typeof res == "object" && res.code==0) {
                        if(self.callBack){
                            if (Object.prototype.toString.call(self.callBack) === "[object Function]") {   //Object.prototype.toString.call方法--精确判断对象的类型
                                self.is_original_data?self.callBack(res):self.callBack(res.data);
                            }else{
                                console.log("callBack is not a function");
                            }
                        }
                    }else{
                        self.toast(0,"出错了",res.msg);
                    }
                },
                error:function(XMLHttpRequest, textStatus, errorThrown){
                    self.hideLoader();
                    self.toast(0,"请求出错");
                }
            });
        }
    };
    window.AjaxRequest = AjaxRequest;
})();




$('.js-post-ajax-dialog-btn').on('click', function (e) {
	e.preventDefault();
	var $_this = this,
	$this = $($_this),
	href = $this.data('href'),
	msg = $this.data('msg');
	href = href?href:$this.attr('href');
	layer.confirm(msg, {
			btn: ['确定', '取消'] //按钮
			}, function(){
			$.post(href,function(data){
				if (data.code == 0) {
				if (data.url) {
				location.href = data.url;
			} else {
				const toast = swal.mixin({
									toast: true,
									position: 'center',
									showConfirmButton: false,
									timer: 1500
									});
						toast({
									type: 'warning',
									title: data.msg,
									});	
									reloadPage(window);
													return true;
									
											}
										} else if (data.code == -1) {
                                const toast = swal.mixin({
									toast: true,
									position: 'center',
									showConfirmButton: false,
									timer: 1500
									});
								toast({
									type: 'warning',
									title: data.msg,
									});	 
									}else{
										const toast = swal.mixin({
									toast: true,
									position: 'center',
									showConfirmButton: false,
									timer: 1500
									});
								toast({
									type: 'warning',
									title: data.msg,
									});	 
								}
							},"json");
				}, function(){
							layer.close();
				});
            });
	
$('.js-ajax-dialog-btn').on('click', function (e) {
	e.preventDefault();
	var $_this = this,
	$this = $($_this),
	href = $this.data('href'),
	msg = $this.data('msg');
	href = href?href:$this.attr('href');
		layer.confirm(msg, {
			btn: ['确定', '取消'] //按钮
			}, function(index){			
			$.post(href,function(data){
				if (data.code == 0) {
					if (data.url) {
						location.href = data.url;
					} else {
						layer.msg("操作成功");											
						reloadPage(window); 
						layer.close(index);
						//return true;									
					}
				} else if (data.code == -1) {
                        layer.msg(data.msg, {
							icon: 'cry',
							time: 1500,
						});	 
				}else{
						layer.msg(data.msg, {
							icon: 'cry',
							time: 1500,
						});	 
				}
			},"json");						
		}, function(index){
			layer.close(index);
		});
});

    //tinymce编辑时提交
    $(".js-ajax-tinymce").click(function() {
		tinyMCE.triggerSave();
        var $btn = $(".js-ajax-tinymce"),$form = $(".js-ajax-form");
        var formObject=$form.serializeObject();
        $.ajax({
            url: $btn.data('action') ? $btn.data('action') : $form.attr('action'), //按钮上是否自定义提交地址(多按钮情况)
            dataType: 'json',
            data: formObject,
            type: "post",
            beforeSend: function(arr, $form, options) {
                $btn.data("loading", true);
                var text = $btn.text();
                //按钮文案、状态修改
                $btn.addClass('disabled');
            },
            success: function(data, statusText, xhr) {
                var text = $btn.text();
                //按钮文案、状态修改
                $btn.removeClass('disabled');
                if (data.code === 0) {
                        if(window.parent.frames.length == 1){
                            parent.layer!==undefined && parent.layer.closeAll('iframe');
							layer.msg(data.msg);
							parent.location.reload();
							layer.close();//关闭弹出层	                            
                        }else{
							layer.msg(data.msg);
							location.href = data.url;
							layer.close();                           
                        }           
                } else{
					layer.msg(data.msg, {
					icon: 'cry',
					time: 1500,
					});
                    $btn.removeProp('disabled').removeClass('disabled');
                }
            },
            error: function(xhr, e, statusText) {
                layer.alert(statusText);
                //刷新当前页
                //reloadPage(window);
            },
            complete: function() {
                $btn.data("loading", false);
            }
        });
   });

// 直接提交数据，不通过validate验证数据，在有图片上传时会有问题
    $(".js-ajax-button").click(function() {
        var $btn = $(".js-ajax-button"),$form = $(".js-ajax-form");
        var formObject=$form.serializeObject();
        // 对于ke-editor编辑器
        if($form.find("textarea[class='ke-edit-textarea']").length &&　$form.find("textarea[class!='ke-edit-textarea']").length>0){
            $.each($form.find("textarea[class!='ke-edit-textarea']"),function(k,v){
                var key=$(v).attr("name");
                formObject[key]=editors[k].html();
            });
        }
        $.ajax({
            url: $btn.data('action') ? $btn.data('action') : $form.attr('action'), //按钮上是否自定义提交地址(多按钮情况)
            dataType: 'json',
            data: formObject,//$form.serialize(),
            type: "post",
            beforeSend: function(arr, $form, options) {

                $btn.data("loading", true);
                var text = $btn.text();
                //按钮文案、状态修改
                $btn.addClass('disabled');
            },
            success: function(data, statusText, xhr) {
                var text = $btn.text();
                //按钮文案、状态修改
                $btn.removeClass('disabled');
                if (data.code === 0) {
                        if(window.parent.frames.length == 1){
                            parent.layer!==undefined && parent.layer.closeAll('iframe');
							layer.msg(data.msg);
							parent.location.reload();
							layer.close();//关闭弹出层	                            
                        }else{
							layer.msg(data.msg);
							location.href = data.url;
							layer.close();                           
                        }           
                } else{
                    var $verify_img = $form.find("#captcha");
                    if ($verify_img.length) {
                        $verify_img.attr("src", "/captcha.html?t=" + Math.random());
                    }
                    $form.find("[name='verify']").val("");
					layer.msg(data.msg, {
					icon: 'cry',
					time: 1500,
					});
                    $btn.removeProp('disabled').removeClass('disabled');
                }
            },
            error: function(xhr, e, statusText) {
                layer.alert(statusText);
                //刷新当前页
                //reloadPage(window);
            },
            complete: function() {
                $btn.data("loading", false);
            }
        });
   });



// 所有的ajax form提交,由于大多业务逻辑都是一样的，故统一处理
    var ajaxForm_list = $('form.js-ajax-form');
    if (ajaxForm_list.length) {
        Wind.use('ajaxForm','validate', function () {
            
            var $btn;

            $('button.js-ajax-submit').on('click', function (e) {
                var btn = $(this),form = btn.parents('form.js-ajax-form');
                $btn=btn;
                
                if(btn.data("loading")){
            		return;
            	}

                //批量操作 判断选项
                if (btn.data('subcheck')) {
                    btn.parent().find('span').remove();
                    if (form.find('input.js-check:checked').length) {
                        var msg = btn.data('msg');
                        if (msg) {
							layer.confirm('确定要删除吗？', {
							btn: ['确定', '取消'] //按钮
							}, function(index){
								btn.data('subcheck', false);
                                btn.click();
							}, function(index){
								layer.close(index);
							});
                        } else {
                            btn.data('subcheck', false);
                            btn.click();
                        }
                    } else {
							layer.msg("至少选中一个项目操作", {
							icon: 'cry',
							time: 1500,
							});
					}
                    return false;
                }

                //ie处理placeholder提交问题
                if ($.browser && $.browser.msie) {
                    form.find('[placeholder]').each(function () {
                        var input = $(this);
                        if (input.val() == input.attr('placeholder')) {
                            input.val('');
                        }
                    });
                }
            });
            
            ajaxForm_list.each(function(){
            	$(this).validate({
    				onkeyup : function( element, event ) {
    					return;
    					var excludedKeys = [
    						16, 17, 18, 20, 35, 36, 37,
    						38, 39, 40, 45, 144, 225
    					];

    					if ( event.which === 9 && this.elementValue( element ) === "" || $.inArray( event.keyCode, excludedKeys ) !== -1 ) {
    						return;
    					} else if ( element.name in this.submitted || element.name in this.invalid ) {
    						this.element( element );
    					}
    				},
    				//当鼠标掉级时验证
    				onclick : false,
    				//给未通过验证的元素加效果,闪烁等
    				//highlight : false,
                	showErrors:function(errorMap, errorArr){
                		try {
    						$(errorArr[0].element).focus();
    						//alert(errorArr[0].message);
    					} catch (err) {
    					}
                	},
                	submitHandler:function(form){
                		var $form=$(form);
                		$form.ajaxSubmit({
                            url: $btn.data('action') ? $btn.data('action') : $form.attr('action'), //按钮上是否自定义提交地址(多按钮情况)
                            dataType: 'json',
                            type:"post",
                            beforeSubmit: function (arr, $form, options) {                           	
                            	$btn.data("loading",true);
                                var text = $btn.text();
                                //按钮文案、状态修改
                                $btn.addClass('disabled');
                            },
                            success: function (data, statusText, xhr, $form) {
                                var text = $btn.text();
                                //按钮文案、状态修改
                                $btn.removeClass('disabled').prop('disabled', false);
                                if (data.code === 0) {
                                    //showNotify(data.msg, 'success', 1000);
                                        if(window.parent.frames.length == 1){
                                            parent.layer!==undefined && parent.layer.closeAll('iframe');
                                            parent.location.reload();
                                        }else{
                                            if(data.url){
												layer.msg(data.msg, {
												icon: 'success',
												time: 2000,
												});
												location.href = data.url;
												layer.close();//关闭弹出层										
                                            }else{		
												layer.msg(data.msg, {
												icon: 'success',
												time: 2000,
												});											
												reloadPage(window); 
												layer.close();
                                            }
                                        }                  
                                    
                                } else{
                                	var $verify_img=$form.find(".verify_img");
                                	if($verify_img.length){
                                		$verify_img.attr("src",$verify_img.attr("src")+"&refresh="+Math.random()); 
                                	}                               	
                                	var $verify_input=$form.find("[name='verify']");
                                	$verify_input.val("");	

									layer.msg(data.msg, {
											icon: 'cry',
											time: 1500,
									});
                                    $btn.removeProp('disabled').removeClass('disabled');
                                }
                                
                            },
                            error:function(xhr,e,statusText){
								layer.alert(statusText);
                            },
                            complete: function(){
                            	$btn.data("loading",false);
                            }
                        });
                	}
                });
            });

        });
    }

	//删除事件
if ($('a.js-ajax-delete').length) {
            $('.js-ajax-delete').on('click', function (e) {
                e.preventDefault();
                var $_this = this,
                    $this = $($_this),
                    href = $this.data('href'),
                    msg = $this.data('msg');
					href = href?href:$this.attr('href');
					layer.confirm(msg, {
							btn: ['确定', '取消'] //按钮
							}, function(index){
									$.post(href,function(data){
									if (data.code == 0) {
										if(data.url){
												layer.msg(data.msg, {
												icon: 'success',
												time: 2000,
												});
												location.href = data.url;
												layer.close(index);//关闭弹出层										
                                            }else{		
												layer.msg(data.msg, {
												icon: 'success',
												time: 2000,
												});										
												reloadPage(window); 
												layer.close(index);
                                            }
									} else{
												layer.msg(data.msg, {
												icon: 'cry',
												time: 2000,
												});
									}
								},"json");	
							}, function(index){
								layer.close(index);
							});
							return false;

				});
 }
	
	// 文本框统一ajax处理
    $(".ajax-text").each(function(){
        var newval;
        $(this).change(function(){
            newval=$(this).val();
            var data=$(this).attr("data"),action=$(this).attr("action");
            var dataArr=data.split("|"),table=dataArr[0],colum=dataArr[1],key=dataArr[2],keyval=dataArr[3];
            $.ajax({
                type:"post",
                dataType:"json",
                data:{"table":table,"colum":colum,"columval":newval,"key":key,"keyval":keyval},
                url:action,
                success:function(data){
					layer.msg(data.msg, {
					icon: 'success',
					time: 2000,
					});											
					location.reload();
					layer.close();					
                }
				
            });
        });
    });		
	
	// 统一状态ajax处理
    $(".ajax-status").click(function(){
        $this=$(this);
        var data=$(this).attr("data"),src=$(this).attr("src"),action=$(this).attr("action");
        var dataArr=data.split("|"),table=dataArr[0],colum=dataArr[1],key=dataArr[2],keyval=dataArr[3];		
        var newval=src.indexOf("btn_enable.png")>0?0:1;		
        $.ajax({
            type:"post",
            dataType:"json",
            url:action,
            data:{"table":table,"colum":colum,"columval":newval,"key":key,"keyval":keyval},
            beforeSend:function(obj){
                $this.attr("src","/data/assets/images/btn_loading.gif");
            },
            success:function(data){
                if(data.code==1){
                    if(src.indexOf("btn_enable.png")>0){
						layer.msg(data.msg, {
						icon: 'success',
						time: 2000,
					});
                        $this.attr("src","/data/assets/images/btn_disable.gif");
                    }else{
						layer.msg(data.msg, {
						icon: 'success',
						time: 2000,
					});
                        $this.attr("src","/data/assets/images/btn_enable.png");
                    }
                }else{
					layer.msg(data.msg, {
						icon: 'cry',
						time: 2000,
					});
                }
                
            }
        });
    });	
	
	
	document.addEventListener('DOMContentLoaded', function(){
   var aluContainer = document.querySelector('.comment-smilies');
    if ( !aluContainer ) return;
    aluContainer.addEventListener('click',function(e){
    var myField,
        _self = e.target.dataset.smilies ? e.target : e.target.parentNode;
        if ( typeof _self.dataset.smilies == 'undefined' ) return;
        var tag = ' ' + _self.dataset.smilies + ' ';
        if (document.getElementById('comment') && document.getElementById('comment').type == 'textarea') {
            myField = document.getElementById('comment')
        } else {
            return false
        }
        if (document.selection) {
            myField.focus();
            sel = document.selection.createRange();
            sel.text = tag;
            myField.focus()
        } else if (myField.selectionStart || myField.selectionStart == '0') {
            var startPos = myField.selectionStart;
            var endPos = myField.selectionEnd;
            var cursorPos = endPos;
            myField.value = myField.value.substring(0, startPos) + tag + myField.value.substring(endPos, myField.value.length);
            cursorPos += tag.length;
            myField.focus();
            myField.selectionStart = cursorPos;
            myField.selectionEnd = cursorPos
        } else {
            myField.value += tag;
            myField.focus()
        }
    });
 });