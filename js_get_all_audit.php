<?php
// $ch 為審核狀況
// 藉由本程式過濾出 不同審核狀況的清單
function  get_all_audit($ch){

include("sqlsrv_connect.php");

$sql = "select w.id id,w.name wname,w.company_id comid,c.ch_name comname,z.name zname,w.is_outside isout,p.name propname,[recruitment _no] rno,w.date date
 from work w,company c,zone z,work_prop p
 where w.zone_id = z.id and c.id=w.company_id and work_prop_id = p.id and w.[check]=?";

$stmt = sqlsrv_query($conn, $sql, array($ch));
$work_list_array = array();

if($stmt) {

	while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) 
		$work_list_array[] = $row;
	
	echo "var work_list_array".$ch." = ". json_encode($work_list_array) . ";";	

	$sql2 ="select c.id, c.ch_name 
			from company c 
			where c.censored=?";
	$stmt2 = sqlsrv_query($conn, $sql2, array($ch));
	$company_list_array = array();
	if($stmt2){
		while( $row2 = sqlsrv_fetch_array( $stmt2, SQLSRV_FETCH_ASSOC) ) 
		$company_list_array[] = $row2;
		echo "var company_list_array".$ch." = ". json_encode($company_list_array) . ";";	
	}else die(print_r( sqlsrv_errors(), true));


}
else die(print_r( sqlsrv_errors(), true));
}














?>