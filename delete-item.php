<?php

    require_once 'connect.php';
    if (isset($_GET['id'])) {
        $sql = 'DELETE FROM items WHERE id=?';
        $query = $db->prepare($sql);
        $result = $query->execute([$_GET['id']]);
        if ($result) {
            //redirect to view clients page
            //header('location: view-clients.php');
        } else {
            echo 'Faild to delete the service';
        }
    }
