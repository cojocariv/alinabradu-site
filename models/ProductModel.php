<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';

class ProductModel
{
    public static function featured(int $limit = 8): array
    {
        $sql = 'SELECT * FROM products ORDER BY id DESC LIMIT :limit';
        $stmt = getDbConnection()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function filter(array $filters = []): array
    {
        $sql = 'SELECT * FROM products WHERE 1=1';
        $params = [];

        if (!empty($filters['category'])) {
            $sql .= ' AND category = :category';
            $params[':category'] = $filters['category'];
        }
        if (!empty($filters['subcategory'])) {
            $sql .= ' AND subcategory = :subcategory';
            $params[':subcategory'] = $filters['subcategory'];
        }
        if (!empty($filters['size'])) {
            $sql .= ' AND FIND_IN_SET(:size, size)';
            $params[':size'] = $filters['size'];
        }

        $sql .= ' ORDER BY id DESC';
        $stmt = getDbConnection()->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function bySlug(string $slug): ?array
    {
        $sql = 'SELECT * FROM products WHERE slug = :slug LIMIT 1';
        $stmt = getDbConnection()->prepare($sql);
        $stmt->bindValue(':slug', $slug);
        $stmt->execute();
        $product = $stmt->fetch();
        return $product ?: null;
    }

    public static function byCategorySlug(string $category, ?string $subcategory = null): array
    {
        $sql = 'SELECT * FROM products WHERE category_slug = :category';
        $params = [':category' => $category];

        if ($subcategory) {
            $sql .= ' AND subcategory_slug = :subcategory';
            $params[':subcategory'] = $subcategory;
        }

        $sql .= ' ORDER BY id DESC';
        $stmt = getDbConnection()->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function similar(int $productId, string $category, int $limit = 4): array
    {
        $sql = 'SELECT * FROM products WHERE category = :category AND id != :id ORDER BY id DESC LIMIT :limit';
        $stmt = getDbConnection()->prepare($sql);
        $stmt->bindValue(':category', $category);
        $stmt->bindValue(':id', $productId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
