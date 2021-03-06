<?php
//想知該帳號的中文名字嗎? 都來
session_start(); 

include('cjcuweb_lib.php');



switch($_POST['mode']){

  case 'work'://查詢工作的中文名稱
       echo_work_name($_POST['workid']);
  break;

  case 'work-prop'://查詢工作的分類
       echo_work_prop($_POST['workid']);
  break;

  case 'com-msg'://查詢公司的聯絡方式
        echo_company_msg($_POST['id']);
  break;
  

//=========================================
  case 'com'://查詢公司的中文名稱
        echo_company_name($_POST['comid']);
  break;

  case 'dep'://查詢系所的中文名稱
        echo_department_name($_POST['depno']);
  break;

  case 'cnd'://查詢公司或系所(學生應徵)
        if($_POST['work_pub']==1) echo_company_name($_POST['comid']);
        else if($_POST['work_pub']==2) echo_department_name($_POST['comid']);
  break;
//=========================================


  case 'img'://查詢該廠商的頭像
       echo_img_file($_POST['pub'],$_POST['id']);
  break;
}




//未來可以整合成一個function就好

function echo_work_name($workid){
  include_once("sqlsrv_connect.php");


  $sql = "select name from work where id =?"; 
  $stmt = sqlsrv_query($conn, $sql, array($workid));

  if($stmt) $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
  else{echo 0;}

  echo $row['name'];
}

function echo_work_prop($workid){
  include_once("sqlsrv_connect.php");


  $sql = "select work_prop_id from work where id =?"; 
  $stmt = sqlsrv_query($conn, $sql, array($workid));

  if($stmt) $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
  else{echo 0;}

  echo $row['work_prop_id'];
}


function echo_company_name($comid){
  include_once("sqlsrv_connect.php");


  $sql = "select ch_name name from company where id =?"; 
  $stmt = sqlsrv_query($conn, $sql, array($comid));

  if($stmt) $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
  else{echo 0;}

  echo $row['name'];
}

function echo_department_name($depno){
  include_once("sqlsrv_connect.php");


  $sql = "select ch_name name from department where no =?"; 
  $stmt = sqlsrv_query($conn, $sql, array($depno));

  if($stmt) $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);
  else{echo 0;}

  echo $row['name'];
}


function echo_company_msg($id){
  include_once("sqlsrv_connect.php");


  $sql = "select phone,address from company where id =?"; 
  $stmt = sqlsrv_query($conn, $sql, array($id));

  if($stmt) $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC);

  echo $row['phone'].'&&'.$row['address'];
}






function echo_img_file($pub,$id){

  if(trim($pub) == "1") $pub = "company";
  else if(trim($pub) == "2") $pub = "department";

  $filename = 'img_'.$pub.'/'.trim($id).'.jpg';
  if (!file_exists($filename)) $filename = 'img_company/default.png';

  echo $filename;
}

?>