<? session_start(); ?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?
if(isset($_SESSION['username'])) $userid = $_SESSION['username']; 
else{echo "您無權訪問該頁面!"; exit;} 
?>

<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/work.css">
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
    <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
	<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
    <script><? include_once('js_work_list.php'); echo_student_apply_list_array($userid);  ?>

    /* front-end 架構
<div class="work-list-box">

	<div class="sub-box"><img src="" class="work-img"></div>
	<div class="sub-box">
		<h1 class="work-tit"><a href="#">標題</a></h1>
		<p class="work-hint">類別<br>校外 工讀<br>時間0</p>
	</div>
	<div class="sub-box2">
		<p>未審查</p>
	</div>

</div>
    */
    $(function(){

 		 var body = $('#company-work-list-container');

		 for(var i=0;i<work_list_array.length;i++){
		   
		    	//work_list_array[i][apply_count]
		    	var check_status='';

		    	var icon = $('<i>').addClass('fa fa-book icon').addClass('work-img'),
		    		
		    		tita = $('<a>').attr('href', 'work/'+work_list_array[i]['wid']).text(work_list_array[i]['wname']),
		    		tit = $('<h1>').addClass('work-tit').append(tita),
		    		hint = $('<p>').addClass('work-hint')
		    		.append(work_list_array[i]['name']+'<br>'+ (work_list_array[i]['isout']=='0'?'校內 ':'校外 ')+ work_list_array[i]['propname'] +'<br>'+ work_list_array[i]['date']),
		    		hint2 = $('<p>').attr('id', work_list_array[i]['wid']).addClass('check-lightbox'),
		    		pass = $('<div>').attr('id', work_list_array[i]['wid']).addClass('pass-req').text("要求再審核"),
		    		statustxt = $('<span>').addClass('nocheck').text('已要求重新再審！'),
		    		subbox1 = $('<div>').addClass('sub-box').append(icon),
		    		subbox2 = $('<div>').addClass('sub-box').append(tit).append(hint),
		    		subbox3 = $('<div>').addClass('sub-box2').append(hint2),

		    		mainbox = $('<div>').addClass('work-list-box').append(subbox1).append(subbox2).append(subbox3);
		    		
		    		switch(work_list_array[i]['ch']) {
		    			//老師說要正名
		    		case 0: check_status='尚未被公司審核'; hint2.addClass('sta1 onecheck').text(check_status); break;
		    		case 1: check_status='應徵成功!'; hint2.addClass('sta2 yescheck').text(check_status); break;
		    		case 2: check_status='應徵失敗!'; hint2.addClass('sta3 nocheck').text(check_status); subbox3.append(pass); break;
		    		case 3: check_status='應徵失敗!'; hint2.addClass('sta4 nocheck').text(check_status); subbox3.append(statustxt); break;
		    		case 4: check_status='已錄取'; hint2.addClass('sta5 yescheck').text(check_status); break;
		    		case 5: check_status='不錄取'; hint2.addClass('sta6 nocheck').text(check_status); break;
		    		case 6: check_status='完成工作'; hint2.addClass('sta7 yescheck').text(check_status); break;
		    		break;
		    		}

		    		body.prepend(mainbox);
		    }


		
		  $('#search-txt').on('input', function(event) {
		  	console.log($('#search-txt').val());
		  	resort_work($('#search-txt').val());
		  });

		  function resort_work(txt){
		  		if(txt=='') $('.work-list-box').removeClass('hide-work');
		  		else{
		  			$('.work-list-box').each(function(index, el) {
		  			var tit_txt = $(this).find('.work-tit a').text().toLowerCase();
		  			var search_txt = txt.toLowerCase();
		  			if(tit_txt.match(search_txt)==null) $(this).addClass('hide-work');
		  			else $(this).removeClass('hide-work');
		  			});
		  		}
		  }

          //注入條件
          var pass_search = ["全部", "尚未審核", "應徵成功", "應徵失敗", "已錄取", "不錄取", "完成工作"];
		  for(var i=0;i<pass_search.length;i++)
          $("#search-sel").append($("<option>").attr("value", i).text(pass_search[i]));

          //用錄取篩選
          $("#search-sel").change(function(event) {
          	sel_val = $('#search-sel').val();
          	sel_txt = $('#search-sel option:selected').text();

          	$('.work-list-box').each(function(index, el) {
          	if(sel_val==0){
                $('.work-list-box').removeClass('hide-work');
          	}
          	else{
          		var match_check = $(this).find('.sub-box2 p').attr('class');
          		if(match_check.indexOf('sta'+sel_val) >= 0){ $(this).removeClass('hide-work');}
          		else{ $(this).addClass('hide-work'); }
		    }
	      });
	      });

          //再次應徵
	        $('.pass-req').click(function() {
               work_id = $(this).attr('id');
               btn = $(this);
               statustxt = $('<span>').addClass('nocheck').text('已要求重新再審！'),

                $.ajax({
				url: 'ajax_line_up.php',
				type: 'post',
				data: {check:3, work_id:work_id},
		    	})
		    	.done(function(data){
		    		btn.parents('.sub-box2').append(statustxt);
		    		btn.remove();
		    	});
	     
          });

        //lightbox api
	    $( ".check-lightbox" ).click(function() {
        //$('body').append($('<div>').addClass('staff-apply-form').append($('<div>').addClass('staff-apply-box').append('efwefwefwef')));
        var workid = $(this).attr('id');
        
        });
		$( "#lightbox-exit" ).click(function() {
        $( ".staff-apply-form" ).remove();
        });

        switch(work_detail_array.check) {
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
			case 2 : case 3:
				icontxt ='fa fa-times';
				statustxt = ' 不通過';
				color = '#CC3333';
				break;
		}
        

    });
</script>
</head>
<body>
	<!-- 該工作的審核狀態 
	<div class="staff-apply-form"> <div id='workedit-content-audit' class="staff-apply-box"> 

		<h1 class="company-audit-status">審核狀況：</h1>
		<p>歷史紀錄：</p>
		<div class="company-audit-history" id="company-audit-history">無歷史紀錄</div>
	</div>
	</div> </div>
	-->
<div id='search-box'>
<select id='search-sel'></select> 
<input type='text' placeholder='搜尋工作名稱' id='search-txt'>
</div>
<div id='company-work-list-container'></div>
</body>
</html>