<?php header('X-Powered-By:'); ?>
<?php
    if( !defined( __DIR__ ) ) define( __DIR__, dirname(__FILE__) );
    require __DIR__.'/../admin/lib/db.inc.php';
    $target = htmlspecialchars($_GET["catid"]);
    $res = fetchAllProductsByCategory($target);

    $res2 = fetchCategories();
    $all_category = array();
    foreach ($res2 as $value){
        array_push($all_category, $value["name"]);
    }

    $cat_name = $all_category[--$target];
    $result = '';

    foreach ($res as $value){
        $result .= "<li>";
        $result .= '<a href="./product_details.php?id='.$value["pid"].'">';
        $result .= '<img class="category_img" src="../pic/product/'. $value["filename"] .'" alt="'. $value["name"] .'">';
        $result .= '<p>'. $value["name"] .'</p>';
        $result .= '<p>'. $value["description"] .'</p></a>';
        $result .= '<p>Price: $'. $value["price"] .'</p>';
        $result .= '<button class="btn shopping_cart" type="submit" id="'. $value["pid"] .'" name="addToCart" value="Add To Cart">';
        $result .= '<i class="fa fa-shopping-cart"></i>';
        $result .= '<span>&nbsp;Add to Cart</span></button>';
        $result .= "</li>";
    }

    $current_link = "./category.php?catid=".++$target;

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title><?php echo $cat_name; ?></title>
        <link rel="stylesheet" href="/css/home.css">
        <link rel="stylesheet" href="/css/template.css">
        <link rel="stylesheet" href="/css/category.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="shortcut icon" href="/icon/favicon.ico" type="image/x-icon">
        <script type="text/javascript" src="/jquery/template.js"></script>
    </head>
    <body>
        <header></header>
        <p id="navigation-path">
            <a href="/index.php">Home</a> &gt;
            <a href="<?php echo htmlspecialchars($current_link); ?>"><?php echo htmlspecialchars($cat_name); ?></a>
        </p>
        <main class="category_list">
            <nav id="navigation"></nav>
            <div>
                <h1 class="category_title"><?php echo htmlspecialchars($cat_name); ?></h1>
                <section class="product">
                    <ul class="table">
                        <?php echo $result; ?>
                    </ul>
                </section>
            </div>
        </main>
        <hr class="line-seperate">
        <footer></footer>
    </body>
</html>
