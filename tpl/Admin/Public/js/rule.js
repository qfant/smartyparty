var url;
//添加会员对话窗
function add(id){
    $('#addrule').dialog('open').dialog('setTitle','添加权限');
    $('#rulefm').form('clear');
    if(id>0){
        $("input[name='pid']").val(id);
        var url =getRuleUrl;
        $.getJSON(url,{id:id},function(data){
            $(".parent").empty().append(data.title);
        })
    }
}
function addRuleSubmit(){
    $('#rulefm').form('submit',{
        url: addRuleUrl,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#addrule').dialog('close');
                $('#ruleGrid').treegrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#addrule').dialog('close');
                $('#ruleGrid').treegrid('reload');
            }
        }
    });
}
function editRuleSubmit(){
    $('#ruleeditfm').form('submit',{
        url: editRuleUrl+'/id/'+id,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#editrule').dialog('close');
                $('#ruleGrid').treegrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#editrule').dialog('close');
                $('#ruleGrid').treegrid('reload');
            }
        }
    });
}
//编辑会员对话窗
function editRule(id){
    if (id>0){
        $('#editrule').dialog('open').dialog('setTitle','编辑');
        var url =getRuleUrl;
        $.getJSON(url,{id:id},function(data){
            $("#editRulePid").val(data.pid);
            $("#editRuleId").val(data.id);
            $("#editRuleTitle").textbox('setValue',data.title);
            $("#editRuleName").textbox('setValue',data.name);
        })
        url =editRuleUrl+'/id/'+id;
    }
}
function deleteRule(id){
    $.messager.confirm('删除提示','确定要删除吗?',function(r){
        if (r){
            var durl=deleteRuleUrl;
            $.getJSON(durl,{id:id},function(result){
                if (result.status){
                    $('#ruleGrid').treegrid('reload');    // reload the user data
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

function formatAction(value,row,index){
    id=row.id;
    if (row.addlevel==1){
        var add = '<a href="javascript:void(0);" onclick="add('+id+')">添加子权限</a> ';
    }else{
        add="";
    }
    var del = '<a href="javascript:void(0);" onclick="deleteRule('+id+')">删除</a>';
    var edit = '<a href="javascript:void(0);" onclick="editRule('+id+')">编辑</a>';
    return add+"&nbsp;&nbsp;"+edit+"&nbsp;&nbsp;"+del;
}
