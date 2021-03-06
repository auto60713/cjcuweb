<?php
$GLOBALS['cust_company'] ='';



//work_profile
function echo_work_detail_array($work_id){

include("sqlsrv_connect.php");
include_once("cjcuweb_lib.php");


$sql = "declare @h int;set @h = (select work_type_id from work where id=?);
		declare @i int;set @i = (select c.parent_no from work_type c where c.id=@h);
	  	declare @j int;set @j = (select b.parent_no from work_type b where b.id=@i);	  

	    select w.name,w.date,w.company_id,w.publisher pub,one.name typeone,two.name typetwo,three.name typethree,w.recruited_date,w.start_date,w.end_date,
	    prop.name popname,w.is_outside,z.name zonename,w.address,w.phone,w.pay,[recruitment _no],w.detail,[check]
	    from work w,work_type one,work_type two,work_type three,work_prop prop,zone z 
	    where w.id=? and w.work_prop_id=prop.id and w.zone_id=z.id  and one.id=@j and two.id=@i and three.id=@h";

$stmt = sqlsrv_query($conn, $sql, array($work_id,$work_id));
if($stmt) $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC); 
else die(print_r( sqlsrv_errors(), true));
$row['detail'] = preg_replace("/\r\n|\r/", "<br>", $row['detail']);
echo "var work_detail_array = ". json_encode($row) . ";";
$GLOBALS['cust_company'] = $row['company_id'];
$GLOBALS['publisher'] = $row['pub'];
}


//assign 給前端,讓其能設定工作的目前資料,以提供修改
function echo_work_detail_edit_array($conn,$work_id){

include_once("cjcuweb_lib.php");

// id name date *company_id [work_type_id] start_date end_date [work_prop_id] is_outside 
// [zone_id] address phone pay recruitment _no detail check 

// GOD SQL query
$sql = "declare @h int;set @h = (select work_type_id from work where id=?);
		declare @i int;set @i = (select parent_no from work_type  where id=@h);
		declare @j int;set @j = (select parent_no from work_type  where id=@i);

		select w.id,w.name,[date],t1.id type1,t2.id type2,t3.id type3,
		w.recruited_date,w.start_date,w.end_date,w.work_prop_id prop,w.is_outside,w.zone_id,w.address,w.phone,w.pay,[recruitment _no] rno,w.detail,[check],z.zone zone
		from work w , work_type t1,work_type t2,work_type t3,zone z
		where w.id=? and t1.id=@j and t2.id=@i and t3.id=@h and z.id=w.zone_id";

$stmt = sqlsrv_query($conn, $sql, array($work_id,$work_id));
if($stmt) $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC); 
else die(print_r( sqlsrv_errors(), true));
echo "var work_detail_array = ". json_encode($row) . ";";

}



//複製工作
if(isset($_POST['workid'])) return_work_detail_array( $_POST['workid'] );

//這邊是要讓複製工作的 ajax 行為抽取工作詳細資料
function return_work_detail_array($work_id){

	include_once("sqlsrv_connect.php");

	$sql = "declare @h int;set @h = (select work_type_id from work where id=?);
		declare @i int;set @i = (select parent_no from work_type  where id=@h);
		declare @j int;set @j = (select parent_no from work_type  where id=@i);

		SELECT w.id,w.name,w.work_prop_id prop,[date],t1.id type1,t2.id type2,t3.id type3,w.recruited_date,
		w.start_date,w.end_date,w.zone_id,w.address,w.phone,w.pay,[recruitment _no] rno,w.detail,[check],z.zone zone
		FROM work w , work_type t1,work_type t2,work_type t3,zone z
		WHERE w.id=? and t1.id=@j and t2.id=@i and t3.id=@h and z.id=w.zone_id";

	$stmt = sqlsrv_query($conn, $sql, array($work_id,$work_id));
	if($stmt) $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC); 
	else die(print_r( sqlsrv_errors(), true));
	echo json_encode($row);
}



//該工作的所有工讀單
function echo_work_time_list_array($work_id,$stud_id){

include("sqlsrv_connect.php");

$sql = "select * from work_time_list where work_id=? and stud_id=?";

$para = array($work_id,$stud_id);
$stmt = sqlsrv_query($conn, $sql, $para);

if($stmt) {
    $work_time_list_array = array();
	while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
		
		//array_walk($row, 'trim_value');
		$row['stud_id']=trim($row['stud_id']);
		$row['year']   =trim($row['year']);
		$row['month']  =trim($row['month']);
		$work_time_list_array[] = $row;
	}
	echo "var work_time_list_array = ". json_encode($work_time_list_array) . ";";	
}
else die(print_r( sqlsrv_errors(), true));
}





//該工作的工讀單項目
function echo_work_time_array($list_no){

include("sqlsrv_connect.php");

$sql = "select * from work_time where list_no IN (".$list_no.") ORDER BY date ASC";
$para = array($list_no);
$stmt = sqlsrv_query($conn, $sql, $para);

if($stmt) {
    $work_time_array = array();
	while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
		
		array_walk($row, 'trim_value');
		$work_time_array[] = $row;
	}
	echo "var work_time_array = ". json_encode($work_time_array) . ";";	
}
else die(print_r( sqlsrv_errors(), true));


//抓取該工作的年月,名字
$sql = "select wl.year,wl.month,wk.name wname from work_time_list wl,work wk where wl.no IN (".$list_no.") and wk.id=wl.work_id";
$stmt = sqlsrv_query($conn, $sql, array($list_no));
if($stmt) {

    $echo_month = "";
	while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
		$echo_work_date = trim($row['year']);
		$echo_work_name = $row['wname'];
		if($echo_month=="") $echo_month .= " / ".trim($row['month']);
		else $echo_month .= ",".trim($row['month']);
	}
	echo "var echo_work_date = '". $echo_work_date .$echo_month. "';";	
	echo "var echo_work_name = '". $echo_work_name . "';";	
}



}


?>