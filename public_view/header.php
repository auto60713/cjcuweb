<?php session_start(); 
function echo_data(){
	include_once("../cjcuweb_lib.php");
	if(isset ($_SESSION['username'])){
		//echo $_SESSION['username'] ;
		if( $_SESSION['level'] == $level_company) {/*
			echo '<span><a href="../../../cjcuweb/company/'.$company_id.'">公司資訊</a></span>';
			echo '<span><a href="../../../cjcuweb/company_work_list.php">管理工作</a></span>';
			echo '<span><a href="../../../cjcuweb/add_work.php">新增工作</a></span>';
			echo '<span><a href="../../../cjcuweb/company_manage_apply.php">管理應徵</a></span>';*/
			echo '<span><a href="../../../cjcuweb/company/'.$_SESSION['username'].'">'.$_SESSION['username'].'</a></span>';
			echo '<span><a href="../../../cjcuweb/company_manage.php">管理</a></span>';
		}
		else if( $_SESSION['level'] == $level_student){
			/*echo '<span><a href="../../../cjcuweb/student_work.php">我的應徵</a></span>';*/
			echo '<span><a href="../../../cjcuweb/student/'.$_SESSION['username'].'">'.$_SESSION['username'].'</a></span>';
			echo '<span><a href="../../../cjcuweb/student_manage.php">管理</a></span>';
			
		}
		else if( $_SESSION['level'] == $level_staff){
			echo '<span><a href="../../../cjcuweb/staff/'.$_SESSION['username'].'">'.$_SESSION['username'].'</a></span>';
			echo '<span><a href="../../../cjcuweb/staff_manage.php">管理</a></span>';
		}
		else if( $_SESSION['level'] == $level_teacher){
			echo '<span><a href="../../../cjcuweb/teacher/'.$_SESSION['username'].'">'.$_SESSION['username'].'</a></span>';
			echo '<span><a href="../../../cjcuweb/teacher_manage.php">管理</a></span>';
		}

		echo '<span><a href="../../../cjcuweb/notice.php">通知</a></span>';
		echo '<span><a href="../../../cjcuweb/logout.php">登出</a></span>';

	}	
	else echo '<span><a href="../../../cjcuweb/login.php">登入</a></span>';
}
?>


<html>
<head>
	<link rel="stylesheet" type="text/css" href="../css/main.css">
</head>
<body>
<div id="header" class="div-align">
<!--<div id="header">-->
	<div class="sub"><a href="../../../cjcuweb/home.php"><h1>長榮大學 媒合系統</h1></a></div>
	<div class="sub2"> 
	<? echo_data()	 ?>  
	</div>
</div>
</body>
</html>