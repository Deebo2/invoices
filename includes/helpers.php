<?php 

    function uniqueVal($db,$key ,$val){
        $sql = "SELECT * FROM clients WHERE '$key'='$val'";
        $result=$db->prepare($sql);
        $result->execute();
        $count = $result->rowCount();
        if($count >= 1){
           return false;
        }
    }


?>