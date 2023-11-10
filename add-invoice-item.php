<?php

    session_start();
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $itemid = $_GET['id'];
        if (isset($_GET['quant']) && !empty($_GET['quant'])) {
            $quant = $_GET['quant'];
        } else {
            $quant = 1;
        }
        if(isset($_SESSION['invoice'])){
           $key = array_search($itemid , array_column($_SESSION['invoice'] , 'item_id'));
           if(isset($key) && ($key != NULL)){
            $_SESSION['invoice'][$key]['quantity']++;

           }else{
            echo "Item does not exist in the session";
            $_SESSION['invoice'][] = [
                'item_id' => $itemid,
                'quantity' => $quant,
            ];
           }
          
        }else{
            echo "Session does not exist";
            $_SESSION['invoice'][] = [
                'item_id' => $itemid,
                'quantity' => $quant,
            ];

        }
        //redirect user to create-invoice page with customer id
        header("location: create-invoice.php?id={$_GET['cid']}");
        
    }else{
        //redirect user to create-invoice page with customer id
        header("location: create-invoice.php?id={$_GET['cid']}");
    }
    echo '<pre>';
    print_r($_SESSION['invoice']);
    echo '</pre>';
  // unset($_SESSION['invoice']);

