<?php session_start();
/*
include_once('cjcuweb_lib.php');
include_once('sqlsrv_connect.php');
//檢查該工作是否屬於該公司
$checkVars = array($level_staff,$level_department);
if(!isCompanyWork($conn,$_SESSION['username'],$_POST['workid'])&& !in_array($_SESSION['level'], $checkVars)){
	
    echo 'No permission!';
    exit();
}

function isCompanyWork($conn,$companyid,$workid){
//工作負責人轉換
if (preg_match("/-/i", $companyid)) $companyid = strstr($companyid,'-',true);

	$sql = "select company_id from work where id=?";
	$params = array($workid);
	$result = sqlsrv_query($conn,$sql,$params);
	$row = sqlsrv_fetch_array($result,SQLSRV_FETCH_NUMERIC);
	if($row[0]==$companyid) return true;
	else return false;
}
*/
?>

<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
	<style type="text/css">
        .company-audit-censored{
            width: 70px;
        }
	</style>
</head>
<body>
<style type="text/css">
.work-divbtn-d{
    font-size: 16px;
}
</style>
<div class="workedit-tabbox" style="display:none;">
	<div id="page_view" class="sub-tab"><i class="fa fa-desktop tab-img"></i> 預覽</div>
	<div id="page-edit" class="sub-tab tab-active" tabtoggle='workedit1'><i class="fa fa-pencil tab-img"></i> 編輯</div>
	<div id="page-apply" class="sub-tab" tabtoggle='workedit1'><i class="fa fa-user tab-img"></i> 應徵</div>
	<div id="page-start" class="sub-tab" tabtoggle='workedit1'><i class="fa fa-bullhorn tab-img"></i> 發佈</div>
	<div id="page-audit" class="sub-tab" tabtoggle='workedit1'><i class="fa fa-check tab-img"></i> 狀態</div>
	<div id="page-set" class="sub-tab" tabtoggle='workedit1'><i class="fa fa-cog tab-img"></i> 刪除</div>
</div>

<div class="workedit-content" id='workedit-content' style="display:none;">
	<!-- 該工作的資料編輯，AJAX別的畫面 -->
	<div id='workedit-content-edit' class="workedit-content-hide" tabtoggle='workedit2'></div>
	<!-- 該工作的應徵學生列表，AJAX別的畫面 -->
	<div id='workedit-content-apply' class="workedit-content-hide" tabtoggle='workedit2'></div>
	<!-- 該工作應徵結束 -->
	<div id='workedit-content-start' class="workedit-content-hide" tabtoggle='workedit2'>
	</div>
	<!-- 該工作的審核狀態 -->
	<div id='workedit-content-audit' class="workedit-content-hide" tabtoggle='workedit2'>
		<h1 class="company-audit-status">工作狀況：</h1>
		<div class="company-audit-history" id="company-audit-history"><p>審核歷史紀錄：</p></div>
	</div>
	<!-- 工作刪除 -->
	<div id='workedit-content-set' class="workedit-content-hide" tabtoggle='workedit2'>	
	    <button type="button" id="divbtn-delete" class="work-divbtn-d">刪除工作</button> 
	</div>
</div>

</body>

<script>

    <?php include_once("js_audit_detail.php"); echo_audit_detail_array($_POST['workid'],1); ?>

	$(function(){

		// 移除不該檢視的頁面
		$.ajax({
		  type: 'POST',
		  url: 'ajax_work_edit.php',
		  data:{mode:0,workid:<?php  echo (int)$_POST['workid']; ?>},
		  success: function (data) { 
            var remove_array = JSON.parse(data);
            for(var i=0;i<remove_array.length;i++) $( remove_array[i] ).remove();
		  }
		});

		// 該工作的詳細資料修改
		$.ajax({
		  type: 'get',
		  url: 'work_add.php',
		  async:false,
		  data: {mode:'edit',workid:  <?php  echo (int)$_POST['workid']; ?> },
		  success: function (data) { $('#workedit-content-edit').html(data) ;  }
		});

		// 該工作的應徵者列表
		$.ajax({
		  type: 'get',
		  url: 'work_detail_apply.php',
		  data: {workid:  <?php  echo (int)$_POST['workid']; ?> },
		  success: function (data) { $('#workedit-content-apply').html(data) ;  }
		});

        // 該工作的能執行的動作
		$.ajax({
		  type: 'POST',
		  url: 'ajax_work_edit.php',
		  data:{mode:3,workid:<?php  echo (int)$_POST['workid']; ?>},
		  success: function (data) { 

		  	if(data == 0) $('#workedit-content-start').append('沒有可執行的指令');
		  	else{
                var work_divbtn_array = JSON.parse(data);
                //幾個array:幾個按鈕 , divbtn_id:按鈕的ID , divbtn_text:按鈕的內容
                for(var i=0;i<work_divbtn_array.length;i++){
                    var work_divbtn = $('<button>').attr('id',work_divbtn_array[i].divbtn_id).addClass('work-divbtn').text(work_divbtn_array[i].divbtn_text),
		  	            divbtn_explain = $('<span>').addClass('divbtn-explain').text(work_divbtn_array[i].divbtn_explain);
		  	        $('#workedit-content-start').append(work_divbtn,divbtn_explain,$('<br>'));  
		        }
		    }
		  }
		});

		switch(work_detail_array.check) {
			case 0:
				icontxt ='fa fa-minus-square-o';
				statustxt = ' 等待校方審核';
				color = '#555';
				break;
			case 1:
				icontxt ='fa fa-check';
				statustxt = ' 招募中';
				color = '#339933';
				break;
			case 2 : case 3:
				icontxt ='fa fa-times';
				statustxt = ' 校方審核不通過';
				color = '#CC3333';
				break;
			case 4:case 5:
				icontxt ='fa fa-minus-square-o';
				statustxt = ' 停止招募';
				color = '#6F9B6F';
				break;
		}

		var audit_history_container = $('#company-audit-history');
		if(audit_array.length==0) audit_history_container.html( $('<p>').text("無審核歷史紀錄") );
		for(var i=0;i<audit_array.length;i++){

			var icontxt2 = (audit_array[i].censored==1)? 'fa fa-check': 'fa fa-times',
				statustxt2 = (audit_array[i].censored==1)? ' 通過': ' 不通過',
				time = $('<span>').addClass('company-audit-time').text(audit_array[i].time.split(' ')[0]),
				icon = $('<i>').addClass(icontxt2),
				censored = $('<span>').addClass('company-audit-censored').append(icon,statustxt2),
				msg = $('<span>').addClass('company-audit-msg').text(audit_array[i].msg),
				vialink = $('<a>').attr('target','_blank').attr('href', 'department-'+audit_array[i].staff_no).text(audit_array[i].staff_no),
				via = $('<span>').addClass('company-audit-via').append('審核者：',vialink),
				all = $('<div>').addClass('company-audit-list').append(time,censored,msg,via);
				audit_history_container.append(all);
		}

		var icon = $('<i>').addClass(icontxt),
			again_txt = $('<span>').addClass('company-audit-again-txt').text('已要求再次審核！'),
			again_btn = $('<button >').addClass('company-audit-again').text("要求再審").on('click', function(event) {
				
				$.ajax({
					url: 'ajax_audit_again.php',
					type: 'post',
					data: {objid:<?php echo (int)$_POST['workid']; ?>},
				})
				.done(function(data) {
					if(data!='0') {
					$('.company-audit-status').append(again_txt);
					again_btn.remove();
					}
				});

			});

		$('.company-audit-status').append(icon,statustxt).css('color', color);
		if(work_detail_array.check==2) $('.company-audit-status').append(again_btn);
		if(work_detail_array.check==3) $('.company-audit-status').append(again_txt);

		// TAB Control
		var tabgroup = $('div[tabtoggle="workedit1"]');
		tabgroup.click(function(event) {
			tabgroup.removeClass('tab-active');
			$(this).addClass('tab-active');
			var currentId = $(this).attr('id');
			$('div[tabtoggle="workedit2"]').addClass('workedit-content-hide');
			switch(currentId){
				case 'page-edit':
				    $('#workedit-content-edit').removeClass('workedit-content-hide');
				break;
				case 'page-apply':
				    $('#workedit-content-apply').removeClass('workedit-content-hide');
				break;
				case 'page-start':
                    $('#workedit-content-start').removeClass('workedit-content-hide');
				break;
				case 'page-audit':
                    $('#workedit-content-audit').removeClass('workedit-content-hide');
				break;
				case 'page-set':
				    $('#workedit-content-set').removeClass('workedit-content-hide');
				break;
			}
		});
		tabgroup[<?php echo (int)$_POST['page']; ?>].click();

        $( "#page_view" ).click(function() {
            window.open("work-"+<?php echo (int)$_POST['workid']; ?>);
        });

        $('.workedit-tabbox,.workedit-content').fadeIn(300);


        //執行某個動作
		$(document).off("click",'button.work-divbtn').on('click','button.work-divbtn', function() {

	 	var btn_text = $(this).text(),
	 	    btn_id = $(this).attr('id');

	 	    if (confirm ("確定要"+btn_text+"?")){

	 	        switch(btn_id){
				    case 'divbtn-stop':
				        var check = 4;
				    break;
				    case 'divbtn-restart':
				        var check = 1;
				    break;
				    case 'divbtn-end':
				        var check = 5;
				    break;
			    }
		    	$.ajax({
			     	type:"POST",
			     	url: "ajax_work_edit.php",
			     	data:{mode:1,workid:<?php echo (int)$_POST['workid']; ?>,check:check},
                    success: function (data) { 
                    	location.reload();
			        }
			    });
		    }    
		});


        /*停止應徵
		$(document).off("click",'button#divbtn-start').on('click','button#divbtn-start', function() {

	 	var btn_text = $('button#divbtn-stop').text();

		    	$.ajax({
			     	type:"POST",
			     	url: "ajax_work_edit.php",
			     	data:{mode:1,workid:<?php echo (int)$_POST['workid']; ?>,check:4},
                    success: function (data) { 
          
                    	alert(data); location.reload();
			        }
			    });
		});
		//完成工作
		$(document).off("click",'button#divbtn-end').on('click','button#divbtn-end', function() {

		    btn_text = $('button#divbtn-end').text();
		    if (confirm ("確定要"+btn_text+"?")){

		    	$.ajax({
			     	type:"POST",
			     	url: "ajax_work_edit.php",
			     	data:{mode:1,workid:<?php echo (int)$_POST['workid']; ?>,check:5},
                    success: function (data) { 
          
                    	$('#workedit-content-start').text(data);
			        }
			    });
		    }
		});
*/
        //刪除工作
        var work_delete = $('button#divbtn-delete');
		work_delete.click(function(event) {

            //查詢該工作名字
			$.ajax({
			    type:"POST",
			    url: "ajax_echo_name.php",
			    data:{mode:'work',workid:<?php echo (int)$_POST['workid']; ?>},
                success: function (data) { //上括號
                if(data != 0){
                var work_name=data;
                  
		            if (confirm ("確定要刪除此工作 「"+work_name+"」 ?")){

		    	    $.ajax({
			         	type:"POST",
			         	url: "delete.php",
			         	data:{mode:0,workid:<?php echo (int)$_POST['workid']; ?>,workname:work_name},
                        success: function (data2) { 
                        	if(data2 != 0){
                    	        window.location.href = data2+'_manage.php#'+data2+'-work';
                        	}
			      	    else alert('資料驗證不正確 無法刪除');
			            }
			        });
		            }

		    	}
			    }//下括號
			});
		});


	});


</script>


</html>