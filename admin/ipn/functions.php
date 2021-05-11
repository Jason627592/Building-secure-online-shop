<?php header('X-Powered-By:'); ?>
<?php
function configDB() {
	$db = new PDO('sqlite:/var/www/cart.db');
	$db->query('PRAGMA foreign_keys = ON;');
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	return $db;
}


function checkTxnid($txnid) {
    //TO BE IMPLEMENTED - check whether we've not already processed the transaction before
    //Sample code from the reference

    $db = configDB();
    $q = $db->prepare('SELECT * FROM payments WHERE txnid = ?');
    $result = $q->execute(array($txnid));
    return !$q->rowCount();
}

function addPayment($data) {
    //TO BE IMPLEMENTED - adding payment record into db
    //Sample code from the reference
    $db = configDB();
	$q2 = $db->prepare('SELECT pid FROM products WHERE name = ?');
    if (is_array($data)) {
		// TODO: reconstruct the hash using the same algorithm
        $q = $db->prepare('UPDATE payment_info SET txnid = ?, payment_status = ? WHERE payment_amount = ? AND order_hash = ? AND order_id = ?');
		$q3 = $db->prepare('SELECT order_salt FROM payment_info WHERE payment_amount = ? AND itemid = ? AND order_id = ?');
		$temp_cart_content = '';
		$salt = '';
		for($k = 1; $k<1000; $k++){
			if(array_key_exists("item_name_".$k, $data) == true){
				$res = $q2->execute(array($data["item_name_".$k]));
				$temp = $q2->fetchAll();
				//error_log(print_r($temp, true));
				if(count($temp)==0){
					$temp_cart_content.= 999;
				}else{
					$temp_cart_content .= $temp[0]["pid"];
				}
				$temp_cart_content .= ",";
				$temp_cart_content .= $data["quantity_".$k];
				// $temp_cart_content .= '1';
				$temp_cart_content .= "@";
			}else{
				break;
			}
		}
		// $temp_cart_content = "123";
		error_log(print_r($temp_cart_content, true));
		$return_salt = '0';
		//CAST($data['payment_amount'] AS DECIMAL(10,1))
		if($q3->execute(array($data['payment_amount'], $temp_cart_content, $data['invoice']))){
			$return_salt = $q3->fetchAll();
			// error_log(print_r($return_salt, true));
			if(count($return_salt)==0){
				$return_salt = '0';
			}else{
				$return_salt = $return_salt[count($return_salt)-1]["order_salt"];
			}
		}
		error_log(print_r($return_salt, true));
		//$digest = hash("sha256",$currency."|".$email."|".$salt.$shopping_cart_content."|".$total_price, false);
		$recalculate_hash = hash("sha256",$data['payment_currency']."|".$data['receiver_email']."|".$return_salt.$temp_cart_content."|".$data['payment_amount'], false);
		error_log(print_r($recalculate_hash, true));
		// $date = new DateTime("now", new DateTimeZone('Asia/Hong_Kong') );
        // $current_time = $date->format('Y-m-d H:i:s');
        if($q->execute(array($data['txn_id'], $data['payment_status'], $data['payment_amount'], $recalculate_hash, $data['invoice'])))
        	return $q->rowCount();
    }

    return false;
}


function verifyTransaction($data) {
    global $paypalUrl;

    $req = 'cmd=_notify-validate';
    foreach ($data as $key => $value) {
        $value = urlencode(stripslashes($value));
        $value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i', '${1}%0D%0A${3}', $value); // IPN fix
        $req .= "&$key=$value";
    }

    $ch = curl_init($paypalUrl);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
    curl_setopt($ch, CURLOPT_SSLVERSION, 6);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
    $res = curl_exec($ch);

    if (!$res) {
        $errno = curl_errno($ch);
        $errstr = curl_error($ch);
        curl_close($ch);
        throw new Exception("cURL error: [$errno] $errstr");
    }

    $info = curl_getinfo($ch);

    // Check the http response
    $httpCode = $info['http_code'];
    if ($httpCode != 200) {
        throw new Exception("PayPal responded with http code $httpCode");
    }

    curl_close($ch);

    return $res === 'VERIFIED';
}
?>
