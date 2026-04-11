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

    /** Produse marcate pentru homepage (după migrare admin). */
    public static function featuredHome(int $limit = 12): array
    {
        $sql = 'SELECT * FROM products WHERE featured_on_home = 1 ORDER BY home_sort ASC, id DESC LIMIT :limit';
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

    public static function allForAdmin(): array
    {
        $sql = 'SELECT * FROM products ORDER BY id DESC';
        return getDbConnection()->query($sql)->fetchAll();
    }

    public static function findById(int $id): ?array
    {
        $sql = 'SELECT * FROM products WHERE id = :id LIMIT 1';
        $stmt = getDbConnection()->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** URL-uri imagini în ordine; dacă nu există rânduri în product_images, returnează []. */
    public static function getImageUrls(int $productId): array
    {
        $sql = 'SELECT image_url FROM product_images WHERE product_id = :id ORDER BY sort_order ASC, id ASC';
        $stmt = getDbConnection()->prepare($sql);
        $stmt->bindValue(':id', $productId, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return array_values(array_filter(array_map('strval', $rows)));
    }

    public static function getPrimaryImageUrl(array $product): string
    {
        $urls = self::getImageUrls((int) $product['id']);
        if (!empty($urls)) {
            return $urls[0];
        }
        return (string) $product['image'];
    }

    public static function replaceImages(int $productId, array $urls): void
    {
        $pdo = getDbConnection();
        $pdo->prepare('DELETE FROM product_images WHERE product_id = ?')->execute([$productId]);
        $urls = array_values(array_unique(array_filter(array_map('trim', $urls))));
        $ins = $pdo->prepare('INSERT INTO product_images (product_id, image_url, sort_order) VALUES (?, ?, ?)');
        foreach ($urls as $i => $url) {
            if ($url !== '') {
                $ins->execute([$productId, $url, $i]);
            }
        }
        $cover = $urls[0] ?? 'https://alinabradupozestorage.blob.core.windows.net/poze/Rectangle-1-5.png';
        $pdo->prepare('UPDATE products SET image = ? WHERE id = ?')->execute([$cover, $productId]);
    }

    public static function createProduct(array $data): int
    {
        $sql = 'INSERT INTO products (name, slug, description, price, category, category_slug, subcategory, subcategory_slug, size, image, featured_on_home, home_sort)
                VALUES (:name, :slug, :description, :price, :category, :category_slug, :subcategory, :subcategory_slug, :size, :image, :featured_on_home, :home_sort)';
        $stmt = getDbConnection()->prepare($sql);
        $stmt->execute([
            ':name' => $data['name'],
            ':slug' => $data['slug'],
            ':description' => $data['description'],
            ':price' => $data['price'],
            ':category' => $data['category'],
            ':category_slug' => $data['category_slug'],
            ':subcategory' => $data['subcategory'],
            ':subcategory_slug' => $data['subcategory_slug'],
            ':size' => $data['size'],
            ':image' => $data['image'],
            ':featured_on_home' => $data['featured_on_home'] ?? 0,
            ':home_sort' => $data['home_sort'] ?? 0,
        ]);
        return (int) getDbConnection()->lastInsertId();
    }

    public static function updateProduct(int $id, array $data): void
    {
        $sql = 'UPDATE products SET name = :name, slug = :slug, description = :description, price = :price,
                category = :category, category_slug = :category_slug, subcategory = :subcategory, subcategory_slug = :subcategory_slug,
                size = :size, featured_on_home = :featured_on_home, home_sort = :home_sort WHERE id = :id';
        $stmt = getDbConnection()->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':slug' => $data['slug'],
            ':description' => $data['description'],
            ':price' => $data['price'],
            ':category' => $data['category'],
            ':category_slug' => $data['category_slug'],
            ':subcategory' => $data['subcategory'],
            ':subcategory_slug' => $data['subcategory_slug'],
            ':size' => $data['size'],
            ':featured_on_home' => $data['featured_on_home'] ?? 0,
            ':home_sort' => $data['home_sort'] ?? 0,
        ]);
    }

    public static function deleteProduct(int $id): void
    {
        $stmt = getDbConnection()->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$id]);
    }

    /** Resetează toate steagurile, apoi marchează ID-urile date cu ordinea din $sortMap[id] = int */
    public static function saveHomepageSelection(array $featuredIds, array $sortByProductId): void
    {
        $pdo = getDbConnection();
        $pdo->exec('UPDATE products SET featured_on_home = 0, home_sort = 0');
        foreach ($featuredIds as $pid) {
            $pid = (int) $pid;
            if ($pid < 1) {
                continue;
            }
            $sort = (int) ($sortByProductId[$pid] ?? 0);
            $stmt = $pdo->prepare('UPDATE products SET featured_on_home = 1, home_sort = ? WHERE id = ?');
            $stmt->execute([$sort, $pid]);
        }
    }

    public static function slugExists(string $slug, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM products WHERE slug = :slug';
        $params = [':slug' => $slug];
        if ($exceptId !== null) {
            $sql .= ' AND id != :id';
            $params[':id'] = $exceptId;
        }
        $stmt = getDbConnection()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }
}
