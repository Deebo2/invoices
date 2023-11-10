<?php

        require_once 'connect.php';
        $sql = 'SELECT id ,name FROM items WHERE name LIKE :search LIMIT 5';
        $query = $db->prepare($sql);
        $query->bindValue(':search', '%'.$_GET['search'].'%');
        $query->execute();
        $items = $query->fetchAll(PDO::FETCH_ASSOC);
        if ($query->rowCount() > 0) {
            foreach ($items as $item) {
                //var_dump($client);
                echo "<li><a href='add-invoice-item.php?id={$item['id']}&cid={$_GET['cid']}'>".$item['name'].'</a></li>';
            }
        } else {
            echo '<li>No results</li>';
        }
