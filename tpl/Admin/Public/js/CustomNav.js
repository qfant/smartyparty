/**
 * Created by ZH on 2017/11/28.
 */
var url;
//添加会员对话窗
function newMenu(){
    $('#Customdlg').dialog('open').dialog('setTitle','添加菜单');
    $('#CustomNavfm').form('clear');
    url=addCustomNavUrl;
}
function addMenu(){
    $('#CustomNavfm').form('submit',{
        url: url,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#Customdlg').dialog('close');
                $('#CustomMenuGrid').treegrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#Customdlg').dialog('close');
                $('#CustomMenuGrid').treegrid('reload');
            }
        }
    });
}
function editMenuSubmit(){
    $('#CustomNavfmEdit').form('submit',{
        url: url,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#CustomNavdlgEdit').dialog('close');
                $('#CustomMenuGrid').treegrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#CustomNavdlgEdit').dialog('close');
                $('#CustomMenuGrid').treegrid('reload');
            }
        }
    });
}
//编辑会员对话窗
function editMenu(){
    var row = $('#CustomMenuGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要编辑的行", 'info');return false;
    }
    if (row){
        $('#CustomNavdlgEdit').dialog('open').dialog('setTitle','编辑');
        $('#CustomNavfmEdit').form('load',row);
        if(row.pid==0){
            $('.pidedit').combobox('loadData',[{'id':0,'name':'无'}]);
        }else {
            $('.pidedit').combobox('loadData',{});
            $('.pidedit').combobox({url:CustomerNavLevelUrl});
            $('.pidedit').combobox('setValue', row.pid);
        }
        if(row.kind==3){
            document.getElementById('news1').style.display = "";
            document.getElementById('menuvalue1').style.display = "none";
        }else{
            document.getElementById('news1').style.display = "none";
            document.getElementById('menuvalue1').style.display = "";
        }
        url =editCustomerNavUrl+'/id/'+row.id;
    }
}
function destroyMenu(){
    var row = $('#CustomMenuGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要删除的行", 'info');return false;
    }
    if (row){
        $.messager.confirm('删除提示','真的要删除此菜单吗?删除将不能再恢复!',function(r){
            if (r){
                var durl=deleteCustomerNavUrl;
                $.getJSON(durl,{id:row.id},function(result){
                    if (result.status){
                        $('#CustomMenuGrid').treegrid('reload');    // reload the user data
                    } else {
                        $.messager.alert('错误提示',result.message,'error');
                    }
                },'json').error(function(data){
                    var info=eval('('+data.responseText+')');
                    $.messager.confirm('错误提示',info.message,function(r){

                    });
                });
            }
        });
    }
}
$('#pid').combobox({});

function shownews(t){
    if (t == 2) {
        document.getElementById('news').style.display = "";
        document.getElementById('menuvalue').style.display = "none";
    }else{
        document.getElementById('news').style.display = "none";
        document.getElementById('menuvalue').style.display = "";
    }
}
function show(t){
    if (t == 2) {
        document.getElementById('news1').style.display = "";
        document.getElementById('menuvalue1').style.display = "none";
    }else{
        document.getElementById('news1').style.display = "none";
        document.getElementById('menuvalue1').style.display = "";
    }
}
function formatAction(value,row,index){
    id=row.id;
    var edit = '<a href="javascript:void(0);" onclick="editMenu('+id+')">编辑</a>';
    var del = '<a href="javascript:void(0);" onclick="destroyMenu('+id+')">删除</a>';

    return edit+"&nbsp;&nbsp;"+del;
}
function kindAction(val,rowData,row){
    if(val==1){
        val="链接";
    }else if(val==2){
        val="命令";
    }else if(val==3){
        val="自定义图文";
    }
    return val;
}
