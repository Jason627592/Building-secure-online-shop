<?php header('X-Powered-By:'); ?>
<?php
    if( !defined( __DIR__ ) ) define( __DIR__, dirname(__FILE__) );
    require __DIR__.'/../admin/payment/payment_db.php';
    require __DIR__.'/../admin/login/website-perm.php';
    $auth_result = auth();
    $result = "";
    if($auth_result!=false){
        $res = fetchMemberTranscation($auth_result);
        function fetchName($pid){
            global $db;
            $db = configDB();
            $q = $db->prepare('SELECT name FROM products WHERE pid = ?');
            if($q->execute(array($pid)))
                return $q->fetchAll();
        }

        foreach ($res as $value){
            $result .= "<tr>";
            $result .= "<td>".$value["createdtime"].'</td>';
            $result .= "<td>".$value["txnid"].'</td>';
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
    }
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Recent Transcations</title>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="/css/template.css">
        <link rel="stylesheet" href="/css/panel.css">
        <link rel="shortcut icon" href="/icon/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="/css/home.css">
    </head>
    <body>
        <header></header>
        <main id="main_panel">
            <p id="navigation-path">
                <a href="/index.php">Home</a>
            </p>
            <table id="user_transcation_info" class="display">
                <thead>
                    <tr>
                        <th>Transcation Time</th>
                        <th>Transcation ID</th>
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
