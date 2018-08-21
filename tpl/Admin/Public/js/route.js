var url;
function addRoute(){
    $('#addRoute').dialog('open').dialog('setTitle','添加');
    $('#addRouteForm').form('clear');
    url=addRouteUrl;
}

function addRouteSubmit(){
    $('#addRouteForm').form('submit',{
        url: url,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#addRoute').dialog('close');
                $('#RouteGrid').datagrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#addRoute').dialog('close');
                $('#RouteGrid').datagrid('reload');
            }
        }
    });
}

function editRouteSubmit(){
    $('#editRouteForm').form('submit',{
        url: url,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#editRoute').dialog('close');
                $('#RouteGrid').datagrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#editRoute').dialog('close');
                $('#RouteGrid').datagrid('reload');
            }
        }
    });
}
//编辑会员对话窗
function editRoute(){
    var row = $('#RouteGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要编辑的行", 'info');return false;
    }
    if (row){
        $('#editRoute').dialog('open').dialog('setTitle','编辑');
        $('#editRouteForm').form('load',row);
          url =editRouteUrl+'/id/'+row.id;
    }
}

function destroyRoute(){
    var row = $('#RouteGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要删除的行", 'info');return false;
    }
    if (row){
        $.messager.confirm('删除提示','真的要删除?',function(r){
            if (r){
                var durl=deleteRouteUrl;
                $.getJSON(durl,{id:row.id},function(result){
                    if (result.status){
                        $('#RouteGrid').datagrid('reload');    // reload the user data
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
/**
 * 启用
 */
function usingRoute(){
    var row = $('#RouteGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要启用的行", 'info');return false;
    }
    if (row){
        $.messager.confirm('启用提示','真的要启用?',function(r){
            if (r){
                var durl=usingUrl;
                $.getJSON(durl,{id:row.id},function(result){
                    if (result.status){
                        $('#RouteGrid').datagrid('reload');    // reload the user data
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

/**
 * 禁用
 */
function forbiddenRoute(){
    var row = $('#RouteGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要禁用的行", 'info');return false;
    }
    if (row){
        $.messager.confirm('禁用提示','真的要禁用?',function(r){
            if (r){
                var durl=forbiddenUrl;
                $.getJSON(durl,{id:row.id},function(result){
                    if (result.status){
                        $('#RouteGrid').datagrid('reload');    // reload the user data
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

function RouteformatStatus(val,rowData,row){
    if(val==1){
        val="<span style='color: green'>启用</span>";
    }else {
        val="<span style='color: red'>禁用</span>";
    }
    return val;
}