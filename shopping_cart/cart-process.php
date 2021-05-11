<?php header('X-Powered-By:'); ?>
<?php
    if( !defined( __DIR__ ) ) define( __DIR__, dirname(__FILE__) );
    require __DIR__.'/../admin/login/website-perm.php';

    function configDB() {
    	$db = new PDO('sqlite:/var/www/cart.db');
    	$db->query('PRAGMA foreign_keys = ON;');
    	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    	return $db;
    }

    try {
        if (($returnVal = call_user_func($_REQUEST['action'])) === false) {
            if ($db && $db->errorCode())
                error_log(print_r($db->errorInfo(), true));
            echo json_encode(array('failed'=>'1'));
        }
        echo $returnVal;
    } catch(PDOException $e) {
        error_log($e->getMessage());
        echo json_encode(array('failed'=>'error-db'));
    } catch(Exception $e) {
        echo 'while(1);' . json_encode(array('failed' => $e->getMessage()));
    }

    function genDigest(){
        // error_log(print_r($_POST,true));
        // error_log(print_r($_POST,true), 1, 'tgb5473@gmail.com', "From: test@s61.ierg4210.ie.cuhk.edu.hk");
        global $db;
        $db = configDB();
        $salt = mt_rand().mt_rand();
        $digest = "123";
        $shopping_cart_content = "";
        $cart_content = json_decode($_POST["cart"]);
        $cart_value = json_decode($_POST["quantity"]);
        $counter = 0;
        $q = $db->prepare("SELECT name, price from products WHERE pid=?");
        $total_price = 0;
        $name_and_price = array();
        foreach($cart_value as $quantity) {
            if($quantity<=0){
                throw new Exception("Error Processing Request", 1);
                exit();
            }
        }
        foreach($cart_content as $cart) {
            if($q->execute(array($cart)))
                $res = $q->fetchAll();
            array_push($name_and_price, $res);
            $total_price+=$res[0]["price"]*$cart_value[$counter];
            $shopping_cart_content.= (string) $cart_content[$counter];
            $shopping_cart_content.= ",";
            $shopping_cart_content.= (string) $cart_value[$counter];
            $shopping_cart_content.= "@";
            $counter++;
        }
        $currency = "HKD";
        $email = "sb-tp6df5972547@business.example.com";
        $total_price = number_format($total_price, 2, '.', '');
        $digest = hash("sha256",$currency."|".$email."|".$salt.$shopping_cart_content."|".$total_price, false);
        $test = array($currency, $email, $salt, $shopping_cart_content, $total_price);
        $q = $db->prepare('INSERT INTO payment_info (email, itemid, order_hash, order_salt, payment_status, payment_amount, createdtime) VALUES (?, ?, ?, ?, ?, ?, ?);');
        $auth_result = auth();
        if($auth_result == false){
            $user_identity = "GUEST";
        }else{
            $user_identity = $auth_result;
        }
        $date = new DateTime("now", new DateTimeZone('Asia/Hong_Kong') );
        $current_time = $date->format('Y-m-d H:i:s');
        $q->execute(array($user_identity, $shopping_cart_content, $digest, $salt, "PENDING", $total_price, $current_time));
        $invoice = $db->lastInsertId();
        $returnval = json_encode(array("digest"=>$digest, "invoice"=>$invoice, "name_and_price" => $name_and_price, "total_price" => $total_price, "test" => json_encode($test)));
        return $returnval;
    }
?>
