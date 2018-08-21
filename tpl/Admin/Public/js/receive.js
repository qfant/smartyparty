var url;
function addRece(){
    $('#addRece').dialog('open').dialog('setTitle','添加');
    $('#addReceForm').form('clear');
    url=addReceUrl;
}

function addReceSubmit(){
    $('#addReceForm').form('submit',{
        url: url,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#addRece').dialog('close');
                $('#receGrid').datagrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#addRece').dialog('close');
                $('#receGrid').datagrid('reload');
            }
        }
    });
}
function editReceSubmit(){
    $('#editReceForm').form('submit',{
        url: url,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#editRece').dialog('close');
                $('#receGrid').datagrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#editRece').dialog('close');
                $('#receGrid').datagrid('reload');
            }
        }
    });
}
//编辑会员对话窗
function editRece(){
    var row = $('#receGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要编辑的行", 'info');return false;
    }
    if (row){
        $('#editRece').dialog('open').dialog('setTitle','编辑');

        $('#editReceForm').form('load',row);

          url =editReceUrl+'/id/'+row.id;
    }
}

function destroyRece(){
    var row = $('#receGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要删除的行", 'info');return false;
    }
    if (row){
        $.messager.confirm('删除提示','真的要删除?',function(r){
            if (r){
                var durl=deleteReceUrl;
                $.getJSON(durl,{id:row.id},function(result){
                    if (result.status){
                        $('#receGrid').datagrid('reload');    // reload the user data
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

function doSearch(){
    $('#receGrid').datagrid('load',{
        receivername: $('#receivenamesearch').val(),
        receivertel: $('#receivetelsearch').val()

    });
}



