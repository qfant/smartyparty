var url;
function addShipper(){
    $('#adShipper').dialog('open').dialog('setTitle','添加');
    $('#addShipperForm').form('clear');
    url=addShipperUrl;
}

function addShipperSubmit(){
    $('#addShipperForm').form('submit',{
        url: url,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#adShipper').dialog('close');
                $('#ShipperGrid').datagrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#adShipper').dialog('close');
                $('#ShipperGrid').datagrid('reload');
            }
        }
    });
}
function editShipperSubmit(){
    $('#editShipperForm').form('submit',{
        url: url,
        onSubmit: function(){
            return $(this).form('validate');
        },
        success:function(data){
            data=$.parseJSON(data);
            if(data.status==1){
                $.messager.alert('Info', data.message, 'info');
                $('#editShipper').dialog('close');
                $('#ShipperGrid').datagrid('reload');
            }else {
                $.messager.alert('Warning', data.message, 'info');
                $('#editShipper').dialog('close');
                $('#ShipperGrid').datagrid('reload');
            }
        }
    });
}
//编辑会员对话窗
function editShipper(){
    var row = $('#ShipperGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要编辑的行", 'info');return false;
    }
    if (row){
        $('#editShipper').dialog('open').dialog('setTitle','编辑');

        $('#editShipperForm').form('load',row);

          url =editShipperUrl+'/id/'+row.id;
    }
}

function destroyShipper(){
    var row = $('#ShipperGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择要删除的行", 'info');return false;
    }
    if (row){
        $.messager.confirm('删除提示','真的要删除?',function(r){
            if (r){
                var durl=deleteShipperUrl;
                $.getJSON(durl,{id:row.id},function(result){
                    if (result.status){
                        $('#ShipperGrid').datagrid('reload');    // reload the user data
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
    $('#ShipperGrid').datagrid('load',{
        shippertel: $('#shippertelsearch').val(),
        shipper: $('#shippersearch').val()

    });
}

function doReceiveS(){
    $('#receiveListGrid').datagrid('load',{
        receivername: $('#renamesear').val(),
        receivertel: $('#retelsear').val()
    });
}

function ajaxReceiveListByid(){
    var row = $('#ShipperGrid').datagrid('getSelected');
    if(row==null){
        $.messager.alert('Warning',"请选择发货人", 'info');return false;
    }
    $('#receiveDlg').dialog('open').dialog('setTitle','对应收货人列表');
    console.log(row.id);
    $('#getshipperid').textbox('setValue', row.id);
    $('#receiveGrid').datagrid({
        url:ajaxReceiveUrl+'/shipperid/'+row.id,
        columns:[[
            {field:'id',title:'收货人id',width:150,hidden:'true'},
            {field:'receivername',title:'姓名',width:150},
            {field:'receivertel',title:'电话',width:150},
            {field:'receiveraddress',title:'地址',width:200}
        ]],
        toolbar: [{
            iconCls: 'fa fa-plus',
            id:'ButonGetCheck',
            handler: function(){ajaxreceiveList();}
        }]
    });
}

function ajaxreceiveList(){
    $('#receiveListDlg').dialog('open').dialog('setTitle','收货人列表');
    $('#receiveListGrid').datagrid({
        url:ajaxReceiveUrl,
        columns:[[
            {field:'id',title:'收货人id',width:150,hidden:'true'},
            {field:'receivername',title:'姓名',width:150},
            {field:'receivertel',title:'电话',width:150},
            {field:'receiveraddress',title:'电话',width:150},
            {field:'opt',title:'操作',width:50,align:'center',
                formatter:function(value,row,index){
                    var btn = '<a class="editcls" onclick="chooseReceive(\''+row.id+'\')" href="javascript:void(0)">确认</a>';
                    return btn;
                }
            }
        ]]

    });
}


function chooseReceive(id){
    var row = $('#receiveListGrid').datagrid('getSelected');//发车行
    var id=row.id;//收货人id

    var shipperid= $('#getshipperid').val();//发货人id
    if(row==null){
        $.messager.alert('Warning',"请选择发货人", 'info');return false;
    }
    if (row){
        $.messager.confirm('确认提示','真的要和收货人建立关系吗?',function(r){
            if (r){
                var durl=chooseReceiveUrl;
                $.getJSON(durl,{id:id,shipperid:shipperid},function(result){

                    if (result.status==1){
                        $('#receiveListDlg').dialog('close');
                        $('#receiveGrid').datagrid('reload');    // reload the user data

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


