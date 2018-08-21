/**
 * Created by ZH on 2017/11/23.
 */
function destroy(){
    var row = $('#BindingGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要删除的行", 'info');return false;
    }
    if (row){
        $.messager.confirm('删除提示','真的要删除?',function(r){
            if (r){
                var durl =deleteBindingUrl+'/id/'+row.id;
                $.getJSON(durl,{id:row.id},function(result){
                    if (result.status){
                        $('#BindingGrid').datagrid('reload');    // reload the user data
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

function formatAction(value,row,index){
    id=row.id;
    var del = '<a href="javascript:void(0);" onclick="destroy('+id+')">删除</a>';
    return del;
}

function doSearch(){
    $('#BindingGrid').datagrid('load',{
        name: $('#namesearch').val()
    });
}