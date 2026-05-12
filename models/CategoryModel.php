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

    /** @return array<string, mixed>|null */
    public static function findBySlug(string $slug): ?array
    {
        $stmt = getDbConnection()->prepare('SELECT id, name, slug FROM categories WHERE slug = :slug LIMIT 1');
        $stmt->execute([':slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(string $name, string $slug): int
    {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare('INSERT INTO categories (name, slug) VALUES (:name, :slug)');
        $stmt->execute([':name' => $name, ':slug' => $slug]);
        return (int) $pdo->lastInsertId();
    }
}
