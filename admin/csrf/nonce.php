<?php header('X-Powered-By:'); ?>
<?php
session_start();

function csrf_getNonce($action){
    $nonce = mt_rand() . mt_rand();

    if(!isset($_SESSION['csrf_nonce'])){
        $_SESSION['csrf_nonce'] = array();
    }

    $_SESSION['csrf_nonce'][$action] = $nonce;
    return $nonce;
}

function csrf_verifyNonce($action, $recievedNonce){
    if(isset($_SESSION['csrf_nonce'][$action]) && $_SESSION['csrf_nonce'][$action] == $recievedNonce){
        if(!isset($_SESSION['auth']) || $_SESSION['auth']==null)
            unset($_SESSION['csrf_nonce'][$action]);
        return true;
    }
    // throw new Exception('csrf attack');
    return false;
}
?>
