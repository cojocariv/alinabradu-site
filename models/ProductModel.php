<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/CategoryModel.php';

class ProductModel
{
    public const HOME_SORT_FEATURED = 1;
    public const HOME_SORT_DEFAULT = 9999;

    /** Ordine homepage: 1 = piesa evidențiată; 2, 3… pentru restul; gol/0 → 9999. */
    public static function normalizeHomeSort(int $sort, bool $featured): int
    {
        if (!$featured) {
            return 0;
        }
        if ($sort < 1) {
            return self::HOME_SORT_DEFAULT;
        }
        return $sort;
    }

    public static function displayHomeSort(int $sort, bool $featured): int
    {
        if (!$featured) {
            return 0;
        }
        return $sort > 0 ? $sort : self::HOME_SORT_DEFAULT;
    }

    private static function homeSortOrderSql(): string
    {
        return 'CASE WHEN home_sort < 1 THEN 999999 ELSE home_sort END ASC, id DESC';
    }

    private static function publicWhereInStock(): string
    {
        return ' AND in_stock = 1';
    }

    public static function featured(int $limit = 8): array
    {
        $sql = 'SELECT * FROM products WHERE 1=1' . self::publicWhereInStock() . ' ORDER BY id DESC LIMIT :limit';
        $stmt = getDbConnection()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Produse bifate în admin pentru homepage (toate, în ordinea setată).
     * Fără limită de număr — lista e controlată din admin.
     */
    public static function featuredHome(?int $limit = null): array
    {
        $sql = 'SELECT * FROM products WHERE featured_on_home = 1 ORDER BY ' . self::homeSortOrderSql();
        if ($limit !== null && $limit > 0) {
            $sql .= ' LIMIT :limit';
            $stmt = getDbConnection()->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        }
        return getDbConnection()->query($sql)->fetchAll();
    }

    public static function countInCategory(string $name, string $slug): int
    {
        $stmt = getDbConnection()->prepare(
            'SELECT COUNT(*) FROM products
             WHERE category_slug = :slug
                OR category = :cat_name
                OR TRIM(category) = TRIM(:cat_name_trim)'
        );
        $stmt->execute([
            ':slug' => $slug,
            ':cat_name' => $name,
            ':cat_name_trim' => $name,
        ]);
        return (int) $stmt->fetchColumn();
    }

    public static function countAll(): int
    {
        return (int) getDbConnection()->query('SELECT COUNT(*) FROM products')->fetchColumn();
    }

    /**
     * Categorii pentru magazin cu număr produse (un singur query, fără erori în șablon).
     *
     * @return list<array{name:string,slug:string,count:int}>
     */
    public static function shopCategoryOptions(): array
    {
        $rows = CategoryModel::all();
        if ($rows === []) {
            $rows = [
                ['name' => 'Bluză', 'slug' => 'bluze'],
                ['name' => 'Fustă', 'slug' => 'fuste'],
                ['name' => 'Home decor', 'slug' => 'home-decor'],
                ['name' => 'Rochie', 'slug' => 'rochii'],
            ];
        }

        $countsBySlug = [];
        $stmt = getDbConnection()->query(
            "SELECT category_slug AS slug, MAX(category) AS name, COUNT(*) AS cnt
             FROM products
             WHERE category_slug IS NOT NULL AND TRIM(category_slug) <> ''
             GROUP BY category_slug"
        );
        foreach ($stmt->fetchAll() as $row) {
            $countsBySlug[(string) $row['slug']] = (int) $row['cnt'];
        }

        $options = [];
        $seenSlugs = [];
        foreach ($rows as $cat) {
            $name = (string) ($cat['name'] ?? '');
            $slug = (string) ($cat['slug'] ?? '');
            if ($slug === '') {
                continue;
            }
            $count = $countsBySlug[$slug] ?? 0;
            if ($count === 0 && $name !== '') {
                $count = self::countInCategory($name, $slug);
            }
            $options[] = ['name' => $name, 'slug' => $slug, 'count' => $count];
            $seenSlugs[$slug] = true;
        }

        foreach ($countsBySlug as $slug => $count) {
            if (isset($seenSlugs[$slug])) {
                continue;
            }
            $nameStmt = getDbConnection()->prepare(
                'SELECT category FROM products WHERE category_slug = :slug AND TRIM(category) <> "" LIMIT 1'
            );
            $nameStmt->execute([':slug' => $slug]);
            $name = (string) ($nameStmt->fetchColumn() ?: $slug);
            $options[] = ['name' => $name, 'slug' => $slug, 'count' => $count];
        }

        usort($options, static fn (array $a, array $b): int => strcasecmp($a['name'], $b['name']));

        return $options;
    }

    public static function countBySubcategory(string $value, string $slug = ''): int
    {
        $sql = 'SELECT COUNT(*) FROM products WHERE subcategory = :val';
        $params = [':val' => $value];
        if ($slug !== '') {
            $sql .= ' OR subcategory_slug = :slug';
            $params[':slug'] = $slug;
        }
        $stmt = getDbConnection()->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public static function countBySize(string $size): int
    {
        $stmt = getDbConnection()->prepare('SELECT COUNT(*) FROM products WHERE FIND_IN_SET(:size, size)');
        $stmt->execute([':size' => $size]);
        return (int) $stmt->fetchColumn();
    }

    public static function filter(array $filters = []): array
    {
        $sql = 'SELECT * FROM products WHERE 1=1';
        $params = [];

        $categories = $filters['categories'] ?? [];
        if ($categories === [] && !empty($filters['category'])) {
            $categories = [(string) $filters['category']];
        }
        if ($categories !== []) {
            $parts = [];
            foreach (array_values($categories) as $i => $cat) {
                $cat = trim((string) $cat);
                if ($cat === '') {
                    continue;
                }
                $slugKey = ':filter_cat_slug_' . $i;
                $nameKey = ':filter_cat_name_' . $i;
                $parts[] = "(category_slug = {$slugKey} OR category = {$nameKey})";
                $params[$slugKey] = $cat;
                $params[$nameKey] = $cat;
            }
            if ($parts !== []) {
                $sql .= ' AND (' . implode(' OR ', $parts) . ')';
            }
        }

        $subcategories = $filters['subcategories'] ?? [];
        if ($subcategories === [] && !empty($filters['subcategory'])) {
            $subcategories = [(string) $filters['subcategory']];
        }
        if ($subcategories !== []) {
            $parts = [];
            foreach (array_values($subcategories) as $i => $sub) {
                if (!is_array($sub)) {
                    $sub = ['value' => trim((string) $sub), 'slug' => ''];
                }
                $val = trim((string) ($sub['value'] ?? ''));
                $slug = trim((string) ($sub['slug'] ?? ''));
                if ($val === '' && $slug === '') {
                    continue;
                }
                $valKey = ':sub_val_' . $i;
                $slugKey = ':sub_slug_' . $i;
                if ($val !== '' && $slug !== '') {
                    $parts[] = "(subcategory = {$valKey} OR subcategory_slug = {$slugKey})";
                    $params[$valKey] = $val;
                    $params[$slugKey] = $slug;
                } elseif ($val !== '') {
                    $parts[] = "subcategory = {$valKey}";
                    $params[$valKey] = $val;
                } else {
                    $parts[] = "subcategory_slug = {$slugKey}";
                    $params[$slugKey] = $slug;
                }
            }
            if ($parts !== []) {
                $sql .= ' AND (' . implode(' OR ', $parts) . ')';
            }
        }

        $sizes = $filters['sizes'] ?? [];
        if ($sizes === [] && !empty($filters['size'])) {
            $sizes = [(string) $filters['size']];
        }
        if ($sizes !== []) {
            $parts = [];
            foreach (array_values($sizes) as $i => $size) {
                $size = trim((string) $size);
                if ($size === '') {
                    continue;
                }
                $key = ':size_' . $i;
                $parts[] = "FIND_IN_SET({$key}, size)";
                $params[$key] = $size;
            }
            if ($parts !== []) {
                $sql .= ' AND (' . implode(' OR ', $parts) . ')';
            }
        }

        $search = trim((string) ($filters['q'] ?? $filters['search'] ?? ''));
        if ($search !== '') {
            $sql .= ' AND name LIKE :search_q';
            $params[':search_q'] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY id DESC';
        $stmt = getDbConnection()->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $rows = $stmt->fetchAll();

        return self::sortShopProducts($rows, (string) ($filters['sort'] ?? ''), $filters);
    }

    /**
     * Sortare magazin: nume, preț sau ordine aleatoare stabilă (per sesiune + filtre).
     *
     * @param array<string, mixed> $filterContext
     */
    private static function sortShopProducts(array $products, string $sort, array $filterContext): array
    {
        if ($products === []) {
            return $products;
        }

        $sort = trim($sort);
        switch ($sort) {
            case 'name':
            case 'name_asc':
                usort($products, static fn (array $a, array $b): int => strcasecmp((string) $a['name'], (string) $b['name']));
                return $products;
            case 'name_desc':
                usort($products, static fn (array $a, array $b): int => strcasecmp((string) $b['name'], (string) $a['name']));
                return $products;
            case 'price':
            case 'price_asc':
                usort($products, static fn (array $a, array $b): int => (float) $a['price'] <=> (float) $b['price']);
                return $products;
            case 'price_desc':
                usort($products, static fn (array $a, array $b): int => (float) $b['price'] <=> (float) $a['price']);
                return $products;
            default:
                return self::shuffleShopProductsStable($products, $filterContext);
        }
    }

    /** @param array<string, mixed> $filterContext */
    private static function shuffleShopProductsStable(array $products, array $filterContext): array
    {
        if (count($products) < 2) {
            return $products;
        }

        $fingerprint = json_encode([
            'categories' => $filterContext['categories'] ?? [],
            'subcategories' => array_map(
                static fn ($s) => is_array($s) ? ($s['slug'] ?? $s['value'] ?? '') : $s,
                $filterContext['subcategories'] ?? []
            ),
            'sizes' => $filterContext['sizes'] ?? [],
            'q' => trim((string) ($filterContext['q'] ?? '')),
        ], JSON_THROW_ON_ERROR);

        $sessionKey = 'shop_rand_' . md5($fingerprint);
        if (empty($_SESSION[$sessionKey])) {
            $_SESSION[$sessionKey] = random_int(1, 999_999_999);
        }
        $seed = (int) $_SESSION[$sessionKey];

        usort($products, static function (array $a, array $b) use ($seed): int {
            $ha = crc32((string) ($a['id'] ?? '') . '|' . $seed);
            $hb = crc32((string) ($b['id'] ?? '') . '|' . $seed);
            return $ha <=> $hb;
        });

        return $products;
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
        $sql = 'SELECT * FROM products WHERE category = :category AND id != :id' . self::publicWhereInStock() . ' ORDER BY id DESC LIMIT :limit';
        $stmt = getDbConnection()->prepare($sql);
        $stmt->bindValue(':category', $category);
        $stmt->bindValue(':id', $productId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function allForAdmin(): array
    {
        $sql = 'SELECT * FROM products ORDER BY featured_on_home DESC, ' . self::homeSortOrderSql();
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
        $sql = 'INSERT INTO products (name, slug, description, price, category, category_slug, subcategory, subcategory_slug, size, image, featured_on_home, home_sort, in_stock)
                VALUES (:name, :slug, :description, :price, :category, :category_slug, :subcategory, :subcategory_slug, :size, :image, :featured_on_home, :home_sort, :in_stock)';
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
            ':home_sort' => self::normalizeHomeSort((int) ($data['home_sort'] ?? 0), !empty($data['featured_on_home'])),
            ':in_stock' => $data['in_stock'] ?? 1,
        ]);
        return (int) getDbConnection()->lastInsertId();
    }

    public static function updateProduct(int $id, array $data): void
    {
        $sql = 'UPDATE products SET name = :name, slug = :slug, description = :description, price = :price,
                category = :category, category_slug = :category_slug, subcategory = :subcategory, subcategory_slug = :subcategory_slug,
                size = :size, featured_on_home = :featured_on_home, home_sort = :home_sort, in_stock = :in_stock WHERE id = :id';
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
            ':home_sort' => self::normalizeHomeSort((int) ($data['home_sort'] ?? 0), !empty($data['featured_on_home'])),
            ':in_stock' => $data['in_stock'] ?? 1,
        ]);
    }

    public static function deleteProduct(int $id): void
    {
        $stmt = getDbConnection()->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$id]);
    }

    /** Scoate toate produsele din blocul „Top produse” de pe homepage. */
    public static function clearHomepageSelection(): void
    {
        getDbConnection()->exec('UPDATE products SET featured_on_home = 0, home_sort = 0');
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
            $sort = self::normalizeHomeSort((int) ($sortByProductId[$pid] ?? 0), true);
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

    public static function setInStock(int $id, int $inStock): void
    {
        if ($id < 1) {
            return;
        }
        $stmt = getDbConnection()->prepare('UPDATE products SET in_stock = ? WHERE id = ?');
        $stmt->execute([$inStock ? 1 : 0, $id]);
    }
}
