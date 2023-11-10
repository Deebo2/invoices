<?php include 'includes/header.php';
     require_once 'connect.php';
     if(isset($_GET['id'])){
        $sql = "SELECT * FROM invoices WHERE id=?";
        $query = $db->prepare($sql);
        $query->execute(array($_GET['id']));
        $invoice = $query->fetch(PDO::FETCH_ASSOC); 
        //get client info
        $sql1 = "SELECT * FROM clients WHERE id=?";
        $query1 = $db->prepare($sql1);
        $query1->execute(array($invoice['client_id']));
        $client = $query1->fetch(PDO::FETCH_ASSOC);
        //get invoice items
        $sql2 = "SELECT * FROM invoice_items WHERE invoice_id=?";
        $query2 = $db->prepare($sql2);
        $query2->execute(array($_GET['id']));
        $invoiceItems = $query2->fetchAll(PDO::FETCH_ASSOC);
        $rowsCount = $query2->rowCount();
     ?>

    <div class="container" style="margin-top: 4%;">
    <div class="row ">
        <div class="col-8">
            <div class="card">
                <div class="card-body p-0">
                    <div class="row p-5">
                        <div class="col-md-6">
                        </div>

                        <div class="col-md-6 text-right">
                            <p class="font-weight-bold mb-1">Invoice #<?= $invoice['id'] ?></p>
                            <p class="text-muted">Due to: <?= $invoice['created'] ?></p>
                        </div>
                    </div>

                    <hr class="my-5">

                    <div class="row pb-5 p-5">
                        <div class="col-md-6">
                           
                            <p class="font-weight-bold mb-4">Client Information</p>
                            <p class="mb-1"><?= $client['name']; ?></p>
                            <p><?= $client['email']; ?></p>
                            <p class="mb-1"><?= $client['address']; ?></p>
                            <p class="mb-1"><?= $client['mobile']; ?></p>
                        </div>

                        <div class="col-md-6 text-right">
                            <p class="font-weight-bold mb-4">Payment Details</p>
                            <p class="mb-1"><span class="text-muted">Payment Mode: </span> <?= $invoice['payment_mode']; ?></p>
                            <p class="mb-1"><span class="text-muted">Payment Reference: </span> <?= $invoice['payment_ref']; ?></p>
                        </div>
                    </div>
                    <?php if ($rowsCount == 0): ?>
                        <div class="alert alert-primary m-3">There is no item for this invoice</div>
                    <?php else: ?>
                        <div class="row p-5">
                        <div class="col-md-12">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="border-0 text-uppercase small font-weight-bold">ID</th>
                                        <th class="border-0 text-uppercase small font-weight-bold">Item</th>
                                        <th class="border-0 text-uppercase small font-weight-bold">Description</th>
                                        <th class="border-0 text-uppercase small font-weight-bold">Quantity</th>
                                        <th class="border-0 text-uppercase small font-weight-bold">Unit Cost</th>
                                        <th class="border-0 text-uppercase small font-weight-bold">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $grandTotal = 0;
                                    foreach($invoiceItems as $invoiceItem){
                                        $sql3 = "SELECT * FROM items WHERE id=?";
                                        $query3 = $db->prepare($sql3);
                                        $query3->execute(array($invoiceItem['item_id']));
                                        $items = $query3->fetchAll(PDO::FETCH_ASSOC);
                                        foreach($items as $item){
                                     ?>
                                    <tr>
                                        <td><?= $item['id'];  ?></td>
                                        <td><?= $item['name'];  ?></td>
                                        <td><?= $item['description'];  ?></td>
                                        <td><?= $invoiceItem['item_quantity']; ?></td>
                                        <td>INR <?= $item['price'];  ?>/-</td>
                                        <td>INR <?= $invoiceItem['total_price']; ?>/-</td>
                                    </tr>
                                    <?php 
                                        
                                         } 
                                         $grandTotal += $invoiceItem['total_price'];  
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                        </div>
                    
                    <div class="d-flex flex-row-reverse bg-dark text-white p-4">
                        <div class="py-3 px-5 text-right">
                            <div class="mb-2">Grand Total</div>
                            <div class="h2 font-weight-light">INR <?= $grandTotal; ?>/-</div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
  
    <?php 
     }
    include 'includes/footer.php'; ?>
