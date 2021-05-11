<?php header('X-Powered-By:'); ?>
<?php
function configDB_Login() {
    $db = new PDO('sqlite:/var/www/cart.db');
    $db->query('PRAGMA foreign_keys = ON;');
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $db;
}

function login(){
    if (isset($_COOKIE['auth'])) {
        setcookie("auth", "", time() - 3600, '/', 'secure.s61.ierg4210.ie.cuhk.edu.hk', true, true);
        unset($_COOKIE['auth']);
    }
    $login_success = false;
    global $db;
    $db = configDB_Login();

    $_POST['email'] = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $_POST['password'] = filter_var($_POST['password'], FILTER_SANITIZE_STRING);

    if(empty($_POST['email']) || empty($_POST['password'])
    || !preg_match("/^[\w=+\-\/][\w='+\-\/\.]*@[\w\-]+(\.[\w\-]+)*(\.[\w]{2,6})$/", $_POST['email'])
    || !preg_match("/^[\w@#$%\^\&\*\-\[\]\!\?\>\.]+$/", $_POST['password'])){
        throw new Exception('Invalid Username/Password');
    }

    $salt = "";
    $q = $db->prepare("SELECT salt FROM users WHERE email=? LIMIT 1;");
    if ($q->execute(array($_POST['email']))){
        $salt = ($q->fetchAll())[0]["salt"];
    }

    $hashed_password = hash_hmac('sha256', $_POST['password'], $salt);
    $admin_flag = -1;

    $q = $db->prepare("SELECT COUNT(*) FROM users WHERE email=? AND password=?;");
    if ($q->execute(array($_POST['email'], $hashed_password))){
        $login_success = $q->fetchAll()[0]["COUNT(*)"];
    }

    $q = $db->prepare("SELECT admin_flag FROM users WHERE email=? AND password=? LIMIT 1;");
    if ($q->execute(array($_POST['email'], $hashed_password))){
        $admin_flag = $q->fetchAll()[0]["admin_flag"];
    }

    if($login_success == 1){
        $exp = time() + 3600 * 24 * 3;
        $token = array(
            'em'=>$_POST['email'],
            'exp'=>$exp,
            'k'=>hash_hmac('sha256', $exp.$hashed_password, $salt)
        );

        setcookie('auth', json_encode($token), $exp, '/', 'secure.s61.ierg4210.ie.cuhk.edu.hk', true, true);
        $_SESSION['auth'] = $token;
        session_regenerate_id();

        if($admin_flag == 0){
            header('Location: ../../index.php', true, 302);
            exit();
        }else if($admin_flag == 1){
            header('Location: /admin/panel.php', true, 302);
            exit();
        }else{
            throw new Exception('Database errors');
        }
    }else{
        header('Location: ../../login.php?err=1', true, 302);
        throw new Exception('Wrong Credentials');
    }
}

function logout(){
    setcookie("auth", "", time() - 3600, '/', 'secure.s61.ierg4210.ie.cuhk.edu.hk', true, true);
    unset($_COOKIE['auth']);
    session_destroy();
    header('Location: ../../index.php', true, 302);
    exit();
}

function change_password(){
    $db = configDB_Login();

    $_POST['email'] = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $_POST['old_password'] = filter_var($_POST['old_password'], FILTER_SANITIZE_STRING);
    $_POST['new_password'] = filter_var($_POST['new_password'], FILTER_SANITIZE_STRING);

    echo $_POST['email'];
    echo "\n";
    echo $_POST['old_password'];
    echo "\n";
    echo $_POST['new_password'];
    echo "\n";

    if(empty($_POST['old_password']) || empty($_POST['new_password']) || empty($_POST['email'])
    || !preg_match("/^[\w=+\-\/][\w='+\-\/\.]*@[\w\-]+(\.[\w\-]+)*(\.[\w]{2,6})$/", $_POST['email'])
    || !preg_match("/^[\w@#$%\^\&\*\-\[\]\!\?\>\.]+$/", $_POST['old_password'])
    || !preg_match("/^[\w@#$%\^\&\*\-\[\]\!\?\>\.]+$/", $_POST['new_password'])){

        throw new Exception('Invalid Password');
    }

    $salt = "";
    $q = $db->prepare("SELECT salt FROM users WHERE email=? LIMIT 1;");
    if ($q->execute(array($_POST['email']))){
        $salt = ($q->fetchAll())[0]["salt"];
    }
    echo $salt;
    echo "\n";

    $hashed_old_password = hash_hmac('sha256', $_POST['old_password'], $salt);
    $hashed_new_password = hash_hmac('sha256', $_POST['new_password'], $salt);
    echo $hashed_old_password;
    echo "\n";
    echo $hashed_new_password;
    echo "\n";

    $q = $db->prepare("SELECT COUNT(*) FROM users WHERE email=? AND password=?;");
    if ($q->execute(array($_POST['email'], $hashed_old_password))){
        $correct = $q->fetchAll()[0]["COUNT(*)"];
    }

    if($correct == 1){
        $q = $db->prepare("UPDATE users SET password = ? WHERE email = ? AND password = ?");
        $q->execute(array($hashed_new_password, $_POST['email'], $hashed_old_password));

        logout();
    }
    header('Location: change_password.php?err=1', true, 302);
    exit();
}

?>
