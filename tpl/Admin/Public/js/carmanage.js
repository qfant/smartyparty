
var url;
function addCar(){
    $('#addCar').dialog('open').dialog('setTitle','添加');
    $('#addCarForm').form('clear');
    url= addCarUrl ;
}

function addCarSubmit(){
    $('#addCarForm').form('submit',{
        url: url,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#addCar').dialog('close');
                $('#CarGrid').datagrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#addCar').dialog('close');
                $('#CarGrid').datagrid('reload');
            }
        }
    });
}
function editCarSubmit(){
    $('#editCarForm').form('submit',{
        url: url,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#editCar').dialog('close');
                $('#CarGrid').datagrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#editCar').dialog('close');
                $('#CarGrid').datagrid('reload');
            }
        }
    });
}
//编辑会员对话窗
function editCar(){
    var row = $('#CarGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要编辑的行", 'info');return false;
    }
    if (row){
        $('#editCar').dialog('open').dialog('setTitle','编辑');
        $('#editCarForm').form('load',row);
        url = editCarUrl +'/id/'+row.id;
    }
}

function destroyCar(){
    var row = $('#CarGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要删除的行", 'info');return false;
    }
    if (row){
        $.messager.confirm('删除提示','真的要删除?',function(r){
            if (r){
                var durl= deleteCarUrl;
                $.getJSON(durl,{id:row.id},function(result){
                    if (result.status){
                        $('#CarGrid').datagrid('reload');    // reload the user data
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
function usingCar(){
    var row = $('#CarGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('警告',"请选择要启用的行", 'info');return false;
    }
    if (row){
        $.messager.confirm('启用提示','真的要启用?',function(r){
            if (r){
                var durl=usingUrl +'/id/'+row.id;
                $.getJSON(durl,{status:row.status},function(result){
                    if (result.status){
                        $('#CarGrid').datagrid('reload');    // reload the user data
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
function forbiddenCar(){
    var row = $('#CarGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('警告',"请选择要禁用的行", 'info');return false;
    }
    if (row){
        $.messager.confirm('停用提示','真的要停用?',function(r){
            if (r){
                var durl=forbiddenUrl +'/id/'+row.id;
                $.getJSON(durl,{status:row.status},function(result){
                    if (result.status){
                        $('#CarGrid').datagrid('reload');    // reload the user data
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
function  formatCar(val,rowData,row){
    if(val==1){
        val="<span style='color: green'>启用</span>";
    }else {
        val="<span style='color: red'>停用</span>";
    }
    return val;
}