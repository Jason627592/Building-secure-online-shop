<?php header('X-Powered-By:'); ?>
<?php
if( !defined( __DIR__ ) ) define( __DIR__, dirname(__FILE__) );
require __DIR__.'/../admin/lib/db.inc.php';
$res = fetchCategories();
$options = '';

foreach ($res as $value){
    $options .= '<li><a href="../product/category.php?catid='. $value["catid"] .'">' . $value["name"] . '</a></li>';
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title></title>
    </head>
    <body>
        <link rel="stylesheet" href="/css/navigation.css">
        <ul id="home-navigation">
            <?php echo $options; ?>
        </ul>
    </body>
</html>
