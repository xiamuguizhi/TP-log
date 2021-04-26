/**
 * 开发版本的文件导入
 */
(function (){
    var paths  = [
		'ui/button.js',
		'ui/uibase.js',
		'ui/mask.js',
		'ui/colorpicker.js',
		'ui/separator.js',
		'ui/splitbutton.js',
        'ui/colorbutton.js',
        'ui/tablebutton.js',
		'ui/stateful.js',
		'ui/autotypesetpicker.js',
        'ui/autotypesetbutton.js',
		'ui/cellalignpicker.js',
		'ui/iconfont.js',
		'ui/toolbar.js',
		'ui/breakline.js',
		'ui/menubutton.js',
        'ui/multiMenu.js',
		'ui/pastepicker.js',
        ],
        baseURL = '/addon/ueditor/view/static/ueditor/';
    for (var i=0,pi;pi = paths[i++];) {
        document.write('<script type="text/javascript" src="'+ baseURL + pi +'"></script>');
    }
})();
