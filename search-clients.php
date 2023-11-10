<?php

        require_once 'connect.php';
        $sql = 'SELECT id ,name ,mobile FROM clients WHERE name LIKE :search OR mobile LIKE :search OR email LIKE :search LIMIT 5';
        $query = $db->prepare($sql);
        $query->bindValue(':search', '%'.$_GET['search'].'%');
        $query->execute();
        $clients = $query->fetchAll(PDO::FETCH_ASSOC);
        if ($query->rowCount() > 0) {
            foreach ($clients as $client) {
                //var_dump($client);
                echo "<li><a href='create-invoice.php?id={$client['id']}'>".$client['name'].'--'.$client['mobile'].'</a></li>';
            }
        } else {
            echo '<li>No results</li>';
        }
