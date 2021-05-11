<?php header('X-Powered-By:'); ?>
<?php
    if( !defined( __DIR__ ) ) define( __DIR__, dirname(__FILE__) );
    require __DIR__.'/admin/login/website-perm.php';
    require __DIR__.'/admin/csrf/nonce.php';

    $auth_result = auth();
    if($auth_result!=false){
        if(determine_admin($auth_result)){
            header('Location: /admin/panel.php');
        }else{
            header('Location: index.php');
        }
        exit();
    }

    $error_msg = '';
    if(isset($_GET['err']) && $_GET['err']!=""){
        $error_msg = "Invalid email/password. Please try again.<br><br>";
    }

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Login Page</title>
        <link rel="stylesheet" href="/css/template.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    </head>
    <body>
        <header></header>
        <main>
            <h1>Login Page</h1>
            <?php echo $error_msg; ?>
            <form class="" action="./admin/login/login-process.php?action=login" method="post">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="" placeholder="email" required>
                <br><br>

                <label for="password">Password</label>
                <input type="password" name="password" id="password" value="" placeholder="********" required>
                <br><br>

                <input type="hidden" name="nonce" value="<?php echo htmlspecialchars(csrf_getNonce("login")); ?>">
                <input type="submit" value="Login">
            </form>
        </main>
        <hr class="line-seperate">
        <footer></footer>
        <script type="text/javascript" src="/jquery/template.js"></script>
    </body>
</html>
