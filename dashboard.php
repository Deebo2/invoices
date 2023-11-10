<?php
include 'includes/header.php';
include 'includes/navigation.php';
 require_once 'connect.php';
 ?>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Dashboard</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-4 col-md-4">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-users fa-5x"></i>
                                </div>
                                <?php
                                    $today = date('Y-m-d');
                                    $clientsql = 'SELECT * FROM clients WHERE DATE(created)=?';
                                    $clientquery = $db->prepare($clientsql);
                                    $clientquery->execute([$today]);
                                    $todayClients = $clientquery->fetchAll(PDO::FETCH_ASSOC);
                                    $todayClientsNum = $clientquery->rowCount();

                                ?>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?= $todayClientsNum; ?></div>
                                    <div>New Customers Today!</div>
                                </div>
                            </div>
                        </div>
                        <a href="#">
                            <div class="panel-footer">
                                <span class="pull-left">View Customers</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">

                                    <i class="fa fa-money fa-5x"></i>
                                </div>
                                <?php
                                    $todayRevenue = 0;
                                    //Get number of new customers based on todays date
                                    $sql = 'SELECT * FROM invoices WHERE DATE(created)=?';
                                    $query = $db->prepare($sql);
                                    $query->execute([$today]);
                                    $invoices = $query->fetchAll(PDO::FETCH_ASSOC);
                                    $invoicesNum = $query->rowCount();
                                    foreach ($invoices as $invoice) {
                                        $todayRevenue += $invoice['amount'];
                                    }
                                ?>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php echo $invoicesNum; ?></div>
                                    <div>Today Invoices!</div>
                                </div>
                            </div>
                        </div>
                        <a href="#">
                            <div class="panel-footer">
                                <span class="pull-left">View Invoices</span>
                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                <div class="clearfix"></div>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4">
                    <div class="panel panel-yellow">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-inr fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php echo $todayRevenue; ?>/-</div>
                                    <div>Todays Revenue!</div>
                                </div>
                            </div>
                        </div>
                        <a href="#">
                            <div class="panel-footer">
                                &nbsp;
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                <script type="text/javascript">
                    google.charts.load('current', {'packages':['corechart']});
                    google.charts.setOnLoadCallback(drawChart);

                    function drawChart() {
                        var data = google.visualization.arrayToDataTable([
                        ['Days', 'Customers', 'Revenue'],
                        <?php
                            //get customers number and revenue for 7 days
                            for ($i = 0; $i <= 7; ++$i) {
                                $revenue = 0;
                                $day = date('Y-m-d', strtotime("-$i days"));
                                $sql7 = 'SELECT * FROM invoices WHERE DATE(created)=?';
                                $query7 = $db->prepare($sql7);
                                $query7->execute([$day]);
                                $inv7 = $query7->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($inv7 as $inv) {
                                    $revenue += $inv['amount'];
                                }
                                $clientsql = 'SELECT * FROM clients WHERE DATE(created)=?';
                                $clientquery = $db->prepare($clientsql);
                                $clientquery->execute([$day]);
                                $todayClientsNum = $clientquery->rowCount(); ?>
                        ["<?= $day; ?>",  <?= $todayClientsNum; ?>,    <?= $revenue; ?>  ],
                            <?php
                            } ?>
                        ]);

                        var options = {
                        title: '7 Days Performance',
                        hAxis: {title: 'Days',  titleTextStyle: {color: '#333'}},
                        vAxis: {minValue: 0}
                        };

                        var chart = new google.visualization.AreaChart(document.getElementById('chart_div'));
                        chart.draw(data, options);
                    }
                </script>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i> Last 7 Days Revenue
                            <!-- <div class="pull-right">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                        Actions
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                        <li><a href="#">Action</a>
                                        </li>
                                        <li><a href="#">Another action</a>
                                        </li>
                                        <li><a href="#">Something else here</a>
                                        </li>
                                        <li class="divider"></li>
                                        <li><a href="#">Separated link</a>
                                        </li>
                                    </ul>
                                </div>
                            </div> -->
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                        <div id="chart_div" style="width: 100%; height: 300px;"></div>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-bar-chart-o fa-fw"></i> 30 Days Revenue
                            <!-- <div class="pull-right">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                        Actions
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                        <li><a href="#">Action</a>
                                        </li>
                                        <li><a href="#">Another action</a>
                                        </li>
                                        <li><a href="#">Something else here</a>
                                        </li>
                                        <li class="divider"></li>
                                        <li><a href="#">Separated link</a>
                                        </li>
                                    </ul>
                                </div>
                            </div> -->
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Number Of Invoices</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                //loop through 30 days
                                                for ($i = 0; $i <= 30; ++$i) {
                                                    $revenue = 0;
                                                    $day = date('Y-m-d', strtotime("-$i days"));
                                                    $sql3 = 'SELECT * FROM invoices WHERE DATE(created)=?';
                                                    $query3 = $db->prepare($sql3);
                                                    $query3->execute([$day]);
                                                    $inv30 = $query3->fetchAll(PDO::FETCH_ASSOC);
                                                    $invoices30Num = $query3->rowCount();
                                                    foreach ($inv30 as $inv) {
                                                        $revenue += $inv['amount'];
                                                    } ?>
                                                <tr>
                                                    <td><?= $day; ?></td>
                                                    <td><?= $invoices30Num; ?></td>
                                                    <td><?= $revenue; ?>/-</td>
                                                </tr>
                                            <?php
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.table-responsive -->
                                </div>
                                <!-- /.col-lg-4 (nested) -->
                                <div class="col-lg-4">
                                    <div id="morris-bar-chart"></div>
                                </div>
                                <!-- /.col-lg-8 (nested) -->
                            </div>
                            <!-- /.row -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    
                </div>
                <!-- /.col-lg-8 -->
                
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->

  <?php include 'includes/footer.php'; ?>
