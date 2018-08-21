var url;

$(function () {
    var oTable = new TableInit();
    oTable.Init();
    var oButtonInit = new ButtonInit();
    oButtonInit.Init();
    $("#search").click(function() {
        var title = $("#title").val();
        $.ajax({
            type: "post",
            url: ajaxListUrl,
            data: {title : title},
            dataType:"json",
            success : function(json) {
                $("#newsGrid").bootstrapTable('load', json);//主要是要这种写法
            }
        });

    });
});
var TableInit = function () {
    var oTableInit = new Object();
    oTableInit.Init = function () {
        $('#newsGrid').bootstrapTable({
            url: ajaxListUrl,         //请求后台的URL（*）
            method: 'get',                      //请求方式（*）
            toolbar: '#toolbar_news',                //工具按钮用哪个容器
            striped: true,                      //是否显示行间隔色
            cache: false,                       //是否使用缓存，默认为true，所以一般情况下需要设置一下这个属性（*）
            pagination: true,                   //是否显示分页（*）
            queryParams: oTableInit.queryParams,//传递参数（*）
            sidePagination: "server",           //分页方式：client客户端分页，server服务端分页（*）
            queryParams: function (params) {
                return {
                    rows: params.limit,                         //页面大小
                    page: (params.offset / params.limit) + 1,   //页码
                }
            },
            pageNumber:1,                       //初始化加载第一页，默认第一页
            pageSize: 10,                       //每页的记录行数（*）
            pageList: [10, 20, 50, 100],        //可供选择的每页的行数（*）
            strictSearch: true,
            showColumns: true,                  //是否显示所有的列
            showRefresh: true,                  //是否显示刷新按钮
            minimumCountColumns: 2,             //最少允许的列数
            clickToSelect: true,                //是否启用点击选中行
            uniqueId: "ID",                     //每一行的唯一标识，一般为主键列
            showToggle:true,                    //是否显示详细视图和列表视图的切换按钮
            cardView: false,                    //是否显示详细视图
            detailView: false,                   //是否显示父子表
            singleSelect : true,
            columns: [{
                checkbox: true
            },{
                    field: 'id',
                    title: '编号',
                    width : '100',
                    visible:false,
                }, {
                    field: 'title',
                    title: '标题',
                    width : '260'
                }, {
                    field: 'type',
                    title: '类型',
                    width : '260',
                    formatter : function (value, row, index) {
                        if (row['type'] == 1) {
                            return '政策宣传';
                        }
                        if (row['type'] == 2) {
                            return '党建业务';
                        }
                        if(row['type'] == 3){
                            return '工作动态';
                        }
                        return value;
                    }
                }, {
                    field: 'createtime',
                    title: '创建时间',
                    width : '260',
                    formatter : function (value, row, index) {
                        if(value==null||value==0){
                            return "";
                        }else{
                            var newDate = new Date();
                            newDate.setTime(value * 1000);
                            if (newDate.getFullYear() < 1900) {
                                return "";
                            }
                            var val = newDate.format("yyyy-MM-dd hh:mm:ss");
                            return val;
                        }
                    }
                }, {
                    field: 'intro',
                    title: '内容'
                }
            ]


        });
    };
    return oTableInit;
};
var ButtonInit = function () {
    var oInit = new Object();
    var postdata = {};
    oInit.Init = function () {
    };
    return oInit;
};



$(function(){
    $('#editor').summernote({
        height: 200,
        width:500,
        minHeight: null,
        maxHeight: null,
        focus: true
    });
    $('#editor1').summernote({
        height: 200,
        minHeight: null,
        maxHeight: null,
        focus:true
    });
});

function addNewsSubmit(){
    var sHTML = $('#editor').code();
    $('#addIntro').val(sHTML);
    $('#addNewsForm').form('submit',{
        url: addNewsUrl,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $("#myModalAdd").modal('hide');
                $("#newsGrid").bootstrapTable('refresh',{url : ajaxListUrl});
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $("#myModalAdd").modal('hide');
                $("#newsGrid").bootstrapTable('refresh',{url : ajaxListUrl});
            }
        }
    });
}

//编辑会员对话窗
function editNews(){
    var row = $("#newsGrid").bootstrapTable('getSelections');
    console.log(row);
    if(row==null || row==""||row=="unfinded"){
        $.messager.alert('Warning',"请选择要编辑的行", 'info');return false;
    }
    if (row){
        $('#editNewsForm').form('load',row[0]);
        $("#editor1").code(row[0].intro);
        $("#myModalEdit").modal('show');
        url =editNewsUrl+'/id/'+row[0].id;
    }
}

function editNewsSubmit(){
    var contentEdit = $('#editor1').code();
    $('#editIntro').val(contentEdit);
    $('#editNewsForm').form('submit',{
        url: url,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $("#myModalEdit").modal('hide');
                $("#newsGrid").bootstrapTable('refresh',{url : ajaxListUrl});
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $("#myModalEdit").modal('hide');
                $("#newsGrid").bootstrapTable('refresh',{url : ajaxListUrl});
            }
        }
    });
}

function destroyNews(){
    var row = $("#newsGrid").bootstrapTable('getSelections');
    if(row==null || row==""||row=="unfinded"){
        $.messager.alert('Warning',"请选择要删除的行", 'info');return false;
    }
    if (row){
        $.messager.confirm('删除提示','真的要删除?',function(r){
            if (r){
                var durl=deleteNewsUrl;
                $.getJSON(durl,{id:row[0].id},function(result){
                    if (result.status){
                        $("#newsGrid").bootstrapTable('refresh',{url : ajaxListUrl});
                    } else {
                        $.messager.alert('错误提示',result.message,'error');
                    }
                },'json').error(function(data){
                    var info=eval('('+data.responseText+')');
                    $.messager.confirm('错误提示',info.message,function(r){});
                });
            }
        });
    }
}
