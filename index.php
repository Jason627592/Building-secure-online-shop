<?php header('X-Powered-By:'); ?>
<?php
    if( !defined( __DIR__ ) ) define( __DIR__, dirname(__FILE__) );
    require __DIR__.'/admin/lib/db.inc.php';
    $res = fetchSelectedProducts();
    $result = '';

    // <li>
    //     <a href="./product/drinks-beverages/coca-cola.html">
    //         <img src="./pic/product/coca-cola.jpg" alt="Coca Cola">
    //         <p>Coca Cola</p>
    //         <p>Classic</p>
    //     </a>
    //     <p>Price: $5</p>
    //     <button class="btn" type="submit" name="addToCart" value="Add To Cart">
    //         <i class="fa fa-shopping-cart"></i>
    //         <span>&nbsp;Add to Cart</span>
    //     </button>
    // </li>

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

    foreach ($res as $value){
        $result .= '<li><a href="./product/product_details.php?id='.$value["pid"].'">';
        $result .= '<img src="./pic/product/'.$value["filename"] .'" alt="'.$value["name"].'">';
        $result .= '<p>'.$value["name"].'</p>';
        $result .= '<p>'.$value["description"].'</p></a>';
        $result .= '<p> Price: $'.$value["price"].'</p>';
        $result .= '<button class="btn shopping_cart" type="submit" id="'. $value["pid"] .'" name="addToCart" value="Add To Cart">';
        $result .= '<i class="fa fa-shopping-cart"></i>';
        $result .= '<span>&nbsp;Add to Cart</span></button>';
    }
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>PriMarket Home</title>
    <link rel="stylesheet" href="/css/home.css">
    <link rel="stylesheet" href="/css/template.css">
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
        <a href="/index.php">Home</a>
    </p>
    <main>
        <div class="welcome-message">
            <nav id="navigation"></nav>
            <div id="promotion_banner">
                <img src="./pic/promotion_banner/picture1.jpg" alt="Promotional Banner">
            </div>
        </div>

        <div class="product">
            <h2>Selected Products</h2>
            <ul class="table">
                <?php echo $result; ?>
            </ul>
        </div>
    </main>
    <hr class="line-seperate">
    <footer></footer>
    <script type="text/javascript" src="/jquery/template.js"></script>
</body>

</html>
