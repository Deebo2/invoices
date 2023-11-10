<?php include 'includes/header.php';
     include 'includes/navigation.php';
     require_once 'connect.php';
     $sql = 'SELECT * FROM clients';
     $result = $db->query($sql);
     $clients = $result->fetchAll(PDO::FETCH_ASSOC);
     $rowsCount = $result->rowCount();
     ?>

        <div id="page-wrapper" style="min-height: 345px;">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">View Clients</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            All Clients
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                        <?php if ($rowsCount == 0): ?>
                            <div class="alert alert-primary m-3">There is no client added please add a client<a class="text-primary" href='add-client.php'>&nbsp&nbsp Add client</a></div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>E-Mail</th>
                                            <th>Mobile</th>
                                            <th>Operators</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($clients as $client): ?>
                                        <tr>
                                            <td><?= $client['id']; ?></td>
                                            <td><?= $client['name']; ?></td>
                                            <td><?= $client['email']; ?></td>
                                            <td><?= $client['mobile']; ?></td>
                                            <td>
                                                <a class="btn btn-primary" href="update-client.php?id=<?= $client['id']; ?>">Update</a>
                                                <a class="btn btn-danger" href="delete-client.php?id=<?= $client['id']; ?>">Delete</a>
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