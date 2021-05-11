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
    <title>Edit Category</title>
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
        <form id="cat_edit" method="POST" action="admin-process.php?action=editCategory">
            <h2>Edit Category</h2>
            <label for="cat_id">Category *</label>
            <div>
                <select id="cat_id" name="catid">
                    <?php echo $options; ?>
                </select>
            </div>
            <br>

            <label for="cat_name">Name *</label>
            <div><input id="cat_name" type="text" name="name" required="true" pattern="^[\w\- ]+$" /></div>
            <br>
            <input type="hidden" name="nonce" value="<?php echo htmlspecialchars(csrf_getNonce("editCategory")); ?>">
            <input type="submit" value="Submit" />
        </form>
    </main>
    <hr class="line-seperate">
    <footer></footer>
</body>

</html>
