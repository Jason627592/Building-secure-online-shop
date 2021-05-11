<?php header('X-Powered-By:'); ?>
<?php
    if( !defined( __DIR__ ) ) define( __DIR__, dirname(__FILE__) );
    require __DIR__.'/lib/db.inc.php';
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

    $res1 = fetchAllProducts();
    $options = '';

    $res2 = fetchCategories();
    $category = array();

    foreach ($res2 as $value){
        array_push($category, $value["name"]);
    }

    $all_products = '';
    foreach ($res1 as $value){
        $all_products .= '<tr id="' . $value["pid"] .'" class="product_record">';
        $all_products .= '<th>' . $value["pid"] . '</th>';
        $all_products .= '<th>' . $category[--$value["catid"]] . '</th>';
        $all_products .= '<th>' . $value["name"] . '</th>';
        $all_products .= '<th> $' . $value["price"] . '</th>';
        $all_products .= '<th>' . $value["product_id"] . '</th>';
        $all_products .= '<th>' . $value["description"] . '</th>';
        $all_products .= '<th>' . $value["long_description"] . '</th>';
        $all_products .= '<th><img class="thumbnail" src="/pic/product/' . $value["filename"] .'" alt="' . $value["filename"] . '"></th>';
        $all_products .= "</tr>";
    }
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Product Information</title>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/jquery.dataTables.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script type="text/javascript" src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <script type="text/javascript" src="/jquery/panel.js"></script>
        <link rel="stylesheet" href="/css/template.css">
        <link rel="stylesheet" href="/css/panel.css">
        <link rel="shortcut icon" href="/icon/favicon.ico" type="image/x-icon">
    </head>
    <body>
        <header></header>
        <main id="main_panel">
            <nav id="admin_nav"></nav>
            <h5>Please click on the table item to view or update product details</h5>
            <table id="product_information" class="display">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Product ID</th>
                        <th>Description</th>
                        <th>Long Description</th>
                        <th>Thumbnail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php echo $all_products; ?>
                </tbody>
            </table>
        </main>
        <hr class="line-seperate">
        <footer></footer>
        <script type="text/javascript" src="/jquery/template.js"></script>
    </body>
</html>
