<?php
session_start();


include_once("sqlsrv_connect.php");
include_once("cjcuweb_lib.php");

$sel    = (trim($_POST['sel']));
$userid = (trim($_POST['id']));
$pw     = (trim($_POST['pw']));

switch ($sel) {
    case "school":
        school_login($conn,$userid,$pw);
    break;
    case "company":
        company_login($conn,$userid,$pw,$level_company);
    break;
    case "department":
        department_login($conn,$userid,$pw,$level_department);
    break;
    case "teacher":
        teacher_login($conn,$userid,$pw);
    break;
    // 竄改資料,登入失敗
    default: login_echo(0);
}


function school_login($conn,$userid,$pw){

    //此登入包含 學生,校友

    $sql = "select * from career_student_data where sd_stud_no=?";
    $params  = array($userid);
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

    $result  = sqlsrv_query( $conn , $sql , $params , $options );
    if( $result ){

    $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
    
        // 資料表查無帳號 , 沒有輸入
        if(count($row) != 0 && $userid != null && $pw != null){

            $_SESSION['username'] = $row['sd_stud_no'];
            $_SESSION['level'] = 3;

            login_echo(1);
        }
        else{
            login_echo(0);
        }
    }
}

function teacher_login($conn,$userid,$pw){

    $sql = "select * from cjcu_user where user_no=?";
    $params  = array($userid);
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

    $result  = sqlsrv_query( $conn , $sql , $params , $options );
    if( $result ){

    $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
    
        // 資料表查無帳號 , 沒有輸入
        if(count($row) != 0 && $userid != null && $pw != null){

            $_SESSION['username'] = $row['user_no'];
            $_SESSION['level'] = 2;

            login_echo(1);
        }
        else{
            login_echo(0);
        }
    }
}


function company_login($conn,$userid,$pw,$level_company){

    $sql = "select * from company where id=?";
    $params  = array($userid);
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

    $result  = sqlsrv_query( $conn , $sql , $params , $options );
    if( $result ){

	$row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
	
	    // 資料表查無帳號 , 沒有輸入或密碼不符
	    if(count($row) != 0 && $userid != null && $pw != null && trim($row['pw']) == $pw){

            $_SESSION['username'] = $userid;
            $_SESSION['level'] = $level_company;

            login_echo(1);
	    }
        
	    else{

	    	echo (count($row)." ".trim($row['pw'])." ".$pw);
	    }
	
    }
}

function department_login($conn,$userid,$pw,$level_department){

    $sql = "select * from department where no=?";
    $params  = array($userid);
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

    $result  = sqlsrv_query( $conn , $sql , $params , $options );
    if( $result ){

    $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
    
        // 資料表查無帳號 , 沒有輸入或密碼不符

        if(count($row) != 0 && $userid != null && $pw != null && (trim($row['pw'])) == $pw){

            //職涯發展組
            if(trim($row['sort']) == 3){
                $_SESSION['username'] = $userid;
                $_SESSION['level']  = 1;

                login_echo(1);
            }
            else{
                $_SESSION['username'] = $userid;
                $_SESSION['level'] = $level_department;

                login_echo(1);
            }
        }
        else{

            login_echo(0);
        }
    }
}




function login_echo($login_msg){

    switch ($login_msg) {
    case 1:
        echo '1';
    break;
    default: 
        echo '0';
    }
  
}

session_write_close(); 

?>