<?php session_start(); header("Content-Type:text/html; charset=utf-8");

if(!isset($_SESSION['username'])) { echo "您無權訪問該頁面!"; exit; }
else if($_GET['workid']==null) { echo "錯誤的操作!"; exit; }
?>

<!doctype html>
<html>
<head>
    <title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <script>
    window.jQuery || document.write('<script src="http://code.jquery.com/jquery-1.11.0.min.js"><\/script>')
    </script>
    <script type="text/javascript" src="js/jquery-ui.js"></script>
    <script type="text/javascript" src="js/jquery.timepicker.js"></script>

</head>
<style type="text/css">
.work_time_detail{
    width: auto;
}
.column{
	display: inline;
}
#work_time_list td{
	min-width: 40px;
    height: 30px;
	text-align: center;
	overflow: hidden;
}
#work_time_list td .full-input{
	width: 100%;
}
#work_time_list td .short-input{
    width: 55px;
}
.work-time-td{
    width: 140px;
}
.input{
	color: #008ACC;
	font-weight: bold;
}
.detail{
    color: #008ACC;
    font-weight: bold;
}
.delet-tb{
    border: 0px solid black;
}
div.ui-datepicker,.ui-timepicker-wrapper{
 font-size:10px;
}

.align{
    margin-right: auto;
    margin-left: auto;
}
.work_time_detail span.title{
    display: inline-block;
    width: 100%;
    font-size: 22px;
    font-weight: bold;
    text-align: center;
    margin-bottom: 3px;
}
.pay-type{
    width: 590px;
    margin-bottom: 3px;
}
.pay-type span{
    margin-right: 30px;
    margin-left: 30px;

}
.experience{
    width: 95%;
}
.experience hr{
    margin-top: 20px;
    opacity: 0.6;
}
#work_time_list{
    margin-bottom: 5px;
}
</style>
<body>
    <h5 id="loading">資料載入中請稍後...</h5>
    <div class="work_time_detail" style="display:none;">
        <span class="title is_setting">長榮大學學生服務助學時數暨表現稽核表</span>
        <div class="pay-type align is_setting">
            <span><input type="checkbox">服務助學(工讀)金</span>
            <span><input type="checkbox">生活助學金</span>
            <span><input type="checkbox">助學生服務學習</span>
        </div>
        <form method="post" action="student_work_time_req.php">
    	<table id="work_time_list" border="2">
            <tr class="is_setting">
            	<td>系所班級</td><td class="input" id="stu_class"></td>
            	<td>姓名</td>        <td class="input" id="stu_name"></td>
            	<td>學號</td>        <td class="input" id="stu_no"></td>
                <td>服務年/月</td>   <td class="input" id="list_time"></td>
            </tr>
            <tr class="is_setting">
                <td>身分欄勾選</td>
                    <td colspan="3"><input type="checkbox">曾接受服務助學(工讀)訓練研習</td>
                    <td colspan="4"><input type="checkbox">曾接受志工基礎(或特殊)訓練</td>
            </tr>
            <tr>
                <td>工作名稱</td><td colspan="3" class="input" id="list_name"></td><td colspan="4" class="is_setting">第一次銀行帳號：______________________</td>
            </tr>
            <tr class="header">
            	<td>日期</td><td>星期</td><td>起止時間</td><td colspan="4">服務內容</td><td>時數</td>
            </tr>
            <tr class="key-in">


                <input type="hidden" name="work_id" value="<?php echo $_GET['workid']; ?>">
                <td><input type="text" val="" name="work_date"    id="work_date" class="full-input"placeholder="選擇日期"/></td>
                <td><input type="text" val="" name="work_day"     id="work_day"  class="full-input"placeholder="自動產生"/></td>
                <td class="work-time-td">
                    <input type="text" val="" name="work_bg_time" class="short-input"placeholder="開始時間"/>
                    <a>~</a>
                    <input type="text" val="" name="work_ed_time" class="short-input"placeholder="結束時間"/>
                </td>
    <td colspan="4"><input type="text" val="" name="work_matter" class="full-input"placeholder="請輸入"/></td>
                <td><input type="text" val="" name="work_hour"   class="full-input"placeholder="自動產生"/></td>
                <td class="delet-tb"><input type="submit" name="button" value="新增"/></td>
            </tr>
            <tr style="font-weight: bold;">
                <td colspan="6"></td><td>助學總時數</td><td><span class="total-hour"></span></td>
            </tr>
        </table>
    <div class="is_setting">
        <div class="experience align">
            <span style="font-weight: bold;">服務心得反思：</span>
            <span style="font-size: 14px;">(約50~100個字，注意禮貌、文字工整，勿用鉛筆)</span>
            <hr>
            <hr>
            <hr>
            <hr>
            <hr>
        </div>
        <table id="work_time_list" border="2" style="width:99%">
            <tr>
                <td style="width:20%">單位對助學生<br>服務表現評分</td><td style="width:35%"></td>
                <td style="width:15%">服務績效</td><td style="width:30%"></td>
            </tr>
            <tr style="font-weight: bold;">
                <td colspan="2">單位承辦人</td><td colspan="2">單位主官簽章</td>
            </tr>
            <tr style="height:40px;">
                <td colspan="2"></td><td colspan="2"></td>
            </tr>
       </table> 
    </div>

        <div>
        <input type="text" val="" id="now_hour_pay" placeholder="填入時薪自動換算"/> <span class="total-pay"></span>
        </div><br>

        <input type="button" name="button" id="view" value="預覽">
        <input type="button" name="button" id="back" value="上一頁">
        </form>
    </div>
</body>

<script>
$( document ).ready(function() {

    $(window).keydown(function(event){
    if(event.keyCode == 13) {
      $( "#now_hour_pay" ).change();
      event.preventDefault();
      return false;
    }
    });

    $( "#loading" ).remove();
    $( ".work_time_detail" ).fadeIn();

});

$(function(){	

    
    <?php  //load data
		include_once("js_detail.php"); echo_student_profile($_GET['studid']); 
        include_once("js_work_detail.php"); echo_work_detail_array($_GET['workid']);
                                            echo_work_time_array($_GET['workid'],$_GET['studid']);
	?>

    $( "#work_date" ).datepicker({
            dateFormat: 'yy-mm-dd'
    });
    

    $( "#work_date" ).change(function() {
        var date = $(this).datepicker('getDate');
        switch(date.getUTCDay()) {
        case 0:
            var day="一";
        break;
        case 1:
            var day="二";
        break;
        case 2:
            var day="三";
        break;
        case 3:
            var day="四";
        break;
        case 4:
            var day="五";
        break;
        case 5:
            var day="六";
        break;
        case 6:
            var day="日";
        break;
        } 
        $( "#work_day" ).val( day );
    });

    var list_time = parseInt(work_detail_array['start_date'].split("-")[0])-1911;
    $('#stu_class').text(student_profile_array['dm_name']+student_profile_array['sd_grade']+student_profile_array['cla_name']);
    $('#stu_name').text(student_profile_array['sd_name']);$('title').text(student_profile_array['sd_name']+'的工讀單');
	$('#stu_no').text(student_profile_array['sd_no']);
        
    $('#list_name').text(work_detail_array['name']);
    $('#list_time').text(list_time+"/N");

   
    //時間API
    $('input[name="work_bg_time"]').timepicker({ 
        'timeFormat': 'H:i',
        'step': 60,
        'minTime': '8:00',
        'maxTime': '17:00'
    });
    //開始時間牽制結束時間
    $('input[name="work_bg_time"]').change(function() {
        var mintime = parseInt($(this).val().split(':')[0]);
        $('input[name="work_ed_time"]').timepicker('option', 'minTime', mintime+':00');
        $('input[name="work_ed_time"]').timepicker('option', 'maxTime', (mintime+4)+':00');
    });

   
    $('input[name="work_ed_time"]').timepicker({ 
        'timeFormat': 'H:i',
        'step': 60,
    });
    //結束時間決定總時數
    $('input.short-input').change(function() {
        var work_bg_time = parseInt($('input[name="work_bg_time"]').val().split(':')[0]),
            work_ed_time = parseInt($('input[name="work_ed_time"]').val().split(':')[0]),
            work_hour = work_ed_time - work_bg_time;
        if(work_bg_time<=12 && work_ed_time>12) work_hour -= 1;
        if(work_hour>=0) $('input[name="work_hour"]').val(work_hour);
        else $('input[name="work_hour"]').val(0);
    });


    for(var i=0;i<work_time_array.length;i++){

        if(work_time_array[i]['check'] == 1) var check_class = "work-time-nocheck";
        else if(work_time_array[i]['check'] == 2) check_class = "work-time-ischeck";

    var work_date = $('<td>').addClass(check_class).text(work_time_array[i]['date']),
        work_day = $('<td>').addClass(check_class).text(work_time_array[i]['day']),
        work_time = $('<td>').addClass(check_class).text(work_time_array[i]['bg_time']+'~'+work_time_array[i]['ed_time']),
        work_matter = $('<td>').addClass(check_class).text(work_time_array[i]['matter']).attr("colspan","4"),
        work_hour = $('<td>').addClass(check_class).text(work_time_array[i]['hour']).addClass('work-hour'),
        delet_btn = $('<button>').attr({type:"button",name:"delet_btn",value:work_time_array[i]['no']}).text("刪除"),
        delet = $('<td>').addClass('delet-tb').append(delet_btn),
        tr = $('<tr>').addClass('detail').append(work_date,work_day,work_time,work_matter,work_hour,delet);

        $(tr).insertBefore( '.key-in' );
    }


    
    var total_hour = 0;
    for(i=0;i<$( ".work-hour.work-time-nocheck" ).length;i++){

        total_hour += parseInt($( ".work-hour.work-time-nocheck:eq("+i+")" ).text());
    }
    $( ".total-hour" ).append(total_hour); $( ".total-pay" ).text('總時薪：??');

    $( "#now_hour_pay" ).change(function() {
        var now_hour_pay = $( "#now_hour_pay" ).val();
        $( ".total-pay" ).text('總時薪：'+total_hour*now_hour_pay+'元');
    });

   






    $( "#view" ).click(function() {
        window.open("student_work_time.php?studid="+<?php echo "\"".$_GET['studid']."\"" ?>+"&workid="+<?php echo $_GET['workid'];?>+"&view=1");
    });
    $( "#back" ).click(function() {
        location.replace('student_manage.php#student-applywork');
    });
    $( "button[name=delet_btn]" ).click(function() {
        //刪除此工讀項目
        $.ajax({
          type: 'POST',
          url: 'delete.php',
          data: {mode:2,no:$(this).val()},
          success: function (data) { if(data==1) location.reload(); }
        });
    });
    
    var view = "";
    <?php if(isset($_GET['view'])) echo "view = ".$_GET['view'].";"; ?>
    if(view == 1){
        $('.key-in, .delet-tb, input[name=button]').remove();

        $("td").css({ 
            "padding-left":"10px",
            "padding-right":"10px"
        });
        $(".detail td:eq(3)").css( "width","200px" );

    }
    else{
        $('.is_setting').remove();
        $('#list_name').attr('colspan','7');
    }

    $( ".work_time_detail" ).css('width',$( "#work_time_list" ).width()+10);
});
</script>

</html>