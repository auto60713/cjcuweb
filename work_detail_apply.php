<?php session_start(); 

include("cjcuweb_lib.php");
include("sqlsrv_connect.php");

$checkVars = array($level_staff,$level_department);
if(!isset($_SESSION['username'])||(!isCompanyWork($conn,$_SESSION['username'],$_GET['workid']) && !in_array($_SESSION['level'], $checkVars))) {

	echo "No permission!"; 
	exit;
}
else $company_id = $_SESSION['username']; 


// 是否為該公司的工作
function isCompanyWork($conn,$companyid,$workid){
// 工作負責人轉換
if (preg_match("/-/i", $companyid)) $companyid = strstr($companyid,'-',true);

	$sql = "select company_id from work where id=?";
	$params = array($workid);
	$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
	$result = sqlsrv_query($conn,$sql,$params,$options);
	$row = sqlsrv_fetch_array($result,SQLSRV_FETCH_NUMERIC);
	if($row[0]==$companyid) return true;
	else return false;
}


?>

<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
	<style type="text/css">
        .isapply,.isnotapply,.unapply,.gondo{
        	padding: 5px;
        	font-size: 17px;
        	font-weight: bold;
        }
        .isapply{
        	color: #339933;
        }
        .isnotapply{
        	color: #CC3333;
        }
        .staff-audit-btn2{
        	padding: 0px 10px;
        }
	</style>
    <script>

    <?php include_once('js_work_detail_apply.php'); echo_work_apply_list_array($_GET['workid']);  ?>

    $(function(){

        //依照工作狀態 更換標題
    	if(work_apply_list_array.length>0){

    		if(work_apply_list_array[0]['check'] == 5) $('.what-is-list').text("工讀學生列表");
        }
        else{
            $('.what-is-list').text('目前尚無人應徵！');
        }

        var body = $('#company-work-list-container');

    	for(var i=0;i<work_apply_list_array.length;i++){

            //左邊
            var img_src='http://esrdoc.cjcu.edu.tw/esr_photo/'+work_apply_list_array[i]['sd_syear'].trim()+'/'+work_apply_list_array[i]['user_id'].trim()+'.jpg',
    		    wimg = $('<img>').attr('src', img_src).addClass('work-img-apply'),
    			tita = $('<a>').attr({'target':'_blank','href':'student-'+work_apply_list_array[i]['user_id']}).text(work_apply_list_array[i]['name']),
    			doca = $('<a>').attr({'target':'_blank','href':'department-'+work_apply_list_array[i]['depno']}).append(work_apply_list_array[i]['depname']),

    			subbox1 = $('<div>').addClass('sub-box').append(wimg),
    			subbox2 = $('<div>').addClass('sub-box').append($('<h1>').addClass('work-tit').append(tita)).append($('<p>').addClass('work-hint').append(" 就讀於 ",doca)),
    			subbox3 = $('<div>').addClass('sub-box2');

                var auditbtn = $('<button>').addClass('staff-audit-btn2').append(' 審核');
                 	auditbtn.on('click', {arr:work_apply_list_array[i]} ,function(event) {
					//event.data.arr['user_id']

						$('.staff-apply-form').remove();

						var hidden1 = $('<input>').attr({value: event.data.arr['user_id'], type:'hidden', id:'hidden_id'}),
							icon = $('<i>').addClass('fa fa-pencil-square-o'),
							close = $('<i>').addClass('fa fa-times').addClass('stu-apply-box-close'),
							tbox = $('<h1>').append(icon).append(' '+event.data.arr['user_id']).css('font-size', '28px').append(close),
							span = $('<span>').text('審核說明：').css('color', '#444'),
							t = $('<textarea>').attr({id: 'staff-audit-apply-msg',placeholder:'選填'}),
							ok = $('<input>').attr({id: 'staff-audit-apply-ok', type: 'button',value :'通過'}).on('click', function(event) {
								submit_audit(true);
							}),
							no = $('<input>').attr({id: 'staff-audit-apply-no', type: 'button',value :'不通過'}).on('click', function(event) {
								submit_audit(false);
							}),
							errtext= $('<span>').attr('id', 'staff-audit-error'),
							gbtn = $('<div>').addClass('staff-apply-gbtn').append(errtext,ok,no),
							box = $('<div>').addClass('staff-apply-box').append(tbox,"<hr><br>",span,"<br>",t,"<br>",gbtn,hidden1),

							bg = $('<div>').addClass('staff-apply-form').append(box);

							close.click(function(event) {$('.staff-apply-form').remove();});

						$('body').append(bg);
		            });
        

                //工作各狀態要顯示的右邊區域
				switch(work_apply_list_array[i]['check']) {

					//要求再審
					case 0:case 3:
						subbox3.append(auditbtn);
					break;
					case 2:case 22:
						subbox3.append($('<span>').addClass('isnotapply').text('不錄取'));
					break;	
					case 1:case 4:case 5:
					$.ajax({
		                type: 'POST',
		                url: 'ajax_echo_name.php',
		                async: false,
		                data: {mode:'work-prop',workid:<?php echo (int)$_GET['workid']; ?>},
		                success: function (data) {
		                	//實習
		                	if(data ==3){
		                		var work_time_link = $('<a>').attr({href:"student_work_time_list.php?studid="+work_apply_list_array[i]['user_id'].trim()+"&workid="+work_apply_list_array[i]['work_id'],target:"_blank"}).text("工讀單"),
					                text = $('<p>').text("學生的實習分數："+work_apply_list_array[i]['score']+"分"),
					                score = $('<input>').addClass('score').attr({type:"text",placeholder:"打分數",name:work_apply_list_array[i]['user_id'].trim()}),
					                submit = $('<button>').attr({type:"button",name:"score_btn","no":work_apply_list_array[i]['no'],value:work_apply_list_array[i]['user_id'].trim()}).text("確定");
						        subbox3.append(work_time_link,text,score,submit);
		                	}
		                	//工讀.正職
		                	else if(data ==1||data ==2){
		                		subbox3.append($('<span>').addClass('isapply').text('已錄取'),$('<i>').addClass('fa fa-times unapply').attr("no",work_apply_list_array[i]['no']),$('<br>'));
                                subbox3.append($('<a>').attr({href:"student_work_time_list.php?studid="+work_apply_list_array[i]['user_id'].trim()+"&workid="+work_apply_list_array[i]['work_id']+"&view=1",target:"_blank"}).text("點我查看工讀單").addClass('gondo'));
		                	}

		                }
		            });
					break;	
				}	
			var mainbox = $('<div>').addClass('work-list-box').append(subbox1,subbox2,subbox3);
				body.append(mainbox);


    	}//for迴圈的下括號

   

    	function submit_audit(boo){

    		if(boo) txt = $('<span>').addClass('isapply').text('已錄取');
    		else txt = $('<span>').addClass('isnotapply').text('不錄取');

    		var userid= $('#hidden_id').val(), chk = boo? 1:2;
    		var msg = $('#staff-audit-apply-msg').val();
    		$.ajax({
					  type: 'post',
					  url: 'company_work_apply_user.php',
					  data: {check:chk, user:userid ,workid:<?php echo $_GET['workid']; ?>,msg:msg},
					  befoerSend:function(){
					  	$('#staff-audit-apply-ok, #staff-audit-apply-no ,#staff-audit-apply-msg').attr('disabled', '');
					  },
					  
			}).done(function(data){
				console.log(data);
				if(data.split('-')[0]=='0'){
					alert('fail');
					$('#staff-audit-apply-ok, #staff-audit-apply-no ,#staff-audit-apply-msg').removeAttr('disabled');
				}
				else{
					subbox3.empty().append(txt);
					$('.staff-apply-form').fadeOut('fast', function() {	$(this).remove();});
				}
			});

    	}

    $( "button[name=score_btn]" ).click(function() {
        //打分數
        var stud_id = $(this).val(),
            line_no = $(this).attr('no'),
            score_val = $('input[name='+stud_id+']').val();
        $.ajax({
          type: 'POST',
          url: 'ajax_work_edit.php',
          data: {mode:6,no:line_no,score:score_val},
          success: function (data) { if(data==1) location.reload(); }
        });
      
    });


    	
    });
    </script>
</head>
<body>
<!--
<div id='search-box'>
<input type='text' placeholder='搜尋應徵者名稱' id='search-txt'>
<input type="button" value="搜尋" id='search-btn'>
</div>-->
<h3 class="what-is-list">應徵者列表</h3>
<div id='company-work-list-container'>

</div>
</body>
</html>