<?php header('X-Powered-By:'); ?>
<?php
    if( !defined( __DIR__ ) ) define( __DIR__, dirname(__FILE__) );
    require __DIR__.'/lib/db.inc.php';
    require __DIR__.'/login/website-perm.php';
    // require __DIR__.'/payment/payment_db.php';

    $auth_result = auth();
    if($auth_result!=false){
        $admin_result = determine_admin($auth_result);
    }else{
        $admin_result = false;
    }

    if($auth_result==false || $admin_result==false){
        if($admin_result==false && $auth_result==true){
            header('Location: ../index.php');
        }else{
            header('Location: ../login.php');
        }
        exit();
    }

    function fetchAllTranscation() {
        // DB manipulation
        global $db;
        $db = configDB();
        $q = $db->prepare("SELECT txnid, payment_amount, payment_status, itemid, createdtime, email FROM payment_info WHERE payment_status = 'Completed' ORDER BY order_id DESC LIMIT 100;");
        if ($q->execute())
            return $q->fetchAll();
    }

    function fetchName($pid){
        global $db;
        $db = configDB();
        $q = $db->prepare('SELECT name FROM products WHERE pid = ?');
        if($q->execute(array($pid)))
            return $q->fetchAll();
    }

    $res = fetchAllTranscation();
    $test = '';
    $result = '';
    foreach ($res as $value){
        $result .= "<tr>";
        $result .= "<td>".$value["createdtime"].'</td>';
        $result .= "<td>".$value["txnid"].'</td>';
        $result .= "<td>".$value["email"].'</td>';
        $step1 = explode('@', $value["itemid"]);
        $product_list = '';
        foreach($step1 as $product){
            if($product == ""){
                break;
            }
            $step2 = explode (",", $product);
            // $test .= print_r($product);
            // $test .=" ";
            // print_r($step2[0]);
            $return_val = fetchName($step2[0]);
            $product_list .= $return_val[0]["name"]." * ".$step2[1]."<br>";
        }
        $result .= "<td>".$product_list.'</td>';
        $result .= "<td>".$value["payment_amount"].'</td>';
        $result .= "<td>".$value["payment_status"].'</td>';
        $result .= "</tr>";
    }
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Show all transactions</title>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <script type="text/javascript" src="/jquery/panel.js"></script>
        <link rel="stylesheet" href="/css/template.css">
        <link rel="stylesheet" href="/css/panel.css">
        <link rel="shortcut icon" href="/icon/favicon.ico" type="image/x-icon">
    </head>
    <body>
        <header></header>
        <main id="main_panel">
            <nav id="admin_nav"></nav>
            <h5>Show All Transcations</h5>
            <table id="product_information" class="display">
                <thead>
                    <tr>
                        <th>Transcation Time</th>
                        <th>Transcation ID</th>
                        <th>Email</th>
                        <th>Purchased Item</th>
                        <th>Total</th>
                        <th>Payment Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php echo $result; ?>
                </tbody>
            </table>
        </main>
        <hr class="line-seperate">
        <footer></footer>
        <script type="text/javascript" src="/jquery/template.js"></script>
    </body>
</html>
