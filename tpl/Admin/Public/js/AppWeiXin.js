/**
 * Created by ZH on 2017/11/28.
 */
var url;
function addAppNav(){
    $('#addApp').dialog('open').dialog('setTitle','添加');
    $('#addAppForm').form('clear');
    url=addAppWeiXinUrl;
}
function addAppNavSubmit(){
    $('#addAppForm').form('submit',{
        url: url,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#addApp').dialog('close');
                $('#appGrid').datagrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#addApp').dialog('close');
                $('#appGrid').datagrid('reload');
            }
        }
    });
}
function editAppNavSubmit(){
    $('#editAppForm').form('submit',{
        url: url,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#editApp').dialog('close');
                $('#appGrid').datagrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#editApp').dialog('close');
                $('#appGrid').datagrid('reload');
            }
        }
    });
}
//编辑会员对话窗
function editAppNav(){
    var row = $('#appGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要编辑的行", 'info');return false;
    }
    if (row){
        $('#editApp').dialog('open').dialog('setTitle','编辑');
        $('#editAppForm').form('load',row);
        url =editAppWeiXinUrl+'/id/'+row.id;
    }
}
function destroyAppNav(){
    var row = $('#appGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要删除的行", 'info');return false;
    }
    if (row){
        $.messager.confirm('删除提示','真的要删除?',function(r){
            if (r){
                var durl=deleteAppWeiXinUrl;
                $.getJSON(durl,{id:row.id},function(result){
                    if (result.status){
                        $('#appGrid').datagrid('reload');    // reload the user data
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