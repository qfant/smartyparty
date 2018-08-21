$.ajaxSetup({
	error: function (XMLHttpRequest, textStatus, errorThrown){
	},
	complete:function(xhr,textStatus){
		var sessionstatus=xhr.getResponseHeader("sessionstatus"); //通过XMLHttpRequest取得响应头,sessionstatus，
		if(isJSON(xhr.responseJSON)&&(xhr.responseJSON['status']==401 || xhr.responseJSON['status']==403)){//session超时跳转到首页
			// $.messager.alert({
			// 	title:'我的消息',
			// 	msg:xhr.responseJSON['message'],
			// 	icon:'error',
			// 	fn: function(){
			// 		parent.window.location.href="/Admin/Login/index"
			// 	}
			// });
            toastr.options = {
                "closeButton": false,
                "positionClass": "toast-top-center"
            };
            toastr.error(xhr.responseJSON['message']);
           // console.log(xhr.responseJSON);
            //parent.window.location.href="/Admin/Login/index"
			//parent.window.location.href="/Admin/Login/index";
		}
		//console.log(xhr);
	}
});
function isJSON(str) {
    if (typeof str == 'string') {
        try {
            JSON.parse(str);
            return true;
        } catch(e) {
            console.log(e);
            return false;
        }
    }
    console.log('It is not a string!')
}