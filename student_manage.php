<?php session_start();
if(!isset($_SESSION['username']) || $_SESSION['level'] != 3) { header("Location: index.php"); exit; }
else{$stud_id = trim($_SESSION['username']);}
?>
<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<link href="img/ico.ico" rel="SHORTCUT ICON">
	<title>長大職涯網-個人頁面</title>
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<link rel="stylesheet" type="text/css" href="css/manage.css">
	<link rel="stylesheet" type="text/css" href="css/work_detail_edit.css">
	<link rel="stylesheet" type="text/css" href="css/profile.css">
	<link rel="stylesheet" type="text/css" href="font-awesome/css/font-awesome.min.css">
	<script type="text/javascript" src="js/jquery-min.js"></script>
    <script type="text/javascript" src="js/jquery-migrate-min.js"></script>
	<script type="text/javascript" src="js/jquery.hashchange.min.js"></script>
	<script type="text/javascript" src="js/upload_img.js"></script>
	<script>
	$(function(){

		$('#view-header').load('public_view/header.php');
		$("#menu").load('public_view/menu.html');
	    $("#footer").load('public_view/footer.html');

		$(window).hashchange( function(){

		  	var loc = location.hash.replace( /^#/, '' );
		  	switch(loc) {
			case 'student-info':case'':doajax(0);break;
			case 'student-applywork':doajax(1);break;
			case 'student-notice':doajax(2);break;
			case 'explanation':doajax(12);break;
			}
			//特殊處理
			if(loc.indexOf("times") >= 0 ) doajax(5);
            else if(loc.indexOf("timelist") >= 0 ) doajax(6);

		});

		$(window).hashchange();


		function doajax(idx){

			    $('#right-box-title').html('載入中...請稍後');

				switch(idx) {
				// student info
				case 0:
				tpe = 'get';
				para = { userid: <?php echo "\"".$stud_id."\"" ?> };
				url = "student_detail_edit.php";
				break;
				// 學生應徵的工作
				case 1:
				tpe = 'get';
				para = {};
				url = "student_work.php";
				break;
				// notice
				case 2:
				tpe = 'post';
				para = {};
				url = "notice.php";	
				break;

				// 工讀單列表
				case 5:
				tpe = 'get';
				var wid = location.hash.replace( /^#times/, '' );
				para = {studid:<?php echo "\"".$stud_id."\"" ?>,workid:wid};
				url = "student_work_time_list.php";	
				break;

				// 工讀單
				case 6:
				tpe = 'get';
				var lid = location.hash.replace( /^#timelist/, '' );
				para = {studid:<?php echo "\"".$stud_id."\"" ?>,listid:lid};
				url = "student_work_time.php";	
				break;

				case 12:
				tpe = 'post';
				para = {mode:'stu'};
				url = "explanation.php";	
				break;
			}

			$.ajax({
			  type: tpe,
			  url: url,
			  data: para,
			  success: function (data) { $('#contailer-box').html(data) ;  }
			});
		    
            if(idx==12) idx=3;
            $('#right-box-title').text($('.list:eq('+idx+')').text());
            if(idx==5 || idx==6){
            	$('#right-box-title').text('工讀單');
            	idx=1;
            }
            

            $('.list').removeClass('list-active');
            $('.list:eq('+idx+')').addClass('list-active');
				
        }

		
		<?php	//load data
		    include_once("js_detail.php"); echo_student_profile($stud_id); 
		?>

        $("#profile-img").attr("src",'http://esrdoc.cjcu.edu.tw/esr_photo/'+student_profile_array['sd_syear'].trim()+'/'+student_profile_array['sd_no'].trim()+'.jpg');
        $(".profile-box h2").text(student_profile_array['sd_no']);

	});
	</script>
</head>


<body>
<div id="view-header"></div>
<!-- 菜單 -->
<div id="menu"></div>

<div class="b-space div-align overfix">

	<div id="" class="left-box" >

		<div class="profile-box">
			<img src="" id="profile-img" class="profile-img-stu"><br>
		</div>

		<a href="#student-info"><div class="list">個人資訊</div></a><hr>
		<a href="#student-applywork"><div class="list">我的應徵</div></a><hr>
		<a href="#student-notice"><div class="list">系統紀錄</div></a><hr>
		<a href="#explanation"><div class="list">操作說明</div></a><hr>
	</div>


	<div id="" class="right-box">
		<h2 id="right-box-title"></h2>
		<br>
		<div id="contailer-box"></div>
	</div>

	
</div>



<!-- 頁尾訊息 -->
<div id="footer"></div>


</body>
</html>