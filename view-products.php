<?php include 'includes/header.php';
     include 'includes/navigation.php';
     require_once 'connect.php';
     $sql = "SELECT * FROM items WHERE type = 'product'";
     $result = $db->query($sql);
     $products = $result->fetchAll(PDO::FETCH_ASSOC);
     $rowsCount = $result->rowCount();
     ?>


        <div id="page-wrapper" style="min-height: 345px;">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Products</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            All Products
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                        <?php if ($rowsCount == 0): ?>
                            <div class="alert alert-primary m-3">There is no Product added please add a service<a class="text-primary" href='add-service.php'>&nbsp&nbsp Add service</a></div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                        <th>#</th>
                                            <th>Product Name</th>
                                            <th>Price</th>
                                            <th>Stock</th>
                                            <th>Operators</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td><?= $product['id']; ?></td>
                                            <td><?= $product['name']; ?></td>
                                            <td><?= $product['price']; ?></td>
                                            <td><?= $product['stock']; ?></td>
                                            <td>
                                                <a class="btn btn-primary" href="update-product.php?id=<?= $product['id']; ?>">Update</a>
                                                <a class="btn btn-success" href="add-product-stock.php?id=<?= $product['id']; ?>">Add Stock</a>
                                                <a class="btn btn-danger" href="delete-item.php?id=<?= $product['id']; ?>">Delete</a>
                                            </td>
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
