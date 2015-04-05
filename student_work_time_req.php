<?php session_start(); 
header("location:student_work_time.php?studid=".trim($_SESSION['username'])."&listid=".$_POST['list_no']);

switch($_POST['mode']){

  case 'time-list'://新增工讀單
       creat_work_time_list($_POST['work_id'],$_POST['stud_id'],$_POST['year'],$_POST['month']);
  break;

  case 'time'://新增工讀單的項目
       creat_work_time();
  break;

}




function creat_work_time_list($work_id,$stud_id,$year,$month){

include("sqlsrv_connect.php");

if(empty($year) || empty($month)){
	echo 'You enter data missing!';
}
else{

    $sql = "INSERT INTO work_time_list (work_id,stud_id,year,month,[check]) VALUES (?, ?, ?, ?, ?)";
    $params = array($work_id,$stud_id,$year,$month,1);
  

    $stmt = sqlsrv_query( $conn, $sql, $params);
    if( $stmt === false ) {

         echo 'Database write error';
    }
    else echo 'Success';
   
}

}


function creat_work_time(){

include("sqlsrv_connect.php");

$list_no   = trim($_POST['list_no']);
$date   = trim($_POST['work_date']);
$day    = trim($_POST['work_day']);
$bg_time   = trim($_POST['work_bg_time']);
$ed_time   = trim($_POST['work_ed_time']);
$matter = trim($_POST['work_matter']);
$hour   = trim($_POST['work_hour']);

if(empty($list_no) || empty($date) || empty($day) || empty($bg_time) || empty($ed_time) || empty($matter) || empty($hour)){
	echo 'You enter data missing!';
}
else{

    $sql = "INSERT INTO work_time (date,day,bg_time,ed_time,matter,hour,list_no) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $params = array($date,$day,$bg_time,$ed_time,$matter,(int)$hour,(int)$list_no);
  

    $stmt = sqlsrv_query( $conn, $sql, $params);
    if( $stmt === false ) {

        echo 'Database write error';
        die( console.log('PHP:'.sqlsrv_errors())  );
    }
   
}

}








?>