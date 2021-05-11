<?php header('X-Powered-By:'); ?>
<?php
function configDB() {
	// connect to the database
	// TODO: change the following path if needed
	// Warning: NEVER put your db in a publicly accessible location
	if ($_SERVER["REQUEST_METHOD"]==="POST"){
		//echo "OK"."<br>";
	}

	$db = new PDO('sqlite:/var/www/cart.db');

	// enable foreign key support
	$db->query('PRAGMA foreign_keys = ON;');

	// FETCH_ASSOC:
	// Specifies that the fetch method shall return each row as an
	// array indexed by column name as returned in the corresponding
	// result set. If the result set contains multiple columns with
	// the same name, PDO::FETCH_ASSOC returns only a single value
	// per column name.
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

	return $db;
}

function fetchCategories() {
    // DB manipulation
    global $db;
    $db = configDB();
    $q = $db->prepare("SELECT * FROM categories LIMIT 100;");
    if ($q->execute())
        return $q->fetchAll();
}

// Since this form will take file upload, we use the tranditional (simpler) rather than AJAX form submission.
// Therefore, after handling the request (DB insert and file copy), this function then redirects back to admin.html
function addProduct() {
    // input validation or sanitization

    // DB manipulation
    global $db;
    $db = configDB();

	$_POST['catid'] = filter_var($_POST['catid'], FILTER_SANITIZE_NUMBER_INT);
	$_POST['name'] = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
	$_POST['price'] = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	$_POST['description'] = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
	$_POST['long_description'] = filter_var($_POST['long_description'], FILTER_SANITIZE_STRING);

    // TODO: complete the rest of the INSERT command if needed
	if (!preg_match('/^\d*$/', $_POST['catid']))
		throw new Exception("invalid-catid");
	$_POST['catid'] = (int) $_POST['catid'];
	if (!preg_match('/^[\w\- ]+$/', $_POST['name']))
		throw new Exception("invalid-name");
	if (!preg_match('/^[\d\.]+$/', $_POST['price']))
		throw new Exception("invalid-price");
	if (!preg_match('/^[\w\-(). ]+$/', $_POST['description']))
		throw new Exception("invalid-text");
	if (!preg_match('/^[\w\-().\n- ]+$/', $_POST['long_description']))
		throw new Exception("invalid-long-description");

    $q = $db->prepare('INSERT INTO products (catid, name, price, product_id, description, long_description) VALUES (?,?,?,?,?,?)');

    // Copy the uploaded file to a folder which can be publicly accessible at incl/img/[pid].jpg
    if ($_FILES["file"]["error"] == 0
        && ($_FILES["file"]["type"] == "image/jpeg" || $_FILES["file"]["type"] == "image/gif" || $_FILES["file"]["type"] == "image/png")
        && (mime_content_type($_FILES["file"]["tmp_name"]) == "image/jpeg" || mime_content_type($_FILES["file"]["tmp_name"]) == "image/gif" || mime_content_type($_FILES["file"]["tmp_name"]) == "image/png")
        && $_FILES["file"]["size"] < 10000000) {

		$category = $_POST["catid"];
        $name = $_POST["name"];
        $price = $_POST["price"];
        $id = rand(100000,999999);
        $description = $_POST["description"];
		$long_description = $_POST["long_description"];
		$q = $db->prepare('INSERT INTO products (catid, name, price, product_id, description, long_description) VALUES (?,?,?,?,?,?)');
        $q->execute(array($category,$name,$price,$id,$description,$long_description));

		if($_FILES["file"]["type"] == "image/gif"){
			$filename = $db -> lastInsertId() . ".gif";
		}else if($_FILES["file"]["type"] == "image/png"){
			$filename = $db -> lastInsertId() . ".png";
		}else{
			$filename = $db -> lastInsertId() . ".jpg";
		}

        $q = $db->prepare('UPDATE products SET filename = ? Where pid=?;');
        $q->execute(array($filename, $db -> lastInsertId()));

		$destination = $_SERVER['DOCUMENT_ROOT'] . "/pic/product/" . $filename;
        // Note: Take care of the permission of destination folder (hints: current user is apache)
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $destination)) {
            // redirect back to original page; you may comment it during debug
			header('Location: addProduct.php');
			exit();
        }
    }
    // Only an invalid file will result in the execution below
    // To replace the content-type header which was json and output an error message
    header('Content-Type: text/html; charset=utf-8');
    echo 'Invalid file detected. <br/><a href="javascript:history.back();">Back to admin panel.</a>';
    exit();
}

function insertCategory() {
    // TODO: complete the code of this function to handle category insert
	global $db;
    $db = configDB();
	$_POST['insert_category'] = filter_var($_POST['insert_category'], FILTER_SANITIZE_STRING);
	if (!preg_match('/^[\w\-(). ]+$/', $_POST['insert_category']))
		throw new Exception("invalid-category-name");
	$insert_category = $_POST["insert_category"];
	$q = $db->prepare('INSERT INTO categories VALUES (null, ?);');
	echo $insert_category;
	$q->execute(array($insert_category));
	header('Location: addCategory.php');
	exit();
}

function editCategory(){
    // TODO: complete the code of this function to handle category edit
	global $db;
    $db = configDB();

	$_POST['catid'] = filter_var($_POST['catid'], FILTER_SANITIZE_NUMBER_INT);
	$_POST['name'] = filter_var($_POST['name'], FILTER_SANITIZE_STRING);

	if (!preg_match('/^\d*$/', $_POST['catid']))
		throw new Exception("invalid-catid");
	$_POST['catid'] = (int) $_POST['catid'];
	if (!preg_match('/^[\w\- ]+$/', $_POST['name']))
		throw new Exception("invalid-name");

	$original_category = $_POST['catid'];
	$new_category = $_POST['name'];
	$q = $db->prepare('UPDATE categories SET name = ? WHERE catid = ?;');
	$q->execute(array($new_category,$original_category));
	header('Location: editCategory.php');
	exit();
}

function deleteCategory(){
    // TODO: complete the code of this function to handle category deletion
	global $db;
    $db = configDB();

	$_POST['del_catid'] = filter_var($_POST['del_catid'], FILTER_SANITIZE_NUMBER_INT);

	if (!preg_match('/^\d*$/', $_POST['del_catid']))
		throw new Exception("invalid-del_catid");
	$_POST['del_catid'] = (int) $_POST['del_catid'];
	$delete_category = $_POST['del_catid'];
	echo $delete_category;
	$q = $db->prepare('DELETE FROM categories WHERE catid = ?;');
	deleteAllProductByCategoryLogic($delete_category);
	if($q->execute(array($delete_category)))
	// 	echo "TRUE";
	// else
	// 	print_r($q->errorInfo());

	header('Location: deleteCategory.php');
	exit();
}

function deleteAllProductByCategory(){
    // TODO: complete the code of this function to handle products deletion
	$_POST['delete_category'] = filter_var($_POST['delete_category'], FILTER_SANITIZE_NUMBER_INT);

	if (!preg_match('/^\d*$/', $_POST['delete_category']))
		throw new Exception("invalid-del_catid");

	$delete_category = $_POST['delete_category'];
	deleteAllProductByCategoryLogic($delete_category);
	header('Location: deleteProductByCategory.php');
	exit();
}

function deleteAllProductByCategoryLogic($catid){
	global $db;
    $db = configDB();
	$delete_category = $catid;
	foreach($_POST as $key=>$value)
	{
	  echo "$key=$value";
	}
	$q = $db->prepare('DELETE FROM products WHERE catid = ?;');
	$q->execute(array($delete_category));
}

function fetchAllProducts(){
    // TODO: complete the code of this function to fetch all products from the database
	global $db;
    $db = configDB();
	$q = $db->prepare('SELECT * FROM products;');
	if ($q->execute())
        return $q->fetchAll();
}

function fetchAllProductsByCategory($catid){
	global $db;
    $db = configDB();
	$category = $catid;
	$q = $db->prepare('SELECT * FROM products WHERE catid=?;');
	if ($q->execute(array($catid)))
        return $q->fetchAll();
}

function fetchSelectedProducts(){
	global $db;
    $db = configDB();
	$q = $db->prepare('SELECT * FROM products LIMIT 7;');
	if ($q->execute())
        return $q->fetchAll();
}

function getProductDetails($id){
    // TODO: complete the code of this function to fetch one specific product from the database
	global $db;
    $db = configDB();
	$chosen_product = $id;
	$q = $db->prepare('SELECT * FROM products WHERE pid = ?;');
	if($q->execute(array($chosen_product))){
		return $q->fetchAll();
	}
}
function editProductDetails(){
    // TODO: complete the code of this function to handle product information edit
	// input validation or sanitization

    // DB manipulation
    global $db;
    $db = configDB();

	$_POST['catid'] = (int) $_POST['catid'];
	$_POST['catid'] = filter_var($_POST['catid'], FILTER_SANITIZE_NUMBER_INT);
	$_POST['name'] = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
	$_POST['price'] = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION);
	$_POST['description'] = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
	$_POST['long_description'] = filter_var($_POST['long_description'], FILTER_SANITIZE_STRING);

    // TODO: complete the rest of the INSERT command if needed
	if (!preg_match('/^[\d\.]+$/', $_POST['pid']))
	    throw new Exception("invalid-pid");
    if (!preg_match('/^\d*$/', $_POST['catid']))
        throw new Exception("invalid-catid");
    if (!preg_match('/^[\w\- ]+$/', $_POST['name']))
        throw new Exception("invalid-name");
    if (!preg_match('/^[\d]+[.]{0,1}[\d]{0,1}$/', $_POST['price']))
        throw new Exception("invalid-price");
	if (!preg_match('/^[\w\-(). ]+$/', $_POST['description']))
		throw new Exception("invalid-text");
	if (!preg_match('/^[\w\-().\n- ]+$/', $_POST['long_description']))
		throw new Exception("invalid-long-description");

	$db_id = $_POST['pid'];
	$category = $_POST["catid"];
	$name = $_POST["name"];
	$price = $_POST["price"];
	$description = $_POST["description"];
	$long_description = $_POST["long_description"];
	// echo $db_id . " " . $category . " " . $name . " ". $price . " ". $id. " ". $description;
	$q = $db->prepare('UPDATE products SET catid = ?, name = ?, price = ?, description = ?, long_description = ? WHERE pid = ?');
	$q->execute(array($category,$name,$price,$description, $long_description, $db_id));
	$lastId = time() . rand(0,99);

    // Copy the uploaded file to a folder which can be publicly accessible at incl/img/[pid].jpg
	if ($_FILES["file"]["error"] == 0
        && ($_FILES["file"]["type"] == "image/jpeg" || $_FILES["file"]["type"] == "image/gif" || $_FILES["file"]["type"] == "image/png")
        && (mime_content_type($_FILES["file"]["tmp_name"]) == "image/jpeg" || mime_content_type($_FILES["file"]["tmp_name"]) == "image/gif" || mime_content_type($_FILES["file"]["tmp_name"]) == "image/png")
        && $_FILES["file"]["size"] < 10000000) {

		if($_FILES["file"]["type"] == "image/gif"){
			$filename = $lastId . ".gif";
		}else if($_FILES["file"]["type"] == "image/png"){
			$filename = $lastId . ".png";
		}else{
			$filename = $lastId . ".jpg";
		}
        $q = $db->prepare('UPDATE products SET filename = ? Where pid=?;');
        $q->execute(array($filename, $db_id));

		$destination = $_SERVER['DOCUMENT_ROOT'] . "/pic/product/" . $filename;
        // Note: Take care of the permission of destination folder (hints: current user is apache)
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $destination)) {
            // redirect back to original page; you may comment it during debug
			echo "SUCCESS";
        }
	}

	header('Location: panel.php');
	exit();
}

function deleteProduct(){
    // TODO: complete the code of this function to handle product deletion
	global $db;
    $db = configDB();
	$_POST['pid'] = filter_var($_POST['pid'], FILTER_SANITIZE_NUMBER_INT);
	if (!preg_match('/^[\d\.]+$/', $_POST['pid']))
		throw new Exception("invalid-pid");
	$_POST['pid'] = (int) $_POST['pid'];

	$delete_product = $_POST['pid'];
	$q = $db->prepare('DELETE FROM products WHERE pid = ?;');
	$q->execute(array($delete_product));
	header('Location: panel.php');
	exit();
}
