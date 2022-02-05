<?php

namespace App\Models;

use PDO;


/**
 * Get all the categories
 * 
 * @return array An associative array of all the categories records
 */
class Category extends \Core\Model
{
    public static function getAll(): array
    {
        $sql = 'SELECT *
                FROM categories
                ORDER BY name';

        $db = static::getDB();

        $stmt = $db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = null;
    }
}
