<?php header('X-Powered-By:'); ?>
<?php
    if( !defined( __DIR__ ) ) define( __DIR__, dirname(__FILE__) );
    require __DIR__.'/website-perm.php';
    require __DIR__.'/../csrf/nonce.php';
    $auth_result = auth();
    if($auth_result==false){
        header('Location: /../../login.php');
        exit();
    }

    $hidden_email_field = "<input type='hidden' name='email' value='".$auth_result."'>";

    if($_GET['err']!=""){
        $err_msg="Incorrect Current Password.<br><br>";
    }
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Change Password</title>
    </head>
    <body>
        <h1>Change Password</h1>
        <?php echo $err_msg; ?>
        <form class="" action="./login-process.php?action=change_password" method="post">
            <label for="email">Current Password</label>
            <input type="password" name="old_password" id="old_password" value="" placeholder="********" required>
            <br><br>

            <label for="password">New Password</label>
            <input type="password" name="new_password" id="new_password" value="" placeholder="********" required>
            <br><br>

            <?php echo $hidden_email_field; ?>
            <input type="hidden" name="nonce" value="<?php echo htmlspecialchars(csrf_getNonce("change_password")); ?>">
            <input type="submit" value="Change Password">
        </form>
    </body>
</html>
