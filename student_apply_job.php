<html>
<head>
	<meta charset="UTF-8">
	<title>唉唷</title>
</head>

<body>

<?php
session_start();
header('Content-Type: text/html; charset=utf8');

//本程式為學生欲申請應徵工作 由 AJAX 執行的程式

include_once("cjcuweb_lib.php");
// 確認身分為學生
if(isset($_SESSION['username']) && $_SESSION['level']==$level_student){

include_once("sqlsrv_connect.php");
include_once("cjcuweb_lib.php");

$user_id = $_SESSION['username'];
$work_id = $_POST['workid'];

$sql = "insert into line_up(user_id,work_id,[check])values(?,?,?)";
$params = array($user_id,$work_id,0);
$options =  array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$result = sqlsrv_query($conn,$sql,$params,$options);

if($result)success_return();
else error_return($work_id);
}

// 無權訪問本頁面
else{header("Location: index.php"); exit;}

function error_return($work_id){
	

	echo '應徵失敗..';

}
function success_return(){

	echo '<meta charset="utf-8" http-equiv="refresh" content="1; url=student_manage.php#student-applywork" />';
	echo '應徵成功...跳轉中...';

}
?>

</body>
</html>