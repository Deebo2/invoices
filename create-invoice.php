<?php
session_start();
ob_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
 include 'includes/header.php';
 include 'includes/navigation.php';
 require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
 require_once 'connect.php';
 require_once 'includes/smtp.php';
 //CSRF TOKEN GENERATE
if (!isset($_SESSION['csrf-token'])) {
    $token = md5(uniqid(rand(), true));
    $_SESSION['csrf-token'] = $token;
    $_SESSION['csrf-token-time'] = time();
}
   //CSRF token validation
   if (isset($_POST['token']) && isset($_SESSION['csrf-token'])) {
       if ($_SESSION['csrf-token'] === $_POST['token']) {
       } else {
           $errors['token'] = 'Problem with CSRF token verification';
       }
   } 
//CSRF Token time validation
$time_limit = 60 * 60 * 24;
if (isset($_SESSION['csrf-token-time'])) {
    $token_time = $_SESSION['csrf-token-time'];

    if (($token_time + $time_limit) >= time()) {
    } else {
        $errors['token-time'] = 'CSRF token expired';
        unset($_SESSION['csrf-token']);
        unset($_SESSION['csrf-token-time']);
    }
} else {
    unset($_SESSION['csrf-token']);
    unset($_SESSION['csrf-token-time']);
}
if (!isset($errors)) {
    if (isset($_POST['submit'])) {
        //insert invoice into invoices table
        $sql1 = 'INSERT INTO invoices (client_id ,amount ,payment_mode ,payment_ref) VALUES (:client_id ,:amount ,:payment_mode ,:payment_ref)';
        $query1 = $db->prepare($sql1);
        $invoice = $query1->execute([
        ':client_id' => $_POST['client_id'],
        ':amount' => $_POST['amount'],
        ':payment_mode' => $_POST['payment_mode'],
        ':payment_ref' => $_POST['payment_ref'],
    ]);
        if ($invoice) {
            //get invoice inserted id
            $insertedID = $db->lastInsertId();
            //get items info form this invoice which are in the session
            foreach ($_SESSION['invoice'] as  $val) {
                $sql2 = 'SELECT * FROM items WHERE id=?';
                $query2 = $db->prepare($sql2);
                $query2->execute([$val['item_id']]);
                $invoiceItem = $query2->fetch(PDO::FETCH_ASSOC);
                $totalPrice = $invoiceItem['price'] * $val['quantity'];
                //Insert into invoice_items table
                $sql3 = 'INSERT INTO invoice_items (invoice_id ,item_id ,item_price ,item_quantity ,total_price) VALUES (:invoice_id ,:item_id ,:item_price ,:item_quantity ,:total_price)';
                $query3 = $db->prepare($sql3);
                $values = [
                ':invoice_id' => $insertedID,
                ':item_id' => $invoiceItem['id'],
                ':item_price' => $invoiceItem['price'],
                ':item_quantity' => $val['quantity'],
                ':total_price' => $totalPrice,
            ];
                $result3 = $query3->execute($values);
                //update the product stock to reduce the stock of item
                if ($invoiceItem['type'] == 'product') {
                    //add the product stock to items_stock table
                    $itemStkSql = 'INSERT INTO items_stock (item_id ,stock_out) VALUES (:item_id ,:stock_out)';
                    $itemStkResult = $db->prepare($itemStkSql);
                    $itemStkRes = $itemStkResult->execute([
                        ':item_id' => $invoiceItem['id'],
                        ':stock_out' => $val['quantity'],
                    ]);
                    //update the item stock value in items table for the invoice product
                    $existing_stock = $invoiceItem['stock'];
                    $updated_stock = $existing_stock - $val['quantity'];
                    $updateStockSql = 'UPDATE items SET stock=:stock WHERE id=:id';
                    $updateStockResult = $db->prepare($updateStockSql);
                    $updateStockRes = $updateStockResult->execute([
                        ':stock' => $updated_stock,
                        ':id' => $invoiceItem['id'],
                    ]);
                }
                if ($result3) {
                    //send invoice details to customer using phpMailer
                    //Create an instance; passing `true` enables exceptions
                    $mail = new PHPMailer(true);
                    $sqlc = 'SELECT name ,email FROM items WHERE id=?';
                    $queryc = $db->prepare($sqlc);
                    $queryc->execute([$_POST['client_id']]);
                    $clientsend = $queryc->fetch(PDO::FETCH_ASSOC);
                    try {
                        //Server settings
                        $mail->isSMTP();                                            //Send using SMTP
                        $mail->Host       = $smtphost;                     //Set the SMTP server to send through
                        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                        $mail->Username   = $smtpuser;                     //SMTP username
                        $mail->Password   = $smtppass;                               //SMTP password
                        $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                        //Recipients
                        $mail->setFrom('deebo0kaian@gmail.com', 'invoice pilling');
                        $mail->addAddress($clientsend['email'], $clientsend['name']);     //Add a recipient
                    
                        //Content
                        $mail->isHTML(true);                                  //Set email format to HTML
                        $mail->Subject = 'Payment Confirmation';
                        $mail->Body    = "Thank you for making payment of INR {$totalprice}/-";
                        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                        $mail->send();
                    } catch (Exception $e) {
                        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                    }
                    //redirect to view invoices page
                    unset($_SESSION['invoice']);
                    header('location: view-invoices.php');
                }
            }
        }
    }
}

 ?>
<style type="text/css">
            ul #result {
                line-height: none;
                width : 100%;
                margin: 0;
                padding : 0;
                display : none;
            }
            ul #result li a {
                color : #000;
                background : #ccc;
                display : block;
                text-decoration: none;
            }
            ul #result li a:hover{
                background: #aaa;
            }
        </style>
        <div id="page-wrapper" style="min-height: 345px;">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Create New Invoice</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <?php  if (isset($_GET['id']) && !empty($_GET['id'])):  ?>

            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Create a New Invoice Here...
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                <?php if (!empty($errors['token'])):  ?>
                                        <div class="alert alert-danger mb-3"><?= $errors['token']; ?></div>
                                    <?php endif; ?> 
                                    <?php if (!empty($errors['token-time'])):  ?>
                                        <div class="alert alert-danger mb-3"><?= $errors['token-time']; ?></div>
                                    <?php endif; ?> 
                                    <form role="form">
                                        <input type="hidden" id="cid" name="cid" value="<?= $_GET['id']; ?>">
                                        <div class="form-group">
                                            <label>Search Product/Service</label>
                                            <input class="form-control" id="search" name="name" placeholder="Product Name">
                                            <ul id="result"></ul>
                                        </div>
                                    </form>

                                </div>
                                <!-- /.col-lg-6 (nested) -->   
                            <!-- /.row (nested) -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <?php if (isset($_SESSION['invoice'])): ?>
                <div class="panel panel-default">
                  <div class="panel-body">
                            <div class="table-responsive">
                            <table class="table invoice-items">
                                <thead>
                                <tr class="h4 text-dark">
                                    <th id="cell-id" class="text-semibold">ID</th>
                                    <th id="cell-item" class="text-semibold">ITEM</th>
                                    <th id="cell-qty" class="text-center text-semibold">QUANTITY</th>
                                    <th id="cell-price" class="text-center text-semibold">UNIT COST</th>
                                    <th id="cell-total" class="text-center text-semibold">TOTAL</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php $total = 0;
                                     foreach ($_SESSION['invoice'] as $key => $val) {
                                         $sql = 'SELECT id ,name ,price FROM items WHERE id=?';
                                         $query = $db->prepare($sql);
                                         $query->execute([$val['item_id']]);
                                         $item = $query->fetch(PDO::FETCH_ASSOC); ?>
                                        <tr id="prodinv"></tr>
                                        <tr id="servinv"></tr>

                                        <tr id="prodinv">
                                            <td><a href="remove-item.php?sid=<?= $key; ?>&id=<?= $_GET['id']; ?>">x</a> <?= $item['id']; ?></td>
                                            <td class="text-semibold text-dark"><?= $item['name']; ?></td>
                                            <form method="POST" action="update-item-quantity.php">
                                                <td class="text-center">
                                                    <input type="number"  name="quantity" value="<?= $val['quantity']; ?>">
                                                    <input type="hidden"  name="itemId" value="<?= $item['id']; ?>">
                                                    <input type="hidden"  name="sessionId" value="<?= $key; ?>">
                                                    <input type="hidden"  name="cid" value="<?= $_GET['id']; ?>">
                                                    
                                                    <input type="submit"  value="Update">
                                                 </td>
                                                <td class="text-center amount">INR <?= $item['price']; ?>/-</td>

                                            </form>
                                            <td class="text-center amount">INR <?= $item['price'] * $val['quantity']; ?>/-</td>
                                        </tr>
                                <?php
                                        $total += $item['price'] * $val['quantity'];
                                     } ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <form method="POST">
                            <input type="hidden" name="token" value="<?php
                                        if (isset($_SESSION['csrf-token'])) {
                                            echo $_SESSION['csrf-token'];
                                        } ?>">
                                <div class="col-sm-12">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Grand Total</label>
                                            <h3 class="amount">INR <?= $total; ?>/-</h3> 
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Payment Mode</label>
                                            <input class="form-control" name="payment_mode" placeholder="Payment Mode">
                                        </div>
                                    </div>
                                    <input type="hidden"  name="client_id" value="<?= $_GET['id']; ?>">
                                    <input type="hidden"  name="amount" value="<?= $total; ?>">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Payment Reference</label>
                                            <input class="form-control" name="payment_ref" placeholder="Payment Reference">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <br>
                                            <input type="submit" name="submit" value="Create Invoice" class="btn btn-primary">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                       
                  </div>
        </div>
        <?php endif; ?>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <?php else: ?>
                <div class="alert alert-danger">Add a client by searching</div>
            <?php endif; ?>
        </div>
        <script type="text/javascript">
            var search = document.getElementById("search");
            var result = document.getElementById("result");
            var customerId = document.getElementById("cid");
            function getItemResults(){
                var searchVal = search.value;
                var customerIdVal = customerId.value;
                console.log('search value :'+searchVal);
                if(searchVal.length < 1){
                    result.style.display = 'none';
                    return;
                }
                var xhr = new XMLHttpRequest();
                var url = "search-items.php?search="+searchVal+"&cid="+customerIdVal;
                xhr.open('GET',url,true);
                 xhr.onreadystatechange = function(){
                    if(xhr.readyState == 4 && xhr.status == 200){
                        var text = xhr.responseText;
                        result.innerHTML=text;
                        result.style.display = 'block';

                    }
                    
                 }
                xhr.send();
            }
            search.addEventListener('input',getItemResults);
        </script>
        <?php include 'includes/footer.php'; ?>
