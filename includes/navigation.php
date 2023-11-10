<style type="text/css">
            ul #search-result {
                line-height: none;
                width : 100%;
                margin: 0;
                padding : 0;
                display : none;
            }
            ul #search-result li a {
                color : #000;
                background : #ccc;
                display : block;
                text-decoration: none;
            }
            ul #search-result li a:hover{
                background: #aaa;
            }
        </style>
 <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="dashboard.php">Billing Dashboard</a>
            </div>
            <!-- /.navbar-header -->
<!-- 
            <ul class="nav navbar-top-links navbar-right">

                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="#"><i class="fa fa-user fa-fw"></i> User Profile</a>
                        </li>
                        <li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a>
                        </li>
                        <li class="divider"></li>
                        <li><a href="login.html"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                        </li>
                    </ul>
                   
                </li>
              
            </ul> -->
            <!-- /.navbar-top-links -->

            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li class="sidebar-search">
                            <div class="input-group custom-search-form">
                                <input type="text" id="client-search" class="form-control" placeholder="Search...">
                                <span class="input-group-btn">
                                <button class="btn btn-default" type="button">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                            </div>
                            <ul id="search-result"></ul>
                            <!-- /input-group -->
                        </li>
                        <li>
                            <a href="dashboard.php"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-bar-chart-o fa-fw"></i> Clients<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="add-client.php">Add Client</a>
                                </li>
                                <li>
                                    <a href="view-clients.php">View Clients</a>
                                </li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-bar-chart-o fa-fw"></i> Products<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="add-product.php">Add Product</a>
                                </li>
                                <li>
                                    <a href="view-products.php">View Products</a>
                                </li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-bar-chart-o fa-fw"></i> Services<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="add-service.php">Add Service</a>
                                </li>
                                <li>
                                    <a href="view-services.php">View Services</a>
                                </li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <li>
                            <a href="#"><i class="fa fa-bar-chart-o fa-fw"></i> Invoices<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="create-invoice.php">Create Invoice</a>
                                </li>
                                <li>
                                    <a href="view-invoices.php">View Invoices</a>
                                </li>
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>
                        <!-- <li>
                            <a href="#"><i class="fa fa-bar-chart-o fa-fw"></i> Admin Users<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="#">Add User</a>
                                </li>
                                <li>
                                    <a href="#">View Users</a>
                                </li>
                                <li>
                                    <a href="#">My Profile</a>
                                </li>
                            </ul>
                           // /.nav-second-level
                        </li> -->
                        <!-- <li>
                            <a href="#"><i class="fa fa-bar-chart-o fa-fw"></i> Settings<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="#">Site Settings</a>
                                </li>
                                <li>
                                    <a href="#">General Articles</a>
                                </li>
                            </ul>
                            ///.nav-second-level
                        </li> -->
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
        </nav>
        <script type="text/javascript">
            var clientsearch = document.getElementById("client-search");
            var searchresult = document.getElementById("search-result");
            function getSearchResults(){
                var clientsearchVal = clientsearch.value;
                console.log('search value :'+clientsearchVal);
                if(clientsearchVal.length < 1){
                    searchresult.style.display = 'none';
                    return;
                }
                var xhr = new XMLHttpRequest();
                var url = "search-clients.php?search="+clientsearchVal;
                xhr.open('GET',url,true);
                 xhr.onreadystatechange = function(){
                    if(xhr.readyState == 4 && xhr.status == 200){
                        var text = xhr.responseText;
                        searchresult.innerHTML=text;
                        searchresult.style.display = 'block';

                    }
                    
                 }
                xhr.send();
            }
            clientsearch.addEventListener('input',getSearchResults);
        </script>