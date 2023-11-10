<?php
    session_start();
    ob_start();
    include 'includes/header.php';
    include 'includes/navigation.php';
    require_once 'connect.php';

    if (isset($_POST['submit'])) {
        if (empty($_POST['name'])) {
            $errors['name'] = 'Name filed is required';
        }
        if (empty($_POST['description'])) {
            $errors['description'] = 'Description filed is required';
        }
        if (empty($_POST['price'])) {
            $errors['price'] = 'price filed is required';
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
            $sql = "INSERT INTO `items`(name, description, type, price, stock ) VALUES (:name, :description, 'product', :price ,'')";
            $query = $db->prepare($sql);
            $values = [
            ':name' => $_POST['name'],
            ':description' => $_POST['description'],
            ':price' => $_POST['price'],
        ];
            $result = $query->execute($values);
            if ($result) {
             header("location: view-products.php");
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
                    <h1 class="page-header">Add New Product</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Create a New Product Here...
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
                                    <form role="form" method="POST" action="add-product.php">
                                    <input type="hidden" name="token" value="<?php
                                        if (isset($_SESSION['csrf-token'])) {
                                            echo $_SESSION['csrf-token'];
                                        } ?>">
                                        <div class="form-group">
                                            <label>Product Name</label>
                                            <input class="form-control" name="name" value="<?php
                                                if (isset($_POST['name'])) {
                                                    echo $_POST['name'];
                                                }
                                            ?>" placeholder="Enter Product Name">
                                            <?php if (!empty($errors['name'])):  ?>
                                                <div class="alert alert-danger mb-3"><?= $errors['name']; ?></div>
                                             <?php endif; ?> 
                                        </div>
                                        <div class="form-group">
                                            <label>Product Description</label>
                                            <textarea class="form-control" name="description" rows="3"><?php
                                                if (isset($_POST['description'])) {
                                                    echo $_POST['description'];
                                                }
                                            ?></textarea>
                                            <?php if (!empty($errors['description'])):  ?>
                                                <div class="alert alert-danger mb-3"><?= $errors['description']; ?></div>
                                             <?php endif; ?>
                                        </div>
                                        <div class="form-group">
                                            <label>Product Price</label>
                                            <input class="form-control" name="price" value="<?php
                                                if (isset($_POST['price'])) {
                                                    echo $_POST['price'];
                                                }
                                            ?>" placeholder="Enter Service Price">
                                            <?php if (!empty($errors['price'])):  ?>
                                                <div class="alert alert-danger mb-3"><?= $errors['price']; ?></div>
                                             <?php endif; ?>
                                        </div>
                                        <input type="submit" name="submit" value="submit" class="btn btn-primary">
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
