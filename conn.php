<?php
        $username = "z1754749"; // object to hold username
        $password = "1995Nov28"; // object to hold password
        try { // if something goes wrong, an exeption is thrown
                $dsn = "mysql:host=courses;dbname=z1754749";      // creating dsn string
                $pdo = new PDO($dsn, $username, $password);       // constructing an instance of the PDO class
        }
        catch(PDOexception $e) { // handle the exeption
                echo "Connection to database failed: " . $e->getMessage();
        }
?>

