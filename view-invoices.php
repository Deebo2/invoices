<?php include 'includes/header.php';
     include 'includes/navigation.php';
     require_once 'connect.php';
     $sql = 'SELECT * FROM invoices';
     $result = $db->query($sql);
     $invoices = $result->fetchAll(PDO::FETCH_ASSOC);
     $rowsCount = $result->rowCount();
     ?>

        <div id="page-wrapper" style="min-height: 345px;">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Invoices</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">

                <div class="col-lg-12">
                <?php if ($rowsCount == 0): ?>
                            <div class="alert alert-primary m-3">There is no invoice added please create a new invoice</div>
                        <?php else: ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            All Invoices
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Client Name</th>
                                            <th>Total</th>
                                            <th>Payment Mode</th>
                                            <th>Patment Reference</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($invoices as $invoice): ?>
                                            <?php
                                                $sql = 'SELECT * FROM clients WHERE id=?';
                                                $query = $db->prepare($sql);
                                                $query->execute([$invoice['client_id']]);
                                                $client = $query->fetch(PDO::FETCH_ASSOC);
                                                ?>
                                        <tr>
                                            <td><a href="view-single-invoice.php?id=<?= $invoice['id']; ?>"><?= $invoice['id']; ?></a></td>
                                            <td><?= $client['name']; ?></td>
                                            <td><?= $invoice['amount']; ?></td>
                                            <td><?= $invoice['payment_mode']; ?></td>
                                            <td><?= $invoice['payment_ref']; ?></td>
                                            <!-- <td>
                                                <a class="btn btn-primary" href="update-invoice.php?id=<?= $invoice['id']; ?>">Update</a>
                                                <a class="btn btn-danger" href="delete-invoice.php?id=<?= $invoice['id']; ?>">Delete</a>
                                            </td> -->
                                        </tr>
                                        <?php endforeach; ?>
                                       <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.table-responsive -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
        </div>

        <?php include 'includes/footer.php'; ?>