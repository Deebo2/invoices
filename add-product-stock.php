<?php
    session_start();
    ob_start();
    include 'includes/header.php';
    include 'includes/navigation.php';
    require_once 'connect.php';
    if (isset($_GET['id'])) {
        $sql = 'SELECT * FROM items WHERE id=?';
        $result = $db->prepare($sql);
        $result->execute([$_GET['id']]);

        if ($result->rowCount() == 1) {
            $productInfo = $result->fetch(PDO::FETCH_ASSOC);
        }
        if (isset($_POST['submit'])) {
            if (empty($_POST['name'])) {
                $errors['name'] = 'Name filed is required';
            }
            if (empty($_POST['stock'])) {
                $errors['stock'] = 'Stock filed is required';
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
                
                $sql = "INSERT INTO items_stock (item_id ,stock_in) VALUES (:item_id ,:stock_in)";
                $query = $db->prepare($sql);
                $result = $query->execute([
                        ':item_id' => $_GET['id'],
                        ':stock_in' => $_POST['stock']
                ]);
                if($result){
                    $existing_stock = $productInfo['stock'];
                    $updated_stock = $existing_stock + $_POST['stock'];
                    $stockSql = "UPDATE items SET stock =:stock ,updated =NOW() WHERE id=:id";
                    $stockQuery = $db->prepare($stockSql);
                    $stocked = $stockQuery->execute([
                        ':stock' => $updated_stock ,
                        ':id'    => $_GET['id']
                    ]);
                    if($stocked){
                        header('location: view-products.php');
                    }
                }else{
                    var_dump($_POST);
                }
     
            }
        }
        //CSRF TOKEN GENERATE
        if (!isset($_SESSION['csrf-token'])) {
            $token = md5(uniqid(rand(), true));
            $_SESSION['csrf-token'] = $token;
            $_SESSION['csrf-token-time'] = time();
        }
    }
?>

        <div id="page-wrapper" style="min-height: 345px;">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Add product Stock</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                        Add product Stock Here...
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
                                    <form role="form"  method="POST" action="add-product-stock.php?id=<?= $_GET['id']; ?>">
                                    <input type="hidden" name="token" value="<?php
                                        if (isset($_SESSION['csrf-token'])) {
                                            echo $_SESSION['csrf-token'];
                                        } ?>">
                                        <div class="form-group">
                                            <label>Product Name</label>
                                            <input class="form-control" name="name" value="<?php
                                                if (isset($_POST['name'])) {
                                                    echo $_POST['name'];
                                                } else {
                                                    if (isset($productInfo['name'])) {
                                                        echo $productInfo['name'];
                                                    }
                                                }
                                            ?>" placeholder="Enter Sevice Name">
                                            <?php if (!empty($errors['name'])):  ?>
                                                <div class="alert alert-danger mb-3"><?= $errors['name']; ?></div>
                                             <?php endif; ?>    
                                        </div>
                                        
                                        <div class="form-group">
                                            <label>Product Stock</label>
                                            <input class="form-control" name="stock" value="<?php
                                                if (isset($_POST['stock'])) {
                                                    echo $_POST['stock'];
                                                } 
                                            ?>" placeholder="Enter Product Stock">
                                            <?php if (!empty($errors['stock'])):  ?>
                                                <div class="alert alert-danger mb-3"><?= $errors['stock']; ?></div>
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