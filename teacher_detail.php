<? session_start(); 
include('cjcuweb_lib.php');
if(!isset($_SESSION['username']) || $_SESSION['level'] != $level_teacher) {
 	echo "<br>No permission";
 	exit; 
}

?>

<!doctype html>
<html lang="en">

<body>
<script>
//後端傳來個人資料
<? include_once("js_staff_detail.php"); echo_teacher_detail_array($_SESSION['username']); ?>


	
		var detail_column = "",detail_input = "",idx = 0;

		for(var key in user_detail_array){
			
			//資料的敘述
			detail_column+=column_name[idx]+"<br>";
			
			//資料的內容
            detail_input+=user_detail_array[key]+"<br>";
            
			idx++;
		}	


		//把資料倒進相對的位置
		$('#detail_column').html(detail_column);
		$('#detail_input').html(detail_input);


</script>
<!-- 呈現欄位名稱 -->
<div style="float:left; padding-right:50px;" id="detail_column"></div>
<!-- 個人資料 -->
<form method="post" action="updata.php" id="detail_input"></form>

</body>
</html>