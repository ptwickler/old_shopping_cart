<?php

ini_set('display_errors', 1);

error_reporting(E_ALL);



/*
 * The index page controls the user's path through the site. There are three different "modes" which will cause the
 * page to display differently. First, the "normal" mode which is a user is either signed in, or not, but has not
 * selected that they want to check out (products are displayed). Second, "checkout" mode. The user must be signed in to
 * click the checkout button. If selected, the products are not displayed, but the user's items for purchase are.
 * Thirdly, "register_new" mode. It displays the products, but also displays the registration form.
 */

ob_start();


require_once('functions.php');

if (isset($_GET['out']) && $_GET['out']==1){
    session_start();

    //Destroys the session
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]);
    }

    // Finally, destroy the session.
    session_destroy();

    //Add in a page reload so that the session_destroy() will take effect

    $url = "http://" . $_SERVER['HTTP_HOST'] . "/cart/index.php";

    header("Location: ".$url) or die("Didn't work");
}

if(!isset($_SESSION)) {
    session_start();
}

$_SESSION['cart'] = array();

// The valid property of the session will be used to store values for form validation. If it's set, don't change it,
// but if it's not set, set it.
if (!isset($_SESSION['valid'])) {
    $_SESSION['valid'] = array();
}

include_once("template_top.inc");

if (isset($_GET['admin']) && $_GET['admin'] == 2) {
    echo '<div class="admin_wrapper">
            <a href="http://petertwickler.com/cart/index.php?admin=3">Edit Accounts</a><br />
            <a href="http://petertwickler.com/cart/index.php?admin=4">Edit Products</a><br />
            <a href="http://petertwickler.com/cart/index.php?admin=5">View Purchases</a>
          </div>';
}

if (isset($_GET['admin']) && $_GET['admin'] == 3) {

    $accounts_display= admin_accounts();
    echo '<div class="admin_wrapper">
            <a href="http://petertwickler.com/cart/index.php?admin=3">Edit Accounts</a><br />
            <a href="http://petertwickler.com/cart/index.php?admin=4">Edit Products</a><br />
            <a href="http://petertwickler.com/cart/index.php?admin=5">View Purchases</a>
          </div>
          <div class="accounts_display">
             '. $accounts_display .'
          </div>';

}


if (isset($_GET['admin']) && $_GET['admin'] ==4){

    $products_display = admin_products();
    echo '<div class="admin_wrapper">
            <a href="http://petertwickler.com/cart/index.php?admin=3">Edit Accounts</a><br />
            <a href="http://petertwickler.com/cart/index.php?admin=4">Edit Products</a><br />
            <a href="http://petertwickler.com/cart/index.php?admin=5">View Purchases</a>
          </div>
          <div class="products_display">
             '. $products_display .'
          </div>';

}

// If the user is in the purchases admin area, but hasn't viewed a particular order.
if ((isset($_GET['admin']) && $_GET['admin'] ==5) && !isset($_GET['order'])){

    $purchases_display = admin_purchases_display();
    echo '<div class="admin_wrapper">
            <a href="http://petertwickler.com/cart/index.php?admin=3">Edit Accounts</a><br />
            <a href="http://petertwickler.com/cart/index.php?admin=4">Edit Products</a><br />
            <a href="http://petertwickler.com/cart/index.php?admin=5">View Purchases</a>
          </div>
          <div class="purchases_display">
             '. $purchases_display .'
          </div>';

}

// If the user is in the display purchases admin area and HAS viewed a particular order, display
// the same as above, but add the order info, too.
if (isset($_GET['order']) && $_GET['order'] == 1){
    $order_display = display_order($_POST);
     $order_display;

    $purchases_display = admin_purchases_display();
    echo '<div class="admin_wrapper">
            <a href="http://petertwickler.com/cart/index.php?admin=3">Edit Accounts</a><br />
            <a href="http://petertwickler.com/cart/index.php?admin=4">Edit Products</a><br />
            <a href="http://petertwickler.com/cart/index.php?admin=5">View Purchases</a>
          </div>
          <div class="order_display">
             '. $purchases_display .'
          </div>
          <div class="order_display_wrapper">'. $order_display . '</div>';
}


// This if statement tests for the username and passwords in the POST variable. If they are there, it activates the
// login.
if (isset($_POST['username']) && isset($_POST['password'])) {

    user_cred($_POST);

}



// This puts the site into "checkout mode".
if ((isset($_GET['checkout']) && $_GET['checkout'] ==1) && isset($_SESSION['out_cart'])) {
    $items = $_SESSION['out_cart'];

    $out_table = build_out_cart($items);

    echo '
<div class="checkout_cart_display">

          <h2>Your Order:</h2>
          <hr>

            <table><tbody><th>Item</th><th>Quantity</th><th>Price</th>' . $out_table . '</div><br><br><br><br>
<form name = "purchase" action="index.php?checkout=1&close=1" method="POST">
  <input type="text" hidden name="mail" value="1">
  <input class="complete_purchase_button" type="submit" value="complete purchase">
</form>
</div>
<div>
  <div class="continue_shopping_link"><a href="index.php?close=1">Continue Shopping!</a></div><!-- end .continue_shopping_link -->

</div>';


    // If the post variable "mail" is set and equals 1, send the confirmation email and display the confirmation message.
    if ((isset($_POST['mail']) && $_POST['mail'] == 1)) {



        $thanks = confirm_email($_SESSION['username']);


        if ($thanks){
            echo $thanks;
        }

        if (!$thanks) {
            echo "There was a problem and we could not send your confirmation email";
        }

    }
    echo '</body></html>';
}

elseif(isset($_GET['remove_cart']) && $_GET['remove_cart'] ==1 ){
    remove_from_cart($_POST);
}

// If none of the other "special case" query strings are set, the script displays the products. That is, the site
// is in "shopping mode".
elseif (!isset($_GET['admin']) && !isset($_GET['order'])){
    $product_list = display();
    for ($i = 0; $i < count($product_list); $i++){
        echo $product_list[$i];
    }

    echo "</div><!--end div.wrapper-->";

    echo '</body>
</html>';
}




