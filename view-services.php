<?php include 'includes/header.php';
     include 'includes/navigation.php';
     require_once 'connect.php';
     $sql = "SELECT * FROM items WHERE type = 'service'";
     $result = $db->query($sql);
     $services = $result->fetchAll(PDO::FETCH_ASSOC);
     $rowsCount = $result->rowCount();
     ?>

        <div id="page-wrapper" style="min-height: 345px;">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Services</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            All Services 
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                        <?php if ($rowsCount == 0): ?>
                            <div class="alert alert-primary m-3">There is no Service added please add a service<a class="text-primary" href='add-service.php'>&nbsp&nbsp Add service</a></div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Service Name</th>
                                            <th>Price</th>
                                            <th>Operators</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($services as $service): ?>
                                        <tr>
                                            <td><?= $service['id']; ?></td>
                                            <td><?= $service['name']; ?></td>
                                            <td><?= $service['price']; ?></td>
                                            <td>
                                                <a class="btn btn-primary" href="update-service.php?id=<?= $service['id']; ?>">Update</a>
                                                <a class="btn btn-danger" href="delete-item.php?id=<?= $service['id']; ?>">Delete</a>
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