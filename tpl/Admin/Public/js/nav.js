/**
 * Created by Liarman on 2017/11/22.
 */
var url;
//添加会员对话窗
function newMenu(){
    $('#dlg').dialog('open').dialog('setTitle','添加菜单');
    $('#fm').form('clear');
    url=addNavUrl;
}
function addMenu(){
    $('#fm').form('submit',{
        url: url,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#dlg').dialog('close');
                $('#menuGrid').treegrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#dlg').dialog('close');
                $('#menuGrid').treegrid('reload');
            }
        }
    });
}
function editMenuSubmit(){
    $('#fmEdit').form('submit',{
        url: url,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#dlgEdit').dialog('close');
                $('#menuGrid').treegrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#dlgEdit').dialog('close');
                $('#menuGrid').treegrid('reload');
            }
        }
    });
}
//编辑会员对话窗
function editMenu(){
    var row = $('#menuGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要编辑的行", 'info');return false;
    }
    if (row){
        $('#dlgEdit').dialog('open').dialog('setTitle','编辑');
        $('#fmEdit').form('load',row);
        if(row.pid==0){
            $('.pidedit').combobox('loadData',[{'id':0,'name':'无'}]);
        }else {
            $('.pidedit').combobox('loadData',{});
            $('.pidedit').combobox({url:menuLevelUrl});
            $('.pidedit').combobox('setValue', row.pid);
        }
        url =editNavUrl+'/id/'+row.id;
    }
}
function destroyMenu(){
    var row = $('#menuGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要删除的行", 'info');return false;
    }
    if (row){
        $.messager.confirm('删除提示','真的要删除此菜单吗?删除将不能再恢复!',function(r){
            if (r){
                $.getJSON(deleteNavUrl,{id:row.id},function(result){
                    if (result.status){
                        $('#menuGrid').treegrid('reload');    // reload the user data
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