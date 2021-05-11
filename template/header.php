<?php header('X-Powered-By:'); ?>
<?php
    if( !defined( __DIR__ ) ) define( __DIR__, dirname(__FILE__) );
    require __DIR__.'/../admin/login/website-perm.php';
    require __DIR__.'/../admin/login/auth-process.php';
    require __DIR__.'/../admin/csrf/nonce.php';
    $auth_result = auth();
    $welcome_message = "Welcome, ";

    if($auth_result!=false){
        $welcome_message.=$auth_result;
        $logout_action = '"../admin/login/login-process.php?action=logout"';
        $change_password = '"../admin/login/change_password.php"';
        $login_button = "<button type='button' name='button' onclick='document.location.href = $change_password'>Change Password</button>";
        $login_button .= "<button type='button' name='button' onclick='window.location=$logout_action'>logout</button>";
    }else{
        $welcome_message.="Guest";
        $login_action = '"../login.php"';
        $login_button = "<button type='button' name='button' onclick='document.location.href = $login_action'>login</button>";
    }
?>
<link rel="stylesheet" href="/css/header.css">
<ul class="header-content">
    <li class="logo">
        <a href="/index.php"><img src="/icon/website_logo.png" alt="PriMarket Logo"></a>
    </li>
    <li id="welcome_message">
        <span><?php echo htmlspecialchars($welcome_message); ?></span>
    </li>
    <li id="shopping-cart">
        <span>Shopping List $<span class="cart_total">0</span></span>
        <div id="dropdown-shopping-cart">
            <p>
                <span class="product-name header">Product Name</span>
                <span class="input-quantity header">Quantity</span>
                <span class="price header">Price/item</span>
                <span class="subtotal header">subtotal</span>
            </p>
            <div id="cart_item">
            </div>
            <p>
                <span class="total header">Total: $<span class="cart_total">0</span></span><br>
                <button type="button" name="clearCart" id="clearCart">Clear Shopping Cart</button><br>
                <!-- <form action="https://www.paypal.com/us/cgi-bin/webscr" method="post"> -->
                <form action="https://www.sandbox.paypal.com/us/cgi-bin/webscr" id="paypal_shopping_cart" method="post" onsubmit="return cartSubmit();event.preventDefault();">
                    <input type="hidden" name="cmd" value="_cart">
                    <input type="hidden" name="redirect_cmd" value="_xclick">
                    <input type="hidden" name="upload" value="1">

                    <input type="hidden" name="business" value="sb-tp6df5972547@business.example.com">
                    <input type="hidden" name="currency_code" value="HKD">
                    <input type="hidden" name="charset" value="utf-8">
                    <input type="hidden" name="cancel_return" value="https://secure.s61.ierg4210.ie.cuhk.edu.hk/payment_status_page/payment-cancelled.html">
                    <input type="hidden" name="no_note" value="0">

                    <input type="hidden" name="custom" value="0">
                    <input type="hidden" name="invoice" value="0">

                    <input type="submit" class="checkOutButton" name="checkOutButton" value="Checkout">
                </form>
            </p>
        </div>
    </li>
    <li id="logout">
        <?php echo $login_button; ?>
    </li>
</ul>
<script type="text/javascript" src="/jquery/shopping_cart.js"></script>
