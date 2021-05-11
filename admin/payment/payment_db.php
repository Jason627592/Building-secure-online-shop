<?php header('X-Powered-By:'); ?>
<?php
function configDB() {
	$db = new PDO('sqlite:/var/www/cart.db');
	$db->query('PRAGMA foreign_keys = ON;');
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	return $db;
}

function fetchMemberTranscation($email){
    global $db;
    $db = configDB();
    $q = $db->prepare("SELECT txnid, payment_amount, payment_status, itemid, createdtime FROM payment_info WHERE email = ? AND payment_status = 'Completed' ORDER BY order_id DESC LIMIT 5;");
    if ($q->execute(array($email)))
        return $q->fetchAll();
}

?>
