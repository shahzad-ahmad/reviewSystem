<?php
use \PHPMailer;

/**
*	get base url from the URL
*	return string 	
*/
function getCurrentUri()
{
	$basepath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
	$uri = substr($_SERVER['REQUEST_URI'], strlen($basepath));
	if (strstr($uri, '?')) $uri = substr($uri, 0, strpos($uri, '?'));
	$base_url = '/' . trim($uri, '/');
	return $base_url;
}


/**
* Sending Email
*/

function sendEmail($to , $from , $subject, $body){
	require_once('vendor/PHPMailer/PHPMailerAutoload.php');
	$mail = new PHPMailer();
	
	$mail->SMTPDebug = 2; 
	//$mail->AddReplyTo('shahzad.malik@coeus-solutions.de', 'Clark Kent');
	$mail->SetFrom($from, 'Review System');
	$mail->AddAddress($to, $to);
	$mail->Subject = $subject;
	$mail->MsgHTML( $body );
	 
	return  $mail->Send() ;
}

/*
Random password 
*/
function randomPassword() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 10; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }

	if(isPasswordAlreadExist() )
		$ret_val = randomPassword();
    else $ret_val = implode($pass); //turn the array into a string
    return $ret_val;
}

/**
check if password alread Exist in database
*/
function isPasswordAlreadExist($password){
	global $db;
	$db->query("SELECT id FROM User WHERE password = :pwd ");
	$db->bind(':pwd', $password ,PDO::PARAM_STR);
	$result = $db->resultset();
	$count = $db->rowCount() ;
	if($count == 0 )
		return 0;
	else return 1;
}

/**
check redirection based on if session exist or not
*/

function isRedirectionApplied(){
	global $ajax_request_url;
	global $without_session_pages;
	global $api_url;
	$base_url =  getCurrentUri();
	
	if( !in_array($base_url, $api_url)){
		if($base_url == URL_ROOT){
			header('Location: '.dir_root_path.'login');
		}
		else if(! in_array($base_url, $ajax_request_url) ){
			if(! (isset($_SESSION) && isset($_SESSION['user_id'])  )  
				&&  ( !in_array($base_url, $without_session_pages) ) 
				) 
				header('Location: '.dir_root_path.'login');
			else if(  ( in_array($base_url, $without_session_pages) )
					&&  (isset($_SESSION) && isset($_SESSION['user_id']) )
					){
				if($_SESSION['user_type'] == USER_TYPE_ADMIN)
					header('Location: '.dir_root_path.ltrim(URL_ADMIN_DASHBOARD,"/") );
				else
					header('Location: '.dir_root_path.ltrim(URL_USER_DASHBOARD,"/") );
			}
		}
	}
}


/**
get user id from from
*/

function get_user_id($token){
	global $db;
	$db->query("SELECT user_id FROM access_tokens WHERE access_token = :acc_token");
	$db->bind(':acc_token', $token ,PDO::PARAM_STR);
	
	$result = $db->resultset();
	$count = $db->rowCount() ;
	if($count > 0 ){
		return  $result[0]['user_id'];
	}else return 0;
}

/**
* create unique review page id
*/

function createUniqueId($user_id,$order_id,$token){
	return md5($user_id.$order_id.$token);
}

function isReviewAlreadAdded($unique_review_id){
	global $db;
	$db->query("SELECT * FROM `review_details` as rev
				Join `order` as ord ON ord.id = rev.order_id
				WHERE review_page_id = :review_id");
	$db->bind(':review_id', $unique_review_id ,PDO::PARAM_STR);
	$result = $db->resultset();
	$count = $db->rowCount() ;
	if($count)
		return true;
	else 
		return false;
}
