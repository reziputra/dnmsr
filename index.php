<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DragonNestMobile</title>
	<link href="css/jquery.searchableSelect.css" rel="stylesheet" type="text/css">
	<script src="js/jquery-1.11.1.min.js" type="text/javascript"></script>
	<script src="js/jquery.searchableSelect.js" type="text/javascript"></script>		
</head>
<?php 
error_reporting(0);
header("Content-type: text/html; charset=utf8");
date_default_timezone_set("PRC");
$dbconfig = array(
	'db_host'			=> '192.46.229.23',	
	'db_username'		=> 'root',		
	'db_password'		=> '123456',			
	'database1'			=> '01_world_s1',	
);
$conn = @mysql_connect($dbconfig['db_host'],$dbconfig['db_username'],$dbconfig['db_password']) or die ("<script>alert('数据连接异常,请联系管理员!!');</script>");
@mysql_select_db($dbconfig['database1'],$conn) or die ("<script>alert('表数据连接异常,请联系管理员!!');</script>");
@mysql_query("set names UTF8");
function http_post($url, $data) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $res = curl_exec($ch);
    curl_close($ch);

    $res = urldecode($res);
    return json_decode($res, true);
}
if($_POST['submit']){
	$uid=trim($_POST['uid']);
	$item=trim($_POST['item']);
	$num=trim($_POST['num']);
	if(empty($uid)){
		echo "<script>alert('请输入玩家角色ID！');history.go(-1)</script>";
		exit;		
	}
	if(empty($item)){
		echo "<script>alert('请选择物品！');history.go(-1)</script>";
		exit;		
	}
	if(empty($num) && $num<1){
		echo "<script>alert('物品数量不正确！');history.go(-1)</script>";
		exit;		
	}
	$sql="select * from role where nickid='".$uid."'";
	$res=mysql_query($sql);
	$row=mysql_fetch_array($res);
	if(empty($row['_id'])){
		echo "<script>alert('发送失败,获取角色信息错误！');history.go(-1)</script>";
		exit;			
	}	
	$url = 'http://127.0.0.1:8001/idip';
	$group_id=1003;
	$role_id=$row['_id'];
	$items='{"ItemList_count":1,"ItemList":[{"ItemId":'.$item.',"ItemNum":'.$num.'}],"IsBind":0,"Time":0,"LanguageId":["zh","cht"],"MailTitle":["\u7cfb\u7edf\u90ae\u4ef6","\u7cfb\u7edf\u90ae\u4ef6"],"MailContent":["\u8bf7\u6ce8\u610f\u67e5\u6536\u0021","\u8bf7\u6ce8\u610f\u67e5\u6536\u0021"]}';
	$mail_body = json_decode($items, true);
	$mail_body['Partition'] = (int)$group_id;
	$mail_body['RoleId'] = (string)$role_id;
	$post_data = array(
	'head' => array('Cmdid' => 4143),
	'body' => $mail_body,
	);
	$response = http_post($url, 'data_packet=' . json_encode($post_data));
	if ($response['head']['Result'] == 0){	
		echo "<script>alert('发送成功,邮件查收！');history.go(-1)</script>";
		exit;		
	}else{
		echo "<script>alert('发送失败,错误信息【{$response['head']['RetErrMsg']}】！');history.go(-1)</script>";
		exit;		
	}
}
?>
<body>
<style>
    *{margin:0;paddign:0;font-family:"微软雅黑";font-style:normal;font-size:14px;}
    .dropdown{position: relative;display:inline-block;width: 198px;padding-left:10px; }
    .dropdown-selected{width: 100%!important;height:30px;line-height:30px;border:1px solid #c6c8cc;-webkit-border-radius: 4px;-moz-border-radius: 4px;border-radius: 4px;color:#333;text-indent: 10px;margin-bottom: 0!important;}
    .dropdown-input{width: 80%!important;height:30px;line-height:30px;border:1px solid #c6c8cc;-webkit-border-radius: 4px;-moz-border-radius: 4px;border-radius: 4px;color:#333;text-indent: 10px;margin-bottom: 0!important;}	
    .dropdown ul{padding:0;width:100%;max-height:200px;overflow-y:auto ;background-color:#fff;margin-top:2px;border:1px solid #c6c8cc;position:absolute;display:none;z-index: 2;}
    .dropdown ul li{list-style: none;text-indent:10px;}
    .dropdown ul li a{display:block;color:#282c33;text-decoration:none;overflow: hidden;text-overflow:ellipsis;white-space: nowrap;}
    .dropdown ul li:hover{background-color:#f2f6fa;}
    .dropdown ul li a:active,.dropdown ul li.active a{background-color: #e4e9f2;}
</style>
<div>
<br>
<form name="form1" method="post" action="">
&nbsp;&nbsp;&nbsp;UID：<div class="dropdown">
		<input type="text" class="dropdown-selected" name="uid" value='' placeholder='请输入角色ID'>
	</div>
	<br><br>
	
	
&nbsp;&nbsp;数量：<div class="dropdown">
		<input type="text" class="dropdown-selected" name="num" value='' placeholder='请输入物品数量'>
	</div>
	<br><br>
&nbsp;&nbsp;物品：<div class="dropdown">
		<select class="dropdown-selected" name='item'>
			<option value="">请选择物品</option>
		<?php 
			$lines=file("item.txt");
				foreach ($lines as $value) {
					$line=explode(";",$value);
					echo '<option value="'.$line[0].'">'.$line[1].'</option>';
				}
		?>			
		</select>
	</div>	
	<div class="dropdown">
		<input type="submit" class="dropdown-input" name="submit" value='发送'>
	</div>	
	<br><br>


</form>	
</div>
<script>
		$(function(){
			$('select').searchableSelect();
		});
</script>
</body>
</html>