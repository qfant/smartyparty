/**
 * Created by Liarman on 2017/11/22.
 */
var url;
var loading = false; //为防止onCheck冒泡事件设置的全局变量
function addAdminUser(){
    $('#addAdminUser').dialog('open').dialog('setTitle','添加账号');
    $('#addAdminUserForm').form('clear');
    $('#cc').combobox({
        url:ajaxGroupAllUrl,
        valueField:'id',
        textField:'title',
        multiple:false,
        onChange:function(){
            $("#group_ids").val($('#cc').combobox('getValues').join(','));
        }
    });
    $('#addDepartmentId').combotree({
        url:ajaxDepartmentAllUrl,
        valueField:'id',
        textField:'name',
        multiple:false,
        onLoadSuccess:function(){
            //  alert(1);
            // $('#ccd').combobox('setValues','[]');
        },
        onChange:function(){
            // $("#group_idsd").val($('#ccd').combobox('getValues').join(','));
        }
    });
    var $radios = $('.addstatus');
    $radios.filter('[value=1]').prop('checked', true);
    url= addAdminUrl;
}
function addAdminUserSubmit(){
    $('#addAdminUserForm').form('submit',{
        url: url,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#addAdminUser').dialog('close');
                $('#adminUserGrid').datagrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#addAdminUser').dialog('close');
                $('#adminUserGrid').datagrid('reload');
            }
        }
    });
}
function editAdminUserSubmit(){
    $('#editAdminUserForm').form('submit',{
        url: url,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#editAdminUser').dialog('close');
                $('#adminUserGrid').datagrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#editAdminUser').dialog('close');
                $('#adminUserGrid').datagrid('reload');
            }
        }
    });
}
//编辑会员对话窗
function editAdminUser(){
    var row = $('#adminUserGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要编辑的行", 'info');return false;
    }
    if (row){
        var depatName = row.deptName;
        console.log("部门编号:"+depatName);
        console.log("部门编号:"+row.department_id);
        $('#editAdminUser').dialog('open').dialog('setTitle','编辑');
        $('#editAdminUserForm').form('load',{
            username:row.username,
            id:row.id,
            status:row.status,
            departmentId:row.department_id
        });
        $('#ccd').combobox({
            url:ajaxGroupAllUrl+"/id/"+row.id,
            valueField:'id',
            textField:'title',
            onLoadSuccess:function(){
            },
            onChange:function(){
                $("#group_idsd").val($('#ccd').combobox('getValues').join(','));
            }
        });
        $('#editDepartmentId').combotree({
            url:ajaxDepartmentAllUrl,
            valueField:'id',
            textField:'name',
            multiple:false,
            onLoadSuccess: function(node,data){
                $('#editDepartmentId').combotree('setValue', row.department_id);
            },
            onChange:function(){
            }
        });
        url =editAdminUrl+'/id/'+row.id;
    }
}
function destroyAdminUser(){
    var row = $('#adminUserGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要删除的行", 'info');return false;
    }
    if (row){
        $.messager.confirm('删除提示','真的要删除?',function(r){
            if (r){
                var durl=deleteAdminUrl;
                $.getJSON(durl,{id:row.id},function(result){
                    if (result.status){
                        $('#adminUserGrid').datagrid('reload');    // reload the user data
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
function formatStatus(val,rowData,row){
    if(val==1){
        val="<span style='color: green'>可用</span>";
    }else {
        val="<span style='color: red'>不可用</span>";
    }
    return val;
}