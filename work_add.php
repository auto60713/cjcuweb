<?
session_start();
include_once("cjcuweb_lib.php");

// 防止駭客繞過登入
if(isset ($_SESSION['username']) && $_SESSION['level'] == $level_company|$level_teacher){

	// 取得公司電話與地址
	include_once("sqlsrv_connect.php");

	if($_SESSION['level']==4) {$sql = "select address,phone from company where id=?"; $who = '公司';}
	else if($_SESSION['level']==5) {$sql = "select address,phone from department where no=?"; $who = '系所';}
	$params = array($_SESSION['username']);
	$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
	$result = sqlsrv_query($conn,$sql,$params,$options);
	$row = sqlsrv_fetch_array($result,SQLSRV_FETCH_NUMERIC);
	$company_address = $row[0];
	$company_phone = $row[1];



	// 編輯模式,檢查該工作是否為其公司,否則顯示錯誤
	if($_GET['mode']=='edit'){
		
		if(!isCompanyWork($conn,$_SESSION['username'],$_GET['workid'])){
			echo '你沒有權限訪問改頁面!!';
			exit();
		}

	}


}
else{
//重定向瀏覽器 且 後續代碼不會被執行 
header("Location: login.php"); 
exit;
}


// 是否為該公司的工作
function isCompanyWork($conn,$companyid,$workid){

	$sql = "select company_id from work where id=?";
	$params = array($workid);
	$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
	$result = sqlsrv_query($conn,$sql,$params,$options);
	$row = sqlsrv_fetch_array($result,SQLSRV_FETCH_NUMERIC);
	if($row[0]==$companyid) return true;
	else return false;
}

?>

<!DOCTYPE html>
<html>
<head>
<script src="lib/jquery.validate.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">

.match_dep,.instead{
	display: none;
}
.instead td{
	color: #DF7000;
}

label.error{

	color: #D50000;
	font-weight: bold;
	margin-left: 10px;
}
form{
	padding-left: 20px;
}
.td1{
	font-size: 17px;
	font-weight: bolder;
    width: 110px;
    overflow: hidden;

    padding-top: 5px;
    padding-bottom: 5px;
}


</style>
</head>
<body>

<button id="btn-copy-work" class="btn-copy-work"><i class="fa fa-files-o"></i> 從現有工作複製</button>
<button id="btn-instead-work" class="btn-copy-work hidden"><i class="fa fa-files-o"></i> 廠商代PO</button>

<form name="work" id="work_edit_form" method="post" action="work_add_finish.php">
<table>
<tr class="instead">
	<td class='td1'>請輸入廠商帳號</td>
	<td><input type="text" name="instead_com"/></td>
</tr>

<tr>
	<td class='td1'>工作名稱：</td>
	<td><input type="text" name="name" id="name"/></td>
</tr>

<tr>
	<td class='td1'>工作類型：</td>
	<td><select name="work_type" id="work_type"><option>請選擇</option></select> 
		<select name="work_type_list1" id="work_type_list1"><option>請選擇</option></select>
		<select name="work_type_list2" id="work_type_list2"><option>請選擇</option></select>
	</td>
</tr>

<tr>
	<td class='td1'>開始日期：</td>
	<td><select name="year1" id="year1"></select>年
		<select name="month1" id="month1"></select>月
		<select name="date1" id="date1"></select>日
		<select name="hour1" id="hour1"></select>時 
		<select name="minute1" id="minute1"></select>分
	</td>
</tr>

<tr>
	<td class='td1'>截止日期：</td>
	<td><select name="year2" id="year2"></select>年
		<select name="month2" id="month2"></select>月
		<select name="date2" id="date2"></select>日
		<select name="hour2" id="hour2"></select>時
		<select name="minute2" id="minute2"></select>分
	</td>
</tr>

<tr>
	<td class='td1'>工作性質：</td>
	<td><select name="work_prop" id="work_prop"></select>
        <div class="match_dep">請選擇系所<select name="match_dep" id="dep_list"></select></div>
	</td>
</tr>

<tr>
	<td class='td1'>工作類別：</td>
	<td><input type="radio" name="isoutside" value="0" checked="true">校外工作
	    <input type="radio" name="isoutside" value="1">校內工作
	</td>
</tr>

<tr>
	<td class='td1'>工作地點：</td>
	<td><select name="zone" id="zone"></select> 
		<select name="zone_name" id="zone_name"></select>
	</td>
</tr>

<tr>
	<td class='td1'>招募人數：</td>
	<td><input type="text" name="recruitment_no" id="recruitment_no" value="1" /></td>
</tr>

<tr>
	<td class='td1'>聯絡地址：</td>
	<td><input type="text" name="address" id="address"/> 
		<label><input type="checkbox" id="address_same" >同<? echo $who ?>地址</label> 
		<? echo '<input type="hidden" name="hidden_address" id="hidden_address" value="'.$company_address.'"/>';?>
	</td>
</tr>

<tr>
	<td class='td1'>連絡電話：</td>
	<td><input type="text" name="phone" id="phone"/> 
		<label><input type="checkbox" id="phone_same" >同<? echo $who ?>電話</label>
		<? echo '<input type="hidden" name="hidden_phone" id="hidden_phone" value="'.$company_phone.'"/>';?>
	</td>
</tr>
  
<tr>
	<td class='td1'>薪資待遇：</td>
	<td><input type="pay" name="pay" id='pay'/>(可填 時薪,月薪 或 面議)</td>
</tr>

<tr>
	<td class='td1'>工作內容：</td>
	<td><textarea name="detail" cols="45" rows="5" id='detail'></textarea></td> 
</tr>

</table>

<?  //紀錄該工作的ID
	if($_GET['mode']=='edit') echo "<input type='hidden' name='work-id' value=".$_GET['workid'].">";
?>

<input type="submit" name="button" value="確定" />
</form>



<script>

	
	<? 
	// php load some help data for js array
	include_once("js_search_work_data.php"); echo_work_sub_data();
	include_once('js_work_list.php'); echo_work_manage_list_array($_SESSION['username']);
	// if it's edit mode and load init data to js array
	if($_GET['mode']=='edit'){
	include_once('js_work_detail.php');
	echo_work_detail_edit_array($conn,$_GET['workid']);
	}
	//應該要做一個回傳身分的ajax
	if( $_SESSION['level'] != 4 ) echo '$( "#btn-instead-work" ).removeClass( "hidden" );';

	?> 
	
	$(function(){

		// 生成年 
		for(var i=0;i<year_array.length;i++)
		$("#year1,#year2").append($("<option></option>").attr("value", year_array[i]).text(year_array[i]));
		//生成 月
		for(var i=1;i<=12;i++)
		$("#month1,#month2").append($("<option></option>").attr("value", i).text(i));
		// 生成 天
		for(var i=1;i<=31;i++)
		$("#date1,#date2").append($("<option></option>").attr("value", i).text(i));
		// 生成 時
		for(var i=0;i<=23;i++)
		$("#hour1,#hour2").append($("<option></option>").attr("value", i).text(i));
		// 生成 分
		for(var i=0;i<=59;i++)
		$("#minute1,#minute2").append($("<option></option>").attr("value", i).text(i));
		
		// 生成工作位置基本資料
		$("#zone").append($("<option></option>").attr("value", 0).text("國內"));
		$("#zone").append($("<option></option>").attr("value", 1).text("國外"));

		// 生成工作位置細目
		change_zone_list();
		
		// 生成工作類型
		for(var i=0;i<work_type.length;i++)
		$("#work_type").append($("<option></option>").attr("value", work_type_id[i]).text(work_type[i]));
		
		// 生成 工作性質
		for(var i=0;i<work_prop.length;i++)
		$("#work_prop").append($("<option></option>").attr("value", work_prop_id[i]).text(work_prop[i]));

		// 改變月份重新生成天數
		$("#month1").change(function() {
			//清空日期
			$("#date1 option").remove();
			var m = $(this).val;
			var d = (m==1 || m==3 || m==5 || m==7  || m==8  || m==10 || m==12)?31:30;
			for(var i=1;i<=d;i++) $("#date1").append($("<option>").attr("value", i).text(i));
		});
		$("#month2").change(function() {
			//清空日期
			$("#date2 option").remove();
			var m = $(this).val;
			var d = (m==1 || m==3 || m==5 || m==7  || m==8  || m==10 || m==12)?31:30;
			for(var i=1;i<=d;i++) $("#date2").append($("<option>").attr("value", i).text(i));
		});




		// 工作類型第一層 改變時，用ajax列出 第二層 工作類型細目
		$('#work_type').change(function() {
			var id=$(this).val();
			$("#work_type_list1 option").remove();
			// 執行AJAX取得細目資料
			$.ajax({
			type:"POST",
			async:false, 
			url:"ajax_work_type_list.php",
			data:"id="+id+"&list=1",
			success:function(msg){ $('#work_type_list1').html(msg);	},
			error: function(){alert("網路連線出現錯誤!");}
			});
		});

		// 工作類型第二層 改變時，用ajax列出 第三層 工作類型細目
		$('#work_type_list1').change(function() {
			var id=$(this).val();
			// 清空工作類別細目
			$("#work_type_list2 option").remove();
			// 執行AJAX取得細目資料
			$.ajax({
			type:"POST",
			async:false, 
			url:"ajax_work_type_list.php",
			data:"id="+id+"&list=2",
			success:function(msg){ $('#work_type_list2').html(msg);	},
			error: function(){alert("網路連線出現錯誤!");}
			});
		});


        //廠商代PO
        $( "#btn-instead-work" ).click(function() {
            $( ".instead" ).fadeIn();
        });


		
		// 如果工作是實習 列出所有系所
		$('#work_prop').change(function() {
			var id=$(this).val();
			$("#dep_list option").remove();
			if(id==3) {
            $( ".match_dep" ).fadeIn();
			$.ajax({
			    type:"POST",
			    async:false, 
			    url:"ajax_dep_list.php",
			    data:"",
			    success:function(msg){ $('#dep_list').html(msg);	},
			    error: function(){alert("網路連線出現錯誤!");}
			});

			}
			else{$( ".match_dep" ).fadeOut();}
		});



		// 工作地點改變時，用AJAX列出地點細目
		$('#zone').change(function() {
			// 清空地點細目
			change_zone_list();
		});
		// 勾選了"同公司地址",自動輸入
		$("#address_same").click( function(){
	   		if( $(this).is(':checked') ) $('#address').val($('#hidden_address').val());
	   		else $('#address').val('');
		});
		// 勾選了"同公司電話",自動輸入
		$("#phone_same").click( function(){
			if( $(this).is(':checked') ) $('#phone').val($('#hidden_phone').val());
	   		else $('#phone').val('');
		});


		function change_zone_list(){
			var zone = $('#zone').val();
			$("#zone_name option").remove();
			// 執行AJAX取得細目資料
			$.ajax({
			type:"POST",
			async:false, 
			url:"ajax_zone_list.php",
			data:"zone="+zone,
			success:function(msg){ $('#zone_name').html(msg);},
			error: function(){alert("網路連線出現錯誤!");}
			});
		}



		// 新增工作，從現有工作中複製資料
		$("#btn-copy-work").click(function(event) {
			
				$('#lightbox-copy-work').fadeIn(100, function() {

				var box = $('.listbox-copy-work');

				if(work_list_array.length>0) box.html('');
				else box.html('您目前沒有新增任何工作...');


				for(var i=0;i<work_list_array.length;i++){

					var icon = $('<i>').addClass('fa fa-book'),
						sub = $('<div>').addClass('list-copy-work').attr('wid', work_list_array[i].wid);

						sub.append(icon).append(' ' + work_list_array[i].wname );

						sub.on('click',function(event) {

							console.log('click');
							var wid = $(this).attr('wid');

							$.ajax({
								url: 'js_work_detail.php',
								type: 'post',
								dataType: 'json',
								data: {workid: wid},
							})
							.done(function (data) {
								console.log(data);
								setInit(data,true);
								$('#lightbox-copy-work').fadeOut(100);

							});


						});

						box.append(sub);
				}

			});
		});

		$('#lightbox-copy-exit').click(function(event) {
			$('#lightbox-copy-work').fadeOut(100);
		});

		/* .............................................................................
		   編輯模模式...................................................................
		// .............................................................................*/

		<?  if($_GET['mode']=='edit') 
		echo 'setInit(work_detail_array,false); $("#btn-copy-work").remove();' ?>

		function setInit(work_detail_array,is_copy_mode){

			if(!is_copy_mode) $('#work_edit_form').attr('action', 'work_update.php');


			$('#name').val(work_detail_array['name']);
			$('#work_type').val(work_detail_array['type1']);
			
			var id=$('#work_type').val();
			// 清空工作類別細目
			$("#work_type_list1 option").remove();
			// 執行AJAX取得細目資料
			$.ajax({
			type:"POST",
			async:true, 
			url:"ajax_work_type_list.php",
			data:"id="+id+"&list=1",
			success:function(msg){ $('#work_type_list1').html(msg);	
			$('#work_type_list1').val( parseInt(work_detail_array['type2']));
			},
			error: function(){alert("網路連線出現錯誤!");}
			});

			
			var id= parseInt(work_detail_array['type2']);
			// 清空工作類別細目
			$("#work_type_list1 option").remove();
			// 執行AJAX取得細目資料
			$.ajax({
			type:"POST",
			async:true, 
			url:"ajax_work_type_list.php",
			data:"id="+id+"&list=2",
			success:function(msg){ $('#work_type_list2').html(msg);	
			$('#work_type_list2').val(work_detail_array['type3']);
			},
			error: function(){alert("網路連線出現錯誤!");}
			});
			

			var start_date = work_detail_array['start_date'].split(" ");
			var date = start_date[0].split("-");
			var time = start_date[1].split(":");
			var y = parseInt(date[0]);
			var m = parseInt(date[1]);
			var d = parseInt(date[2]);
			var hh = parseInt(time[0]);
			var mm = parseInt(time[1]);
			
			$('#year1').val(y);
			$('#month1').val(m);
			$('#date1').val(d);
			$('#hour1').val(hh);
			$('#minute1').val(mm);


			var start_date = work_detail_array['end_date'].split(" ");
			var date = start_date[0].split("-");
			var time = start_date[1].split(":");
			var y = parseInt(date[0]);
			var m = parseInt(date[1]);
			var d = parseInt(date[2]);
			var hh = parseInt(time[0]);
			var mm = parseInt(time[1]);

			$('#year2').val(y);
			$('#month2').val(m);
			$('#date2').val(d);
			$('#hour2').val(hh);
			$('#minute2').val(mm);


			$('#work_prop').val(work_detail_array['prop']); 
			$('input[type="radio"][value="'+work_detail_array['is_outside']+'"]').attr('checked', 'true');
			$('#zone').val(work_detail_array['zone']);
			$('#zone_name').val(work_detail_array['zone_id']);
			$('#recruitment_no').val(parseInt(work_detail_array['rno']));
			$('#address').val(work_detail_array['address']);
			$('#phone').val(work_detail_array['phone']);
			$('#pay').val(work_detail_array['pay']);
			$('#detail').val(work_detail_array['detail']);
		}


	});


//欄位限制
$(document).ready(function() { 

        $("#work_edit_form").validate({ 
            rules: { 
                name:            { required:true,maxlength:20 },
                work_type_list2: { required:true },
                zone_name:       { required:true },
                recruitment_no:  { required:true,maxlength:3,digits:true },
                address:         { required:true,maxlength:40 },
                phone:           { required:true,maxlength:14 },
                detail:          { maxlength:80 }
            }
        }); 

        jQuery.extend(jQuery.validator.messages, {
            required: "此為必填項目",
            digits: "請輸入正整數",
            maxlength: jQuery.validator.format("不得超過{0}個字"),
            rangelength: jQuery.validator.format("不符合格式"),
        });

      
});


</script>



<!-- 從現有的工作中複製 -->
<div class="staff-apply-form" id="lightbox-copy-work" style="display:none"> 
	<div class="staff-apply-box"> 
	
		<h2 class="listbox-copy-work-title"><i class="fa fa-files-o"></i> 複製工作 <i class="fa fa-times login-exit" id="lightbox-copy-exit"></i></h2>  
		<p class="listbox-copy-work-hint">請選擇欲複製的工作，系統會將該工作資料為您填入</p>

		<div class="listbox-copy-work"></div>
	
	</div> 
</div>

</body>


</html>