<?php

namespace MVC\models;

use core\Core;
use core\Model;
use core\Utils;

class Product extends Model
{
    public static $tableName = 'product';
    public static $photoTableName = 'product_photos';

    public static function getProduct()
    {
        return Core::get()->db->select(self::$tableName, '*');
    }

    public static function addProduct($name, $price, $shortDescription, $description, $visible, $categoryId)
    {
        return Core::get()->db->insert('product', [
            'name' => $name,
            'price' => $price,
            'short_description' => $shortDescription,
            'description' => $description,
            'visible' => $visible,
            'category_id' => $categoryId
        ]);
    }

    public static function updateProductMainPhoto($productId, $mainPhotoPath)
    {
        return Core::get()->db->update('product', ['main_photo' => $mainPhotoPath], ['id' => $productId]);
    }

    public static function getProducts($offset = 0, $limit = 10, $sort = 'name_asc')
    {
        $orderBy = self::getOrderByClause($sort);
        return Core::get()->db->select('product', '*', null, [
            'ORDER' => $orderBy,
            'LIMIT' => [$offset, $limit]
        ]);
    }

    public static function getProductsByCategory($categoryId, $offset = 0, $limit = 10, $sort = 'name_asc')
    {
        $orderBy = self::getOrderByClause($sort);
        return Core::get()->db->select('product', '*', ['category_id' => $categoryId], [
            'ORDER' => $orderBy,
            'LIMIT' => [$offset, $limit]
        ]);
    }

    private static function getOrderByClause($sort)
    {
        switch ($sort) {
            case 'name_asc':
                return ['name' => 'ASC'];
            case 'name_desc':
                return ['name' => 'DESC'];
            case 'price_asc':
                return ['price' => 'ASC'];
            case 'price_desc':
                return ['price' => 'DESC'];
            default:
                return ['name' => 'ASC'];
        }
    }

    public static function addProductMainPhoto($productId, $photoPath)
    {
        $directory = "files/products/{$productId}/";
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $fileName = 'main_' . uniqid() . '.jpg';
        $newPath = $directory . $fileName;

        if (move_uploaded_file($photoPath, $newPath)) {
            return $newPath;
        }
        return null;
    }

    public static function addProductPhoto($productId, $photoPath)
    {
        $result = Core::get()->db->insert(self::$photoTableName, [
            'product_id' => $productId,
            'photo_path' => $photoPath
        ]);
        if ($result) {
            error_log("Successfully inserted photo into database: " . $photoPath);
        } else {
            error_log("Failed to insert photo into database: " . $photoPath);
        }
        return $result;
    }

    public static function getProductsInCategory($categoryId, $offset = 0, $limit = 9)
    {
        return Core::get()->db->select('product', '*', ['category_id' => $categoryId], [
            'LIMIT' => [$offset, $limit]
        ]);
    }

    public static function getProductPhotos($productId)
    {
        return Core::get()->db->select(self::$photoTableName, '*', ['product_id' => $productId]);
    }

    public static function loadMoreProductsInCategory($categoryId, $offset, $limit)
    {
        return Core::get()->db->select('product', '*', ['category_id' => $categoryId], [
            'LIMIT' => [$offset, $limit]
        ]);
    }

    public static function getAllProducts()
    {
        return Core::get()->db->select(self::$tableName, '*');
    }

    public static function deleteProduct($id)
    {
        $product = self::getProductById($id);
        if (!$product) {
            return false;
        }

        $directory = "files/products/{$id}/";

        if (is_dir($directory)) {
            $files = glob($directory . '*', GLOB_MARK);
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($directory);
        }

        Core::get()->db->delete(self::$photoTableName, ['product_id' => $id]);
        Core::get()->db->delete(self::$tableName, ['id' => $id]);

        return true;
    }

    public static function updateProduct($id, $row)
    {
        $fieldsList = ['name', 'price', 'short_description', 'description', 'category_id', 'visible', 'main_photo'];
        $row = Utils::filterArray($row, $fieldsList);

        $currentProduct = self::getProductById($id);
        $currentMainPhoto = $currentProduct->main_photo;

        Core::get()->db->update(self::$tableName, $row, [
            'id' => $id
        ]);

        if (!empty($row['main_photo']) && $currentMainPhoto && $currentMainPhoto !== $row['main_photo']) {
            self::deletePhoto($currentMainPhoto);
        }
    }

    public static function deletePhoto($photoPath)
    {
        if (file_exists($photoPath)) {
            unlink($photoPath);
        }
    }

    public static function getProductById($id)
    {
        $rows = Core::get()->db->select(self::$tableName, '*', ['id' => $id]);
        if (!empty($rows)) {
            return $rows[0];
        }
        return null;
    }

    public static function getProductsWithPagination($limit, $offset)
    {
        return Core::get()->db->select(self::$tableName, '*', [
            'LIMIT' => [$offset, $limit]
        ]);
    }

    public static function getProductsInCategoryWithPagination($categoryId, $limit, $offset)
    {
        return Core::get()->db->select(self::$tableName, '*', [
            'category_id' => $categoryId,
            'LIMIT' => [$offset, $limit]
        ]);
    }

    public static function getFilteredProducts($filters, $sort, $offset = 0, $limit = 10)
    {
        $where = [];
        if (isset($filters['color'])) {
            $where['color'] = $filters['color'];
        }
        if (isset($filters['purpose'])) {
            $where['purpose'] = $filters['purpose'];
        }
        if (isset($filters['stone'])) {
            $where['stone'] = $filters['stone'];
        }

        $order = [];
        if ($sort === 'name_asc') {
            $order['ORDER'] = ['name' => 'ASC'];
        } elseif ($sort === 'name_desc') {
            $order['ORDER'] = ['name' => 'DESC'];
        } elseif ($sort === 'price_asc') {
            $order['ORDER'] = ['price' => 'ASC'];
        } elseif ($sort === 'price_desc') {
            $order['ORDER'] = ['price' => 'DESC'];
        }

        return Core::get()->db->select(self::$tableName, '*', $where, array_merge($order, [
            'LIMIT' => [$offset, $limit]
        ]));
    }
}