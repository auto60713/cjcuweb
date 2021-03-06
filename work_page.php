<!doctype html>
<html>
<head>

	<meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <link href="img/ico.ico" rel="SHORTCUT ICON">
	<title>長大職涯網</title>
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<link rel="stylesheet" type="text/css" href="css/area_div.css">
  <link rel="stylesheet" type="text/css" href="font-awesome/css/font-awesome.css">
  <script src="js/jquery-min.js"></script>
  <script src="js/jquery-migrate-min.js"></script>
	<script>
	$(function(){ 	

		$('#view-header').load('public_view/header.php');
    $("#menu").load('public_view/menu.html');
    $("#footer").load('public_view/footer.html');

	})
	</script>
    
</head>
<style>
  #page_ctrl{
      text-align: center;
      padding: 5px;
  }
  #page_ctrl a{
      cursor: pointer;
      margin-right: 20px;
  }
  .this-page{
      color: #991133;
      font-weight: bold;
  }

</style>
<body>


<!-- 版頭 -->
<div id="view-header" class=""></div>


<!-- 菜單 -->
<div id="menu"></div>


<!-- 主體 -->
<div id="main" class="div-align">
<!-- 任何區塊因為空間大小都要限制字數 -->



<!-- 左區塊 -->
<div id="left_div" class="area_box2">



    <!-- 快速搜尋 -->
    <div class="rush-search">
        <a href="work_page.php" class="<?php if(count($_GET)==0) echo "rush-searching"; ?>">最新工作</a>
        <a href="work_page.php?mode=search&io=1" class="<?php if(isset($_GET['io']))if($_GET['io']=='1') echo "rush-searching"; ?>">校內工作</a>
        <a href="work_page.php?mode=search&prop=2" class="<?php if(isset($_GET['prop']))if($_GET['prop']=='2') echo "rush-searching"; ?>">正職</a>
        <a href="work_page.php?mode=search&prop=1" class="<?php if(isset($_GET['prop']))if($_GET['prop']=='1') echo "rush-searching"; ?>">工讀</a>  
        <a href="work_page.php?mode=search&prop=3" class="<?php if(isset($_GET['prop']))if($_GET['prop']=='3') echo "rush-searching"; ?>">實習</a>      
    </div>

    <!-- 列表 -->
    <div id="home-work-list-box"></div>
    <div id="page_ctrl"></div>

</div>



<!-- 右區塊 -->
<div id="right_div" class="area_box2"><h1 id="area_title">進階搜尋</h1>

    <!-- 進階搜尋 -->
    <div class="high_search-bar">


    <!-- 名稱搜尋 -->
    <input type="text" id="normal-search">

    <!-- 條件1 -->
    <div class="search-detail-sub">
         <input type="checkbox" id="search_prop" value="prop">
         <label for="search_prop">工作性質 : </label><select name="work_prop" id="work_prop"></select>
    </div>
    
    <!-- 條件2 -->
    <div class="search-detail-sub">
        <input type="checkbox" id="search_io" value="io">
         <label for="search_io">校內外工作：</label>
         <select name="work_io" id="work_io" class="search-detail-input">
              <option value="0">校內</option>
              <option value="1">校外</option>
         </select> 
    </div>

    <!-- 條件3 -->
    <div class="search-detail-sub">
        <input type="checkbox" id="search_zone" value="zone" >
        <label for="search_zone">工作地點 : </label>
        <select name="zone" id="zone" class="search-detail-input"></select> 
        <select name="zone_name" id="zone_name" class="search-detail-input"></select>
    </div>

    <input type="button" id="search" value="搜尋">
    </div>

</div>

</div>


<!-- 頁尾訊息 -->
<div id="footer"></div>


</body>




<!--秀出工作-->
<script>
    var page = 0;
    
    <?php
    //後端傳來的工作資料
    include_once('js_work_list.php'); echo_work_list_array(0); 
    //後端傳來"進階搜尋項目"的資料
    include_once("js_search_work_data.php"); echo_work_sub_data();

    if(isset($_GET['page'])) echo "page = ".$_GET['page']."-1;";
    ?>

   
    var page_limit = 16,
        how_many_page = (work_list_array.length/page_limit)+1,
        page_sel = page*page_limit;

    for(var i=1; i<=how_many_page; i++){

        $('#page_ctrl').append($('<a>').text(i)); 
    }
    $("#page_ctrl a:eq("+page+")").addClass('this-page');

    var box = $('#home-work-list-box'),
        now = new Date();

 
    for(i=page_sel; i<page_sel+page_limit; i++){

    if(typeof work_list_array[i] === 'undefined') break;

        var box2 = $('<div>').addClass('work-box').addClass('box-detail'),
            box3 = $('<div>').addClass('work-box').addClass('box-loc'),
            box4 = $('<div>').addClass('work-box').addClass('box-pop'),

            a_link = $('<a>').attr({href:'work-'+work_list_array[i].wid}),
            div_work = $('<div>').addClass('work'),

            work_name = $('<h1>').text(work_list_array[i].wname),
            work_zone = $('<p>').text(work_list_array[i].zname).prepend($('<i>').addClass('fa fa-map-marker')),
            work_date = $('<p>').text('職缺更新日期' + work_list_array[i].up_data.split(" ")[0]).addClass('dateee'),
            work_propn = $('<p>').text(((work_list_array[i].isout=='0')?'校外 ':'校內 ') + work_list_array[i].propname),
            work_recr = $('<p>').addClass('num').text('需求 '+ work_list_array[i].rno +' 人');

            box2.append(work_name,work_date);
            box3.append(work_zone);
            box4.append(work_propn);

        //檢查是否應徵過期 date = '2015-01-01'
        var compare_date = new Date(work_list_array[i].recruited_date.split(" ")[0]);
        if( compare_date < now ) div_work.addClass('isExpired');

        //發布公司
        $.ajax({
          type: 'POST',
          async: false,
          url: 'ajax_echo_name.php',
          data:{mode:"cnd",work_pub:work_list_array[i].pub,comid:work_list_array[i].cid},
          success: function (data) { 
              work_com = $('<p>').addClass('date').text(data);
              box2.append(work_com);
          }
        });

        //公司頭像
        $.ajax({
          type: 'POST',
          async: false,
          url: 'ajax_echo_name.php',
          data:{mode:"img",pub:work_list_array[i].pub,id:work_list_array[i].cid},
          success: function (data) { 
              img = $('<img>').addClass('box-img').attr('src', data);
              div_work.append(img);
          }
        });

            div_work.append(box2,box3,box4);
            a_link.append(div_work);
            box.append(a_link);

    }

    $( "#page_ctrl a" ).click(function() {

        var page_val = $(this).text(),
            wl = String(window.location);

        if(wl.indexOf("&page") >= 0 ) window.location = window.location.href.split('&page=')[0]+'&page='+page_val; 
        else if(wl.indexOf("?page") >= 0 ) window.location = window.location.href.split('page=')[0]+'page='+page_val; 
        else if(wl.indexOf("?") >= 0 ) window.location = window.location.href+'&page='+page_val; 
        else window.location = 'work_page.php?page='+page_val; 
    });



    //搜尋結果的訊息 search_log_cont從php回傳
    if(typeof search_log_cont != "undefined") {

    var search_log = $('<a>').addClass('search-log').text(search_log_cont);
    box.prepend(search_log);
    }

    // 生成工作類型
        for(var i=0;i<work_type.length;i++)
        $("#work_type").append($("<option></option>").attr("value", work_type_id[i]).text(work_type[i]));


</script>




<!--搜尋功能的API-->
<script src="js/home_search_lib.js"></script>
</html>