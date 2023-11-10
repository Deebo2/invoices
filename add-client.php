<?php
    session_start();
    ob_start();
    include 'includes/header.php';
    include 'includes/navigation.php';
    require_once 'connect.php';
    function uniqueVal($db, $key, $val)
    {
        $sql = "SELECT * FROM clients WHERE `$key`=?";
        $result = $db->prepare($sql);
        $result->execute([$val]);
        $count = $result->rowCount();

        return $count;
    }

    if (isset($_POST['submit']) && !empty($_POST)) {
        if (empty($_POST['name'])) {
            $errors['name'] = 'Name filed is required';
        }
        if (empty($_POST['email'])) {
            $errors['email'] = 'E-Mail filed is required';
        } else {
            $count = uniqueVal($db, 'email', $_POST['email']);
            if ($count >= 1) {
                $errors['email'] = 'E-mail already exists';
            }
        }
        if (empty($_POST['mobile'])) {
            $errors['mobile'] = 'Mobile filed is required';
        } else {
            $count = uniqueVal($db, 'mobile', $_POST['mobile']);
            if ($count >= 1) {
                $errors['mobile'] = 'Mobile already exists';
            }
        }

        //CSRF token validation
        if (isset($_POST['token']) && isset($_SESSION['csrf-token'])) {
            if ($_SESSION['csrf-token'] === $_POST['token']) {
            } else {
                $errors['token'] = 'Problem with CSRF token verification';
            }
        } else {
            $errors['token'] = 'Problem with CSRF token verification';
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
            $sql = 'INSERT INTO clients (name ,email ,mobile ,address) VALUES (:name ,:email ,:mobile ,:address)';
            $query = $db->prepare($sql);
            $values = [
            ':name' => $_POST['name'],
            ':email' => $_POST['email'],
            ':mobile' => $_POST['mobile'],
            ':address' => $_POST['address'],
        ];
            $result = $query->execute($values);
            if ($result) {
                header('location: view-clients.php');
            }
        }
    }
    //CSRF TOKEN GENERATE
    if (!isset($_SESSION['csrf-token'])) {
        $token = md5(uniqid(rand(), true));
        $_SESSION['csrf-token'] = $token;
        $_SESSION['csrf-token-time'] = time();
    }
?>

        <div id="page-wrapper" style="min-height: 345px;">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Add New Client</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Create a New Client Here...
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-6">
                                
                                     <?php if (!empty($errors['token'])):  ?>
                                        <div class="alert alert-danger mb-3"><?= $errors['token']; ?></div>
                                    <?php endif; ?> 
                                    <?php if (!empty($errors['token-time'])):  ?>
                                        <div class="alert alert-danger mb-3"><?= $errors['token-time']; ?></div>
                                    <?php endif; ?> 
                                    <form role="form" method="POST" action="add-client.php">
                                        <input type="hidden" name="token" value="<?php
                                        if (isset($_SESSION['csrf-token'])) {
                                            echo $_SESSION['csrf-token'];
                                        } ?>">
                                        <div class="form-group">
                                            <label>Client Name</label>
                                            <input class="form-control" name="name" value="<?php
                                                if (isset($_POST['name'])) {
                                                    echo $_POST['name'];
                                                }
                                            ?>" placeholder="Enter Client Name">
                                            <?php if (!empty($errors['name'])):  ?>
                                                <div class="alert alert-danger mb-3"><?= $errors['name']; ?></div>
                                             <?php endif; ?>       
                                        </div>
                                        <div class="form-group">
                                            <label>E-Mail</label>
                                            <input type="email" name="email" class="form-control" value="<?php
                                                if (isset($_POST['email'])) {
                                                    echo $_POST['email'];
                                                }
                                            ?>" placeholder="Enter E-Mail">
                                            <?php if (!empty($errors['email'])):  ?>
                                                <div class="alert alert-danger mb-3"><?= $errors['email']; ?></div>
                                             <?php endif; ?> 
                                        </div>
                                        <div class="form-group">
                                            <label>Mobile</label>
                                            <input class="form-control" name="mobile" value="<?php
                                                if (isset($_POST['mobile'])) {
                                                    echo $_POST['mobile'];
                                                }
                                            ?>" placeholder="Enter Mobile Number">
                                            <?php if (!empty($errors['mobile'])):  ?>
                                                <div class="alert alert-danger  mb-3"><?= $errors['mobile']; ?></div>
                                             <?php endif; ?> 
                                        </div>
                                        <div class="form-group">
                                            <label>Address</label>
                                            <textarea class="form-control" name="address" placeholder="Enter Client Address">
                                            <?php
                                                 if (isset($_POST['address'])) {
                                                     echo $_POST['address'];
                                                 }
                                            ?>
                                            </textarea>
                                        </div>

                                        <input type="submit" class="btn btn-primary" name="submit" value="Submet">
                                        <button type="reset" class="btn btn-danger">Reset </button>
                                    </form>
                                </div>
                                <!-- /.col-lg-6 (nested) -->   
                            <!-- /.row (nested) -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
        </div>

        <?php include 'includes/footer.php'; ?>