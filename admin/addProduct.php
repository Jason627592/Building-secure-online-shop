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
    <title>Add Product</title>
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
        <form id="prod_insert" method="POST" action="admin-process.php?action=addProduct" enctype="multipart/form-data">
            <h2>New Products</h2>
            <label for="prod_catid">Category *</label>
            <div>
                <select id="prod_catid" name="catid">
                    <?php echo $options; ?>
                </select>
            </div>
            <br>

            <label for="prod_name">Name *</label>
            <div><input id="prod_name" type="text" name="name" required="true" pattern="^[\w\- ]+$" /></div>
            <br>

            <label for="prod_price">Price *</label>
            <div><input id="prod_price" type="number" name="price" step="0.1" required="true" pattern="^[\w\-. ]+$" /></div>
            <br>

            <label for="prod_id">Short Description *</label>
            <div><input id="prod_id" type="text" name="description" required="true" pattern="^[\w\-()-. ]+$" /></div>
            <br>

            <label for="prod_name">Long Description *</label>
            <div><textarea id="prod_description" type="text" name="long_description" required="true" pattern="^[\w\-()\n-. ]+$" rows="4" cols="80"/></textarea></div>
            <br>

            <label for="prod_name">Image *</label>
            <div><input type="file" name="file" required="true" accept="image/*" /></div>
            <br>

            <input type="hidden" name="nonce" value="<?php echo htmlspecialchars(csrf_getNonce("addProduct")); ?>">
            <input type="submit" value="Submit" />
        </form>
    </main>
    <hr class="line-seperate">
    <footer></footer>
</body>

</html>
