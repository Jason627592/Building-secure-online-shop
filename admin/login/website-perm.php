<?php header('X-Powered-By:'); ?>
<?php
function auth(){
    if(!empty($_SESSION['auth']))
        return $_SESSION['auth']['em'];
    if(!empty($_COOKIE['auth'])){
        if($t = json_decode(stripslashes($_COOKIE['auth']), true)){
            if(time() > $t['exp'])
                return false;

            $db = new PDO('sqlite:/var/www/cart.db');
        	$db->query('PRAGMA foreign_keys = ON;');
        	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            $q = $db->prepare('SELECT * FROM users WHERE email = ?');
            $q->execute(array($t['em']));
            if($r=$q->fetch()){
                $realk = hash_hmac('sha256', $t['exp'].$r['password'], $r['salt']);
                if($realk == $t['k']){
                    $_SESSION['auth'] = $t;
                    return $t['em'];
                }
            }
        }
    }
    return false;
}

function determine_admin($user_email){
    $db = new PDO('sqlite:/var/www/cart.db');
    $db->query('PRAGMA foreign_keys = ON;');
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $user_email = filter_var($user_email, FILTER_SANITIZE_STRING);
    if(filter_var($user_email, FILTER_VALIDATE_EMAIL)){
        $q = $db->prepare('SELECT * FROM users WHERE email = ?');
        $q->execute(array($user_email));
        if($r=$q->fetch()){
            if($r['admin_flag']=="1"){
                return true;
            }else{
                return false;
            }
        }
    }
    return false;
}
?>
