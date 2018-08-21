(function($) {
    /**
     * jQuery EasyUI 1.4 --- 功能扩展
     *
     * Copyright (c) 2009-2015 RCM
     *
     * 新增 validatebox 校验规则
     *
     */
    $.extend($.fn.validatebox.defaults.rules, {
        idcard: {
            validator: function(value, param) {
                return idCardNoUtil.checkIdCardNo(value);
            },
            message: '请输入正确的身份证号码'
        },
        checkNum: {
            validator: function(value, param) {
                return /^([0-9]+)$/.test(value);
            },
            message: '请输入整数'
        },
        checkFloat: {
            validator: function(value, param) {
                return /^[+|-]?([0-9]+\.[0-9]+)|[0-9]+$/.test(value);
            },
            message: '请输入合法数字'
        },
        CHS: {        //验证汉子
            validator: function (value) {
                return /^[\u0391-\uFFE5]+$/.test(value);
            },
            message: '只能输入汉字'
        },
        mobile: {//value值为文本框中的值        //移动手机号码验证
            validator: function (value) {
                var reg = /^1[3|4|5|7|8|9]\d{9}$/;
                return reg.test(value);
            },
            message: '输入手机号码格式不准确.'
        },
        phone: {//value值为文本框中的值        //手机、电话
            validator: function (value) {
                var reg = /^([1[3|4|5|7|8|9]\d{9}|(\(\d{3,4}\)|\d{3,4}-|\s)?\d{7,14}])$/;
                return reg.test(value);
            },
            message: '输入手机号码格式不准确.'
        },
        zipcode: {        //国内邮编验证
            validator: function (value) {
                var reg = /^[1-9]\d{5}$/;
                return reg.test(value);
            },
            message: '邮编必须是非0开始的6位数字.'
        },
        account: {//param的值为[]中值        //用户账号验证(只能包括 _ 数字 字母)
            validator: function (value, param) {
                if (value.length < param[0] || value.length > param[1]) {
                    $.fn.validatebox.defaults.rules.account.message = '用户名长度必须在' + param[0] + '至' + param[1] + '范围';
                    return false;
                } else {
                    if (!/^[\w]+$/.test(value)) {
                        $.fn.validatebox.defaults.rules.account.message = '用户名只能数字、字母、下划线组成.';
                        return false;
                    } else {
                        return true;
                    }
                }
            }, message: ''
        },
        password: {//param的值为[]中值        //用户账号验证(只能包括 _ 数字 字母)
            validator: function (value, param) {
                if (value.length < param[0] || value.length > param[1]) {
                    $.fn.validatebox.defaults.rules.account.message = '密码长度必须在' + param[0] + '至' + param[1] + '范围';
                    return false;
                } else {
                    if (!/^[\w]+$/.test(value)) {
                        $.fn.validatebox.defaults.rules.account.message = '密码只能数字、字母、下划线组成.';
                        return false;
                    } else {
                        return true;
                    }
                }
            }, message: ''
        },
        equalTo: { validator: function (value, param) { return $(param[0]).val() == value; }, message: '字段不匹配' }
    });
})(jQuery);