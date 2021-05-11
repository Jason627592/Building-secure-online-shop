<?php header('X-Powered-By:'); ?>
<?php
require('functions.php');

// For test payments we want to enable the sandbox mode. If you want to put live
// payments through then this setting needs changing to `false`.
$enableSandbox = true;

// PayPal settings. Change these to your account details and the relevant URLs
// for your site.
$paypalConfig = [
    //'email' => 'user@example.com',
    'return_url' => 'https://secure.s61.ierg4210.ie.cuhk.edu.hk/payment_status_page/payment_success.html',
    'cancel_url' => 'https://secure.s61.ierg4210.ie.cuhk.edu.hk/payment_status_page/payment-cancelled.html',
    'notify_url' => 'https://secure.s61.ierg4210.ie.cuhk.edu.hk/admin/ipn/payments.php'
];

$paypalUrl = $enableSandbox ? 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr' : 'https://ipnpb.paypal.com/cgi-bin/webscr';
error_log(print_r($_POST, true));

// Check if paypal request or response

if (!isset($_POST["txn_id"]) && !isset($_POST["txn_type"])) {

    // Grab the post data so that we can set up the query string for PayPal.
    // Ideally we'd use a whitelist here to check nothing is being injected into
    // our post data.
    $data = [];
    foreach ($_POST as $key => $value) {
        $data[$key] = stripslashes($value);
    }

    // Set the PayPal account.
    // $data['business'] = stripslashes($paypalConfig['email']);

    // Set the PayPal return addresses.
    $data['return'] = stripslashes($paypalConfig['return_url']);
    $data['cancel_return'] = stripslashes($paypalConfig['cancel_url']);
    $data['notify_url'] = stripslashes($paypalConfig['notify_url']);

    // Set the details about the product being purchased, including the amount and currency so that these aren't overridden by the form data.
    //$data['item_name'] = $itemName;
    //$data['amount'] = $itemAmount;
    //$data['currency_code'] = 'GBP';

    // Add any custom fields for the query string.
    //$data['custom'] = USERID;

    // Build the query string from the data.
    $queryString = http_build_query($data);

    // Redirect to paypal IPN
    header('location:' . $paypalUrl . '?' . $queryString);
    exit();

} else {
    // Handle the PayPal response.

    // Create a connection to the database.
    $db = configDB();

    // Assign posted variables to local data array.
    $data = [
        'payment_status' => $_POST['payment_status'],
        'payment_amount' => $_POST['mc_gross'],
        'payment_currency' => $_POST['mc_currency'],
        'txn_id' => $_POST['txn_id'],
        'receiver_email' => $_POST['receiver_email'],
        'custom' => $_POST['custom'],
        'invoice' => $_POST['invoice']
    ];

    for($k = 1; $k<1000; $k++){
        if(array_key_exists("item_name".$k, $_POST) == true){
            $data['item_name_'.$k] = $_POST['item_name'.$k];
            $data['quantity_'.$k] = $_POST['quantity'.$k];
        }else{
            break;
        }
    }
    error_log(print_r($data, true));
    // We need to verify the transaction comes from PayPal and check we've not
    // already processed the transaction before adding the payment to our
    // database.
    if (verifyTransaction($_POST) && checkTxnid($data['txn_id']) && isset($_POST["txn_type"]) && $_POST["txn_type"]=="cart") {
        if (addPayment($data) !== false) {
            // Payment successfully added into db.
            header('location:' . $paypalConfig['return_url']);
            exit();
        }
    }else{
        //Payment failed
        header('location:' . $paypalConfig['cancel_url']);
        exit();
    }
}
?>
