<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
  <link href="img/ico.ico" rel="SHORTCUT ICON">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>長大職涯網</title>
	<link rel="stylesheet" type="text/css" href="css/main.css">
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
<style type="text/css">

#back_div{
    width: 1100px;
    height: 920px;
    background-color: #FFF;

}
#master_div{
    background-image: url("img/inner4_area01.jpg");
    background-repeat: no-repeat;
    background-size: 100% 100%;

    position: relative;
    overflow: hidden;
    float: left;

    width: 70%;
    height: 800px;

    margin-top: 20px;
    margin-left: 15%;

    padding-top: 30px;
    padding-right: 15px;
    padding-bottom: 40px;
}
.links-show{

    margin-top: 20px;
	margin-left: 10%;
}
.links-show p{

    font-size: 1.3em;
    font-weight: bold;
    margin-bottom: 10px;
}
</style>
<body>


<!-- 版頭 -->
<div id="view-header" class=""></div>

<!-- 菜單 -->
<div id="menu"></div>

<!-- 主體 -->
<div id="back_div" class="div-align">

<!-- 主區塊 -->
<div id="master_div">

    <div class="links-show">

    </div>
</div>

</div>


<!-- 頁尾訊息 -->
<div id="footer"></div>


</body>





<script>

        
        $.ajax({
          type: 'POST',
          async: false,
          url: 'cjcu_career/cc/index.php/links',
          data:{},
          success: function (data) { 

              var links_json = JSON.parse(data);

              for(key in links_json) {
	              var link = $('<a>').attr({"href":links_json[key]['href'],"target":"_blank"}).text("► "+links_json[key]['name']),
                      adata = $('<p>').append(link);

                  $('.links-show').append(adata);
  
              }
          }
        });


</script>
</html>