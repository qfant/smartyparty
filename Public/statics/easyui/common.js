/**
 * Created by Liarman on 2017/9/7.
 */
function closes(){
    $("#Loading").fadeOut("fast",function(){
        $(this).remove();
    });
}
var pc;
$.parser.onComplete = function(){
     if(pc) clearTimeout(pc);
    pc = setTimeout(closes, 500);
}

//图片添加路径
function imgFormatter(value,row,index){
    if('' != value && null != value){
        var strs = new Array(); //定义一数组
        if(value.substr(value.length-1,1)==","){
            value=value.substr(0,value.length-1)
        }
        strs = value.split(","); //字符分割
        var rvalue ="";
        for (i=0;i<strs.length ;i++ ){
            rvalue += "<img onclick=showimg(\""+strs[i]+"\") style='width:66px; height:60px;margin-left:3px;' src='" + strs[i] + "' title='点击查看图片'/>";
        }
        return  rvalue;
    }
}
//这里需要自己定义一个div   来创建一个easyui的弹窗展示图片
function showimg(img){
    var simg =  img;
    $('.imgdlg').dialog({
        title: '预览',
        width: 400,
        height:400,
        resizable:true,
        closed: false,
        cache: false,
        modal: true
    });
    $(".simg").attr("src",simg);

}