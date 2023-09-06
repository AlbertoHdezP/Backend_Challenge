<?php

namespace Crimsoncircle\Model;

use \PDO;

class Blog
{
    function getBlogs()
    {
        try {
            $pdo = new PDO('mysql:host=db;dbname=crimsoncircle', 'root', 'crimsoncircle');
            $stmt = $pdo->query("SELECT * FROM blog ORDER BY id DESC");
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }
}