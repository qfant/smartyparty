/**
 * Created by ZH on 2017/11/23.
 */
$(document).ready(function () {
    var addurl=addBindkeyUrl;
    $(".bigbtn").click(function(){
        $.getJSON(addurl, function(data){
            if(data.status==1){
                alert('生成成功！');
                location.href="{:U('Bindkey/index')}";
            }
        });
    });
});
function destroy(){
    var row = $('#BindKeyGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要删除的行", 'info');return false;
    }
    if (row){
        $.messager.confirm('删除提示','真的要删除?',function(r){
            if (r){
                var durl =deleteBindkeyUrl+'/id/'+row.id;
                $.getJSON(durl,{id:row.id},function(result){
                    if (result.status){
                        $('#BindKeyGrid').datagrid('reload');    // reload the user data
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
function  attrValue(val,rowData,row){
    if(val==1){
        val="可用";
    }else{
        val="不可用";
    }
    return val;
}
function formatAction1(value,row,index){
    id=row.id;
    var del = '<a href="javascript:void(0);" onclick="destroy('+id+')">删除</a>';
    return del;
}

function doSearch(){
    $('#BindKeyGrid').datagrid('load',{
        key: $('#keysearch').val()
    });
}