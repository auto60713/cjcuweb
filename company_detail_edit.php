<? session_start(); 
if(isset($_GET['companyid'])) $_SESSION['userid']=$_GET['companyid']; else{header("Location: home.php"); exit;}
?>

<!doctype html>
<html>
<head>
	<script><? include_once("js_company_detail.php"); echo_company_detail_array($_SESSION['userid']); ?></script>
	<script><? include_once("js_audit_detail.php"); echo_audit_detail_array($_SESSION['userid'],0); ?></script>
</head>

<body>
<script>
	$(function(){

		var html_detail = "",idx = 0;
		//column_name array必須優化成json格式 不然目前依賴index順序
		var column_name = ["中文名稱","英文名稱","連絡電話","傳真號碼","統一編號","負責人　","照片網址","電子信箱","公司類別","公司位置","公司地址","資本額　","關於公司","相關文件","員工數量","相關連結"];
		for(var key in company_detail_array){
	     	//特殊處理欄位
	     	if(key == "typename"||key == "zonename"||key == "censored"){

                //公司類型
			    if(key == "typename"){
	     			html_detail+=column_name[idx]+"&emsp;&emsp;&emsp;<select name='"+key+"' id='company_type'></select> <br>";
	     		}

	     		//公司地點
			    if(key == "zonename"){
	     			html_detail+=column_name[idx]+"&emsp;&emsp;&emsp;<select name='"+key+"' id='company_zone'></select> <br>";
	     		}

	     		if(key == "censored"){
	     		//審核狀況不印出
	     		}


	     	}

            //普通欄位
	     	else{
            html_detail+=column_name[idx]+"&emsp;&emsp;&emsp;<input type='text' name ='"+key+"' value='"+company_detail_array[key]+"'><br>";
		    }
			idx++;
		}	
		    html_detail+="<br><input type='submit' value='修改資料'/>";
		$('#detail').html(html_detail);
	






		// append audit data
		/*
		<div class="company-audit-list">

			<span class="company-audit-time">2014-05-30</span>

			<span class="company-audit-censored">
				<i class="fa fa-times"></i> 不通過
			</span>

			<span class="company-audit-msg">afewewfewfewf</span>

			<span class="company-audit-via">
				審核人：<a href="staff/wu">Wu</a>
			</span>

		</div>
<!-- <i class="fa fa-times"></i> -->
<!-- <i class="fa fa-check"></i> -->
{"staff_no":"wu","censored":1,"msg":"erveververve","time":"2014-05-12 09:35:59.260"},
		*/
		
		var audit_history_container = $('#company-audit-history');
		if(audit_array.length>0) audit_history_container.html('');
		for(var i=0;i<audit_array.length;i++){

			var icontxt = (audit_array[i].censored==1)? 'fa fa-check': 'fa fa-times',
				statustxt = (audit_array[i].censored==1)? ' 通過': ' 不通過',
				time = $('<span>').addClass('company-audit-time').text(audit_array[i].time.split(' ')[0]),
				icon = $('<i>').addClass(icontxt),
				censored = $('<span>').addClass('company-audit-censored').append(icon).append(statustxt),
				msg = $('<span>').addClass('company-audit-msg').text(audit_array[i].msg),
				vialink = $('<a>').attr('href', 'staff/'+audit_array[i].staff_no).text(audit_array[i].staff_no),
				via = $('<span>').addClass('company-audit-via').append('審核者：').append(vialink),
				all = $('<div>').addClass('company-audit-list').append(time).append(censored)
				.append(msg).append(via);
				audit_history_container.append(all);
		}

		//<i class="fa fa-check"></i> 通過
		switch(company_detail_array.censored) {
			case 0:
				icontxt ='fa fa-minus-square-o';
				statustxt = ' 未審核';
				color = '#555';
				break;
			case 1:
				icontxt ='fa fa-check';
				statustxt = ' 通過';
				color = '#339933';
				break;
			case 2 :case 3:
				icontxt ='fa fa-times';
				statustxt = ' 不通過';
				color = '#CC3333';
				break;

		}		
			var icon = icon = $('<i>').addClass(icontxt),
			again_txt = $('<span>').addClass('company-audit-again-txt').text('已要求再次審核！'),
			again_btn = $('<input>').addClass('company-audit-again').attr({
				value: '請求再次審核',
				type: 'button'
			}).on('click', function(event) {
				
				$.ajax({
					url: 'ajax_audit_again.php',
					type: 'post',
					data: {},
				})
				.done(function(data) {
					if(data!='0') {
					$('.company-audit-status').append(again_txt);
					again_btn.remove();
					}
				});

			});

		$('.company-audit-status').append(icon).append(statustxt).css('color', color);
		if(company_detail_array.censored==2) $('.company-audit-status').append(again_btn);
		if(company_detail_array.censored==3) $('.company-audit-status').append(again_txt);

		// TAB control
		var tabgroup = $('div[tabtoggle="workedit1"]');
		tabgroup.click(function(event) {
			tabgroup.removeClass('tab-active');
			$(this).addClass('tab-active');
			var index = tabgroup.index( this );
			$('div[tabtoggle="workedit2"]').removeClass('workedit-content-hide');
			$('div[tabtoggle="workedit2"]:not(div[tabtoggle="workedit2"]:eq('+index+'))').addClass('workedit-content-hide');
		});

		tabgroup[<?  echo (int)$_POST['page']; ?>].click();

	});

    //從後端得到公司所有類型
    <?php include("js_company_type.php"); ?> 
    for(var i=0;i<company_type_array.length;i++)
    $("#company_type").append($("<option>").attr("value", company_type_array_id[i]).text(company_type_array[i]));

    //從後端得到公司地點
    for(var i=0;i<company_zone_array.length;i++)
    $("#company_zone").append($("<option>").attr("value", company_zone_array_id[i]).text(company_zone_array[i]));

    //js_company_detail.php取得公司類型與位置
    <? echo_company_type_and_zone($_SESSION['userid']); ?>
    $("#company_type").val(company_type);
	$("#company_zone").val(company_zone);
	
</script>




	
<div class="workedit-tabbox">
	<div class="sub-tab tab-active" tabtoggle='workedit1'><i class="fa fa-pencil"></i> 關於公司</div>
	<div class="sub-tab" tabtoggle='workedit1'><i class="fa fa-check-square-o"></i> 審核狀況</div>
</div>


<div class="workedit-content" id='workedit-content'>
	
	<div id='workedit-content-edit' class="" tabtoggle='workedit2'>
		<form method="post" action="updata.php" id="detail"></form>
	</div>



<div id='workedit-content-apply' class="workedit-content-hide" tabtoggle='workedit2'>
	
	<h1 class="company-audit-status">審核狀況：</h1>
	
	<p>歷史紀錄：</p>

	<div class="company-audit-history" id="company-audit-history">
		無歷史紀錄
	</div>
	

</div>



</div>




</body>
</html>
