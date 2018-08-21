<?php
    if(C('LAYOUT_ON')) {
        echo '{__NOLAYOUT__}';
    }
?>
<!DOCTYPE html>
<html>

<head>
	<title> 页面跳转中</title>
	<script src="__PUBLIC__/statics/js/jquery-1.10.2.min.js"></script>
	<script src="__PUBLIC__/statics/hplus/js/plugins/layer/layer.min.js"></script>
</head>

<body>
<b id="wait" style='display:none'><?php echo($waitSecond); ?></b>
<a id="href" href="<?php echo($jumpUrl); ?>" style='display:none'>跳转</a>
<script type="text/javascript">
	$(function() {
		var mess = "<?php echo($message); ?>";
		var err = "<?php echo($error); ?>";
		var jumpurl = "<?php echo($jumpUrl); ?>";
		var waitSecond = 1;
		<?php if(isset($message)) {?>
		var suc = mess + '页面自动跳转中... 等待时间:' + waitSecond + '秒';
		layer.alert(suc, {
			icon: 1,
			title: mess,
				yes: function(index, layero){
				var href = document.getElementById('href').href;
				location.href = href;
				},
		end: function(index, layero){
			var href = document.getElementById('href').href;
			location.href = href;
		},
		success: function (layer) {
				var wait = document.getElementById('wait'),
						href = document.getElementById('href').href;
				var interval = setInterval(function () {
					var time = --wait.innerHTML;
					if (time <= 0) {
						location.href = href;
						clearInterval(interval);
					}
					;
				}, 100000);
			}
		});
	<?php }else{?>
		var er = err + '页面自动跳转中... 等待时间:' + waitSecond + '秒';
		layer.alert(er, {
			icon: 2,
			yes: function(index, layero){
				var href = document.getElementById('href').href;
				location.href = href;
			},
			end: function(index, layero){
				var href = document.getElementById('href').href;
				location.href = href;
			},
			title: err,
			success: function (layer) {
				var wait = document.getElementById('wait'),
						href = document.getElementById('href').href;
				var interval = setInterval(function () {
					var time = --wait.innerHTML;
					if (time <= 0) {
						location.href = href;
						clearInterval(interval);
					}
					;
				}, 100000);
			}
		});
	<?php }?>
	});
</script>
</body>

</html>

