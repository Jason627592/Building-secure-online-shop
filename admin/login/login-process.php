<?php header('X-Powered-By:'); ?>
<?php
include_once('./auth-process.php');
if( !defined( __DIR__ ) ) define( __DIR__, dirname(__FILE__) );
require __DIR__.'/../csrf/nonce.php';
header('Content-Type: application/json');

// input validation
if (empty($_REQUEST['action']) || !preg_match('/^\w+$/', $_REQUEST['action'])) {
	echo json_encode(array('failed'=>'undefined'));
	exit();
}

if($_REQUEST['action'] === "logout" || csrf_verifyNonce($_REQUEST['action'], $_POST['nonce']) == true){
	// The following calls the appropriate function based to the request parameter $_REQUEST['action'],
	//   (e.g. When $_REQUEST['action'] is 'cat_insert', the function ierg4210_cat_insert() is called)
	// the return values of the functions are then encoded in JSON format and used as output
	try {
		if (($returnVal = call_user_func($_REQUEST['action'])) === false) {
			if ($db && $db->errorCode())
				error_log(print_r($db->errorInfo(), true));
			echo json_encode(array('failed'=>'1'));
		}
		csrf_verifyNonce($_REQUEST['action'], $_POST['nonce']);
		echo 'while(1);' . json_encode(array('success' => $returnVal));
	} catch(PDOException $e) {
		error_log($e->getMessage());
		echo json_encode(array('failed'=>'error-db'));
	} catch(Exception $e) {
		echo 'while(1);' . json_encode(array('failed' => $e->getMessage()));
	}
}else{
	header('Location: ../../index.php');
	exit();
}

?>
