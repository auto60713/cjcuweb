<?php

/* 工作列表轉成JS Array */

//首頁顯示的工作
function echo_work_list_array($work_length){

include("sqlsrv_connect.php");

$para = array();
$work_list_array = array();
if($work_length == 0) $work_length2 = ""; else $work_length2 = "TOP 0";


//過期工作=========================
$sql = "select ".$work_length2." w.company_id cid, w.id wid,w.name wname,w.publisher pub,z.name zname,w.is_outside isout,p.name propname,[recruitment _no] rno,w.date date,w.recruited_date,w.up_data 
 from work w,zone z,work_prop p 
 where w.zone_id = z.id and work_prop_id = p.id and w.[check] = 1 and recruited_date > GETDATE()";
if(isset($_GET['search'])) $sql.= " and w.name like '%".$_GET['search']."%'";
if(isset($_GET['type'])) $sql.= " and w.work_type_id = ".$_GET['type'];
if(isset($_GET['prop'])) $sql.= " and w.work_prop_id = ".$_GET['prop'];
if(isset($_GET['io'])) $sql.= " and w.is_outside = ".$_GET['io'];
if(isset($_GET['zone'])) $sql.= " and w.zone_id = ".$_GET['zone'];
$sql.= " ORDER BY up_data DESC";
$stmt = sqlsrv_query($conn, $sql, $para);
while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ){

    	if(!isset($row['up_data'])) $row['up_data'] = "2015-01-01 00:00:00.000";
		$work_list_array[] = $row;
}
//=================================
if($work_length == 0) $work_length2 = ""; else $work_length2 = "TOP ".$work_length;

$sql = "select ".$work_length2." w.company_id cid, w.id wid,w.name wname,w.publisher pub,z.name zname,w.is_outside isout,p.name propname,[recruitment _no] rno,w.date date,w.recruited_date,w.up_data 
 from work w,zone z,work_prop p 
 where w.zone_id = z.id and work_prop_id = p.id and w.[check] = 1 and recruited_date <= GETDATE()";
//check=1 只秀出通過審核的工作  and recruited_date > GETDATE()

//搜尋功能開啟======================
if(isset($_GET['search'])) $sql.= " and w.name like '%".$_GET['search']."%'";
if(isset($_GET['type'])) $sql.= " and w.work_type_id = ".$_GET['type'];
if(isset($_GET['prop'])) $sql.= " and w.work_prop_id = ".$_GET['prop'];
if(isset($_GET['io'])) $sql.= " and w.is_outside = ".$_GET['io'];
if(isset($_GET['zone'])) $sql.= " and w.zone_id = ".$_GET['zone'];
//==================================
$sql.= " ORDER BY up_data DESC";
//最新的在前面

$stmt = sqlsrv_query($conn, $sql, $para);




if($stmt) {

	while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ){

    	if(!isset($row['up_data'])) $row['up_data'] = "2015-01-01 00:00:00.000";
		$work_list_array[] = $row;
	}

	echo "var work_list_array = ". json_encode($work_list_array) . ";";	
}
else die(print_r( sqlsrv_errors(), true));




//回傳搜尋後的訊息
$work_length = count($work_list_array);
if($work_length != 0) {echo "var search_log_cont = '共有 '+".$work_length."+' 項工作符合條件 灰色項目為應徵時間過期';";}
else {echo "var search_log_cont = '沒有工作符合搜尋條件!';";}




}



// 公司管理畫面 管理工作 的 工作清單
function echo_work_manage_list_array($companyid){

	include("sqlsrv_connect.php");
	//轉換工作負責人
	if (preg_match("/-/i", $companyid)) $companyid = strstr($companyid,'-',true);
	$para = array($companyid);

	$sql = "select w.id wid,w.name wname,z.name zname,w.is_outside isout,p.name propname,[recruitment _no] rno,w.date date,t.name,w.[check] ch
	 from work w,zone z,work_prop p,work_type t
	 where w.zone_id = z.id and work_prop_id = p.id and w.company_id=? and w.work_type_id=t.id and [check]<>24 ORDER BY up_data DESC;";

	$stmt = sqlsrv_query($conn, $sql, $para);
	$work_list_array = array();

	if($stmt) while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) $work_list_array[] = $row;
	else die(print_r( sqlsrv_errors(), true));


		
	for($i=0;$i<count($work_list_array);$i++){

		// 多少人應徵
		$sql = 'select COUNT(l.work_id)c from line_up l where work_id=?';
		$para = array($work_list_array[$i]['wid']);
		$stmt = sqlsrv_query($conn, $sql, $para);
		if($stmt) $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ;
		$work_list_array[$i]['apply_count']=  $row['c'];
		
		// 多少人錄取
		$sql = 'select COUNT(l.[check])c from line_up l where  work_id=? and [check]=1';
		$para = array($work_list_array[$i]['wid']);
		$stmt = sqlsrv_query($conn, $sql, $para);
		if($stmt) $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ;
		$work_list_array[$i]['check_count']=  $row['c'];

	}

		
	echo "var work_list_array = ". json_encode($work_list_array) . ";";	
}	

//僅列出通過審核的工作 (避免其他使用者看到以及應徵)
function echo_pass_work_array($companyid){

	include("sqlsrv_connect.php");
	$para = array($companyid,1);

	$sql = "select w.id wid,w.name wname,w.is_outside isout,p.name propname
	 from work w,work_prop p
	 where work_prop_id = p.id and w.company_id=? and w.[check]=?";

	$stmt = sqlsrv_query($conn, $sql, $para);
	$pass_work_array = array();

	if($stmt) while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) $pass_work_array[] = $row;
	else die(print_r( sqlsrv_errors(), true));

	echo "var pass_work_array = ". json_encode($pass_work_array) . ";";	
}	



//學生應徵的工作
function echo_student_apply_list_array($userid){

		include("sqlsrv_connect.php");
//工作的資料
		$sql = "SELECT w.id wid,w.name wname,w.publisher pub,w.company_id comid,p.name prop,z.name zone,l.[check] ch,l.match_no tea_name,score 
				FROM work w,line_up l,work_prop p,zone z 
				WHERE l.user_id=? and w.id=l.work_id and p.id=w.work_prop_id and z.id=w.zone_id and (w.[check]IN(0,1,4,5) OR (w.[check]=24 AND l.[check]IN(1,4,5))) 
				ORDER BY l.no ASC";

		$stmt = sqlsrv_query($conn, $sql, array($userid));
		$work_list_array = array();


		if($stmt) {
		while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) $work_list_array[] = $row;
		echo "var work_list_array = ". json_encode($work_list_array) . ";";	
		}
		else die(print_r( sqlsrv_errors(), true));

}


//在學生profile上列出完成的工作
function profile_work_list($stu_no){

	include("sqlsrv_connect.php");

	$sql = "select w.id wid,w.name wname,w.publisher pub,w.company_id cid from line_up l,work w where l.user_id=? and l.[check]in(1,4,5) and w.id=l.work_id";
	$stmt = sqlsrv_query($conn, $sql, array($stu_no));
	$profile_work_list = array();

	if($stmt) while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) $profile_work_list[] = $row;
	else die(print_r( sqlsrv_errors(), true));

	echo "var profile_work_list = ". json_encode($profile_work_list) . ";";	
}	


//管理員僅維護 工讀,正職
function staff_maintain_work(){

	include("sqlsrv_connect.php");
	$para = array();

	//$sql = "select w.id,w.name,c.id com_id,c.ch_name com_name,wp.name prop,w.[check] from work w,company c,work_prop wp where w.publisher=1 and w.work_prop_id IN (1,2) and w.[check] IN (1,4,5) and c.id=w.company_id and wp.id=w.work_prop_id";
	$sql = "select w.id,w.name,c.id com_id,c.ch_name com_name,wp.name prop,w.[check] from work w,company c,work_prop wp where w.[check] IN (1,4,5) and c.id=w.company_id and wp.id=w.work_prop_id";
	$stmt = sqlsrv_query($conn, $sql, $para);
	$staff_maintain_work = array();

	if($stmt) while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) $staff_maintain_work[] = $row;
	else die(print_r( sqlsrv_errors(), true));

	echo "var staff_maintain_work = ". json_encode($staff_maintain_work) . ";";	
}	




?>