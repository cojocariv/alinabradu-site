<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

class OrderModel
{
    public static function create(array $customer, array $cart, float $total): int
    {
        $pdo = getDbConnection();
        $pdo->beginTransaction();
        try {
            $orderSql = 'INSERT INTO orders (full_name, phone, address, total_price, created_at)
                         VALUES (:full_name, :phone, :address, :total_price, NOW())';
            $orderStmt = $pdo->prepare($orderSql);
            $orderStmt->execute([
                ':full_name' => $customer['name'],
                ':phone' => $customer['phone'],
                ':address' => $customer['address'],
                ':total_price' => $total,
            ]);

            $orderId = (int) $pdo->lastInsertId();
            $itemSql = 'INSERT INTO order_items (order_id, product_id, product_name, size, quantity, unit_price)
                        VALUES (:order_id, :product_id, :product_name, :size, :quantity, :unit_price)';
            $itemStmt = $pdo->prepare($itemSql);

            foreach ($cart as $item) {
                $itemStmt->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $item['id'],
                    ':product_name' => $item['name'],
                    ':size' => $item['selected_size'],
                    ':quantity' => $item['qty'],
                    ':unit_price' => $item['price'],
                ]);
            }

            $pdo->commit();
            return $orderId;
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
