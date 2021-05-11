<?php header('X-Powered-By:'); ?>
<?php
    if( !defined( __DIR__ ) ) define( __DIR__, dirname(__FILE__) );
    require __DIR__.'/../admin/lib/db.inc.php';
    $target = htmlspecialchars($_GET["id"]);
    $res = getProductDetails($target);

    $res2 = fetchCategories();
    $all_category = array();
    foreach ($res2 as $value){
        array_push($all_category, $value["name"]);
    }


    $result = '';

    // <div class="product-img">
    //     <img src="/pic/product/coca-cola.jpg" alt="Coca Cola Image">
    // </div>
    // <div class="product-description">
    //     <h1 id="product_name">Coca Cola</h1>
    //     <h2 id="product_details">Coke (330ml)</h2>
    //     <p id="product_id">Product ID: #342813</p>
    //     <p id="product_location">Made in Hong Kong</p>
    //     <br>
    //     <p id="product_price">$5 each</p>
    //     <input type="number" name="quantity" value="1" min="1" max="50"><br><br>
    //     <button class="btn" type="submit" name="addToCart" value="Add To Cart">
    //         <i class="fa fa-shopping-cart"></i>
    //         <span>&nbsp;Add to Cart</span>
    //     </button>
    // </div>
    $prod_name = '';
    $current_link = '';
    $cat_name = '';
    $prod_link = '';
    $prod_name = '';
    
    foreach ($res as $value){
        $result .= '<div class="product-img"><img src="../pic/product/';
        $result .= $value["filename"];
        $result .= '" alt="'.$value["name"] .'"></div>';
        $result .= '<div class="product-description">';
        $result .= '<h1 id="product_name">'.$value["name"].'</h1>';
        $result .= '<h2 id="product_details">'.$value["description"].'</h2>';
        $result .= '<p id="product_id">Product ID: '.$value["product_id"].'</p>';
        $result .= '<p id="product_location">'.$value["long_description"].'</p><br>';
        $result .= '<p id="product_price">$'.$value["price"].' each</p>';
        // $result .= '<input type="number" name="quantity" value="1" min="1" max="50"><br><br>';
        $result .= '<button class="btn shopping_cart" type="submit" id="'. $value["pid"] .'" name="addToCart" value="Add To Cart">';
        $result .= '<i class="fa fa-shopping-cart"></i>';
        $result .= '<span>&nbsp;Add to Cart</span></button></div>';
        $prod_name = $value["name"];
        $prod_link = "./product_details.php?id=".$target;
        $cat_name = $all_category[$value["catid"]-1];
        $current_link = "./category.php?catid=".$value["catid"];
    }

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title><?php echo $prod_name; ?></title> <!-- 1st -->
    <link rel="stylesheet" href="/css/home.css">
    <link rel="stylesheet" href="/css/template.css">
    <link rel="stylesheet" href="/css/product.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="shortcut icon" href="/icon/favicon.ico" type="image/x-icon">
</head>

<body>
    <header></header>
    <p id="navigation-path">
        <a href="/index.php">Home</a> &gt;
        <a href="<?php echo htmlspecialchars($current_link); ?>"><?php echo htmlspecialchars($cat_name); ?></a> &gt;
        <a href="<?php echo htmlspecialchars($prod_link); ?>"><?php echo htmlspecialchars($prod_name); ?></a>
    </p>
    <main>
        <section class="product-details">
            <nav id="navigation"></nav>
            <?php echo $result; ?>
        </section>
    </main>
    <hr class="line-seperate">
    <footer></footer>
    <script type="text/javascript" src="/jquery/template.js"></script>
</body>

</html>
