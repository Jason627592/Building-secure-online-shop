<?php header('X-Powered-By:'); ?>
<?php
    if( !defined( __DIR__ ) ) define( __DIR__, dirname(__FILE__) );
    require __DIR__.'/login/website-perm.php';
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
?>
<link rel="stylesheet" href="/css/panel.css">
<ul id="utility_tools">
    <li><a href="./panel.php">Show All Products</a></li>
    <li><a href="./addProduct.php">Add Product</a></li>
    <li><a href="./addCategory.php">Add Category</a></li>
    <li><a href="./editCategory.php">Edit Category</a></li>
    <li><a href="./deleteCategory.php">Delete Category</a></li>
    <li><a href="./deleteProductByCategory.php">Delete Product By Category</a></li>
    <li><a href="./showTranscations.php">Show All Transcations</a></li>
</ul>
