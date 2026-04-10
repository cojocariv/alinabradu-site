<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

class CategoryModel
{
    public static function all(): array
    {
        $sql = 'SELECT id, name, slug FROM categories ORDER BY name ASC';
        $stmt = getDbConnection()->query($sql);
        return $stmt->fetchAll();
    }
}
