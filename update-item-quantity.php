<?php 
session_start();
if(isset($_POST['quantity'])){
    $sessionId = $_POST['sessionId'];
    $quantity = $_POST['quantity'];
    $_SESSION['invoice'][$sessionId]['quantity'] = $quantity;
    header("location: create-invoice.php?id={$_POST['cid']}");
}


?>