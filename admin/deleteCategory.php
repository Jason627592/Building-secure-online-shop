<?php header('X-Powered-By:'); ?>
<?php
    if( !defined( __DIR__ ) ) define( __DIR__, dirname(__FILE__) );
    require __DIR__.'/lib/db.inc.php';
    require __DIR__.'/login/website-perm.php';
    require __DIR__.'/csrf/nonce.php';

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

    $res = fetchCategories();
    $options = '';

    foreach ($res as $value){
        $options .= '<option value="'.$value["catid"].'"> '.$value["name"].' </option>';
    }
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>Delete Category</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/css/template.css">
    <link rel="shortcut icon" href="/icon/favicon.ico" type="image/x-icon">
    <script type="text/javascript" src="/jquery/template.js"></script>
</head>

<body>
    <header></header>
    <main>
        <nav id="admin_nav"></nav>
        <form id="cat_delete" method="POST" action="admin-process.php?action=deleteCategory">
            <h2>Delete Category</h2>
            <label for="prod_catid">Category *</label>
            <div>
                <select id="prod_catid" name="del_catid">
                    <?php echo $options; ?>
                </select>
            </div>
            <br>
            <input type="hidden" name="nonce" value="<?php echo htmlspecialchars(csrf_getNonce("deleteCategory")); ?>">
            <input type="submit" value="Submit" />
        </form>
    </main>
    <hr class="line-seperate">
    <footer></footer>
</body>

</html>
