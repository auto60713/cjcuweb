<? 
session_start();
$lev = $_SESSION['level'];
$user = $_SESSION['username'];



function echo_data($user,$lev){
	//if($lev=='0')$lev = $_SESSION['level2'];

	include_once("../cjcuweb_lib.php");
	if(isset ($user)){
		//echo $user ;
		if( $lev == $level_company) {/*
			echo '<span><a href="../../../cjcuweb/company/'.$company_id.'">公司資訊</a></span>';
			echo '<span><a href="../../../cjcuweb/company_work_list.php">管理工作</a></span>';
			echo '<span><a href="../../../cjcuweb/add_work.php">新增工作</a></span>';
			echo '<span><a href="../../../cjcuweb/company_manage_apply.php">管理應徵</a></span>';*/
			echo '<span><a href="../../../cjcuweb/company/'.$user.'">'.$user.'</a></span>';
			echo '<span><a href="../../../cjcuweb/company_manage.php">管理</a></span>';
			echo '<span id="header-notice"><a href="../../../cjcuweb/company_manage.php#company-notice">通知</a></span>';
		}
		else if( $lev == $level_student){
			/*echo '<span><a href="../../../cjcuweb/student_work.php">我的應徵</a></span>';*/
			echo '<span><a href="../../../cjcuweb/student/'.$user.'">'.$user.'</a></span>';
			echo '<span><a href="../../../cjcuweb/student_manage.php">管理</a></span>';
			echo '<span id="header-notice"><a href="../../../cjcuweb/student_manage.php#student-notice">通知</a></span>';
		}
		else if( $lev == $level_staff){
			echo '<span><a href="../../../cjcuweb/staff/'.$user.'">'.$user.'</a></span>';
			echo '<span><a href="../../../cjcuweb/staff_manage.php">管理</a></span>';
			echo '<span id="header-notice"><a href="../../../cjcuweb/staff_manage.php#staff-notice">通知</a></span>';
		}

		else if( $lev == $level_teacher){
			//echo '<span><a href="../../../cjcuweb/teacher/'.$user.'">'.$user.'</a></span>';
			//echo '<span><a href="../../../cjcuweb/teacher_manage.php">管理</a></span>';
		}
//
		
		echo '<span><a href="../../../cjcuweb/logout.php">登出</a></span>';

	}	
	else echo '<span><a href="#" id="login-btn">登入</a></span>';

	//echo $user." >".$lev." >".$level_student ." >" .$_SESSION['level2'];
}
?>


<html>
<head>
</head>
<body>
<div id="header" class="div-align">
<script>

	$(function(){

		polling();
		function polling(){
			$.ajax({
			url: 'ajax_get_news_num.php',
			type: 'get',
			})
			.done(function(d) {

				console.log('Get News Num:',d);
				$('.header-notice-num').remove();
				

				if(d!='0')
					$('#header-notice a').append($('<span>').addClass('header-notice-num').html(d));
				//else
					//$('#header-notice a').append($('<span>').addClass('header-notice-num').text(data));
			});
			setTimeout(function(){ polling();},5000);
		}

		$('#header-notice').click(function(event) {
			$('.header-notice-num').remove();
		});
		
	});
</script>
<!--<div id="header">-->
	<div class="sub"><a href="../../../cjcuweb/home.php"><h1>長大職涯網</h1></a></div>
	<div class="sub2"> 
	<? echo_data($user,$lev)	 ?>  
	</div>
</div>
</body>
</html>