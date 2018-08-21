// JavaScript Document
var flage = null;
 var open ='';
var CM = {
    subOrganization:null,
    ajax: new HttpService(),
    urlParam: common.getUrlArgObject(),
    loadPage: function() {
        var id = common.getParamFromUrl("id");
        flage = common.getParamFromUrl("flagPage");

        if (CM.urlParam.id) {
            $("#_title").text("编辑生活记录");
            this.ajax.get("meeting/back/selcetsingle/" + CM.urlParam.id, {}, function(data) {
                console.log(data);
                if (data.code == "1001") {
                    CM.setEditText(data.result);
                }
            });
        } else {
             open=$('#isPublic').attr("checked");
            //资料添加          
            $('#uploadFiles').uploadFiles({
                btnText: '添加资料',
            });
            $('')
            CM.setSubOrgOption(null);
        }
        CM.selectTag();
    },
    setSubOrgOption:function(subOrganization){
        this.ajax.get("organization/back/managerorg", {}, function(data) {
            if (data.code == "1001" && data.result.length > 0) {
                var orglist = data.result;
                for (var i = 0; i < orglist.length; i++) {
                    var item = orglist[i];
                    var check = item._id == subOrganization ? "selected":"";
                    $("#subOrg").append('<option value="'+item._id+'" '+check+'>'+item.name+'</option>');
                }
            } else {
                common.nomalPrompt(data.result);
            }
        })
    },
    setEditText: function(res) {
        //已发布-编辑，“发布”按钮变灰，点击“保存”会议状态不变

        if (res.status == 2) {
            $('#publishBtn').attr('disabled', 'disabled');
            $('#saveBtn').attr('onclick', 'checkedSuss(this,CM.save,\'22\')');
        }
        $("#title").val(res.title);
        $("#meetingType").val(res.type);
        $("#startTime").val(res.beginDate);
        $("#endTime").val(res.endDate);
        $("#meetQues").val(res.subject);
        $("#meetTrans").val(res.schedule);
        //  open = $('#ispublic').attr('checked');
        // $('#isPublic').attr("checked",open);
        // res.isPublic == 1 ? $("#isPublic").attr('checked', open) : '';
        // $("#place").val(res.location);
        var tagsStr = JSON.stringify(res.tags);
        $.each($("#selectTags").find('.btn'), function(i, item) {
            var thisId = $(this).attr('data-id'),
                inTags = tagsStr.indexOf(thisId) >= 0;
            inTags ? $(this).addClass('btn-primary').removeClass('btn-default') : '';
        });
        if (res.titlePic) {
            $('li.upload-pic-btn').addClass('edit');
            common.writeUploadPic(res.titlePic, $('li.upload-pic-btn'));
        };
        var lngLatStr = res.position.longitude + "," + res.position.latitude;
        $("#lnglat").text('坐标：' + lngLatStr).data('val', lngLatStr);
        var lnglatWidth = $("#lnglat").outerWidth() + 10;
        $("#address").val(res.location).css('width','calc(100% - '+lnglatWidth+'px)');
        //资料添加
        $('#uploadFiles').uploadFiles({
            btnText: '添加资料',
            files: res.files
        });
        CM.addPersons(res.attendUser, res.recorder);
        //有签到数据后，不允许对开始时间、地点、坐标、出席人员更改，只判断“结束时间须大于开始时间”
        if (res.signMeeting.signNum > 0) {
            $('#startTime').attr('disabled', 'disabled').removeClass('form-check');
            $('#address').attr('disabled', 'disabled');
            $('#lnglat').attr({
                'onclick': '',
                'disabled': 'disabled'
            });
            var $attendPersons = $('#attendPersons');
            $attendPersons.siblings().remove();
            $attendPersons.find('.del').remove();
            $attendPersons.find('.list').attr('onclick', '');
        }
        $('#isPublic').attr("checked",res.isPublic == 1);
        CM.setSubOrgOption(res.subOrganization);
    },
        
    showDeleteImg: function(el) {
        $(el).find(".imgfix").animate({
            "opacity": "0.5"
        });
        $(el).find(".btn").animate({
            "opacity": "1"
        });
    },
    hideDeleteImg: function(el) {
        $(el).find(".imgfix").animate({
            "opacity": "0"
        });
        $(el).find(".btn").animate({
            "opacity": "0"
        });
    },
    selectTag: function() {
        $("#selectTags").empty();
        var param = {
            pageNo: 1,
            pageSize: this.ajax.MAX_VALUE
        }
        this.ajax.post("meetingtag/back/select", param, function(data) {
            if (data.code == "1001") {
                $.each(data.result.list, function(i, item) {
                    $("#selectTags").append('<div class="btn btn-default mR5 mB5" data-id=\'' + item._id + '\' onclick="CM.appendTag(this);">' + item.name + '</div>');

                })
            }
        });
    },
    appendTag: function(el) {
        var choose = $(el).hasClass('btn-primary');
        if (choose) {
            $(el).removeClass('btn-primary').addClass('btn-default');
        } else {
            $(el).addClass('btn-primary').removeClass('btn-default');
        }
    },
    // pub:function(status){
    //     console.log(status)
    //     common.selectMyOrg(function (data) {
    //         CM.subOrganization = data[0]._id;
    //         CM.save(status)
    //     },CM.subOrganization )
    // },
    save: function(status) {
        var title = $.trim($("#title").val());
        var meetingType = $.trim($("#meetingType").val());
        var startTime = $.trim($("#startTime").val());
        var startTimeCheck = $("#startTime").attr('disabled');
        var endTime = $.trim($("#endTime").val());
        var meetQues = $.trim($("#meetQues").val());
        var meetTrans = $.trim($("#meetTrans").val());
        var address = $.trim($("#address").val());
        var isPublic = $("#isPublic").is(':checked') ? 1 : 0;
        var subOrg = $("#subOrg").val();
        if (startTimeCheck == undefined && Date.parse(startTime) <= (new Date()).getTime()) {
            common.nomalPrompt("开始时间须大于当前时间");
            return;
        }
        if (Date.parse(endTime) <= Date.parse(startTime)) {
            common.nomalPrompt("结束时间须大于开始时间");
            return;
        }
        var lnglatVal = $("#lnglat").data('val'),
            lnglat = lnglatVal && lnglatVal.indexOf(',') >= 0 ? lnglatVal.split(',') : '',
            long = "",
            lat = "";
        if (lnglat && lnglat.length == 2) {
            long = lnglat[0];
            lat = lnglat[1];
        } else {
            common.nomalPrompt("请设置有效签到位置");
            return;
        }
        var tags = [];
        $("#selectTags").find(".btn.btn-primary").each(function(i, item) {
            tags.push($(item).attr("data-id"));
        });
        var attendUser = [],
            recorder = "";
        $("#attendPersons").find(".list").each(function(i, item) {
            var id = $(item).data("id");
            attendUser.push(id);
            if ($(item).hasClass("record-person")) {
                recorder = id;
            }
        });
        if (attendUser.length == 0) {
            common.nomalPrompt("请添加出席人员");
            return;
        }
        if (recorder == "") {
            common.nomalPrompt("请设置记录人员");
            return;
        }
        var headPic = '';
        $('li.upload-pic-btn').siblings('li').each(function() {
            var imgUrl = $(this).attr('imgUrl');
            if (imgUrl) headPic = imgUrl;

        });
        var param = {
                title: title,
                type: meetingType,
                location: address,
                longitude: long,
                latitude: lat,
                endDate: endTime,
                beginDate: startTime,
                status: status,
                tags: JSON.stringify(tags),
                subject: meetQues,
                schedule: meetTrans,
                titlePic: headPic,
                files: JSON.stringify($("#uploadFiles").data("files")),
                attendUser: JSON.stringify(attendUser),
                recorder: recorder,
                isPublic: isPublic,
                subOrganization:subOrg
            };

             console.log(param,CM.urlParam.id)
            apiurl = "meeting/back/savemeeting",
            sussText = status == '2' ? '发布成功' : '新增成功';
        if (CM.urlParam.id != null) {
            apiurl = "meeting/back/editmeeting",
            param.meetingId = CM.urlParam.id;
            sussText = '保存成功';
        }
       if (status == '2') {
            top.layer.confirm('发布后无法删除，确定要发布吗？', {
                btn: ['确定', '取消'], //按钮
                title: "提示",
                icon: 0,
                closeBtn: false,
                shade: ['0.3', '#000'] //不显示遮罩
            }, function() {
                CM.ajax.post(apiurl, param, function(data) {
                    if (data.code == "1001") {
                        //编辑成功，去发布
                        if (CM.urlParam.id && status == '2') {
                            // CM.ajax.get('meeting/back/publish/' + CM.urlParam.id, {}, function(data) {
                                if (data.code == "1001") {
                                    common.nomalPrompt("发布成功", CM.back);
                                } else if (data.code == "1002") {
                                    common.nomalPrompt(data.message);
                                } else {
                                    common.nomalPrompt(data.result);
                                }
                            // });
                        } else {
                            common.nomalPrompt(sussText, CM.back);
                        };
                    } else if (data.code == "1002") {
                        common.nomalPrompt(data.message);
                    } else {
                        common.nomalPrompt(data.result);
                    }
                });
            }, function() {});
        } else {
            if(status=='22'){
                param.status='2';
            };
            CM.ajax.post(apiurl, param, function(data) {
                if (data.code == "1001") {
                    //编辑成功，去发布
                    if (CM.urlParam.id && status == '0') {
                        // CM.ajax.get('meeting/back/publish/' + CM.urlParam.id, {}, function(data) {
                            if (data.code == "1001") {
                                common.nomalPrompt("保存成功", CM.back);
                            } else if (data.code == "1002") {
                                common.nomalPrompt(data.message, CM.back);
                            } else {
                                common.nomalPrompt(data.result);
                            }
                        // });
                    } else {
                        common.nomalPrompt(sussText, CM.back);
                    };
                } else if (data.code == "1002") {
                    common.nomalPrompt(data.message);
                    CM.back();
                } else {
                    common.nomalPrompt(data.result);
                }
            });
        }   
    },
    addPersons: function(data, recorder) {
        $("#attendPersons").empty();
        recorder = recorder || "";
        $.each(data, function(i, item) {
            var isRecordPerson = recorder == item._id ? "record-person" : "";
            $("#attendPersons").append('<div data-id="' + item.id + '" class="list ' + isRecordPerson + '" onclick="$(this).addClass(\'record-person\').siblings().removeClass(\'record-person\');">' +
                '<div class="del" onclick="$(this).parent().remove();"><i class="fa fa-times-circle" ></i></div>' +
                '<div class="image-auto-fix img-circle" >' +
                '</div>' +
                '<p class="text-center elc mB0">' + item.truename + '</p>' +
                '</div>');
        })

    },
    back: function(isCallback) {
        var path = '';
        if (isCallback) path = 'html/meeting/';
        location.href = path + "list.html?from=add";
    },

}

$(function() {
    // AddMinute.loadPage();//load tabel data
    CM.loadPage();
    $('#lnglat').getLnglat({
        //$address: $('#address')//20180202改为不设置地址详情
    }, function($e, data) {
        if (data) {
            var res = data.lng + ',' + data.lat;
            $e.text('坐标：' + res).data('val', res);
            //20180202改为不设置地址详情
            //$('#address').val(data.address);
        }
    });
    // laydate.render({
    //     elem: '#startTime',
    //     type: 'datetime',
    //     format: 'yyyy-MM-dd HH:mm:ss', //可任意组合
    //     done: function() {
    //         //控件选择完毕后的回调
    //         //选择完成，失去焦点，以便验证
    //         // var $e = $(this.elem);
    //
    //         // setTimeout(function() {
    //         //     $e.focus();
    //         // }, 100);
    //         // setTimeout(function() {
    //         //     $e.blur();
    //         // }, 200);
    //     }
    // });
    // laydate.render({
    //     elem: '#endTime',
    //     type: 'datetime',
    //     format: 'yyyy-MM-dd HH:mm:ss', //可任意组合
    //     done: function() {
    //         //控件选择完毕后的回调
    //         //选择完成，失去焦点，以便验证
    //         // var $e = $(this.elem);
    //         // $e.focus();
    //         // setTimeout(function() {
    //         //     $e.blur();
    //         // }, 100);
    //     }
    // });

    //出席人员添加
    $('#addPersons').on('click', function() {
        var $that = $(this),
            $hasItem = $("#attendPersons").find('.list'), //已选人员
            hasIds = [];
        //弹出人员选择器
        var openSelBox = function(hasIds, recorder) {
            common.selectOrgPersons(0, function(data) {
                CM.addPersons(data, recorder);
            }, hasIds, 'manage');
        };
        if ($hasItem.length > 0) {
            //有已选人员时，遍历查出人员id再弹出人员选择器
            var recorder = '';
            $hasItem.each(function(i) {
                //存入已有人员id
                hasIds.push($(this).data('id'));
                //存入记录人id
                $(this).hasClass('record-person') ? recorder = $(this).data('id') : '';
                //遍历到最后一条，弹出人员选择器
                i == ($hasItem.length - 1) ? openSelBox(hasIds, recorder) : '';
            });
        } else {
            //没有已选人员，直接弹出人员选择器
            openSelBox(hasIds);
        };
    });
});
