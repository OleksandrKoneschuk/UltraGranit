<?php
namespace MVC\models;

use core\Core;
use core\Model;

class ProductReview extends Model
{
    public static $tableName = 'product_reviews';

    public static function getReviewsByProductId($productId)
    {
        return Core::get()->db->select(self::$tableName, '*', ['product_id' => $productId], ['ORDER' => ['created_at' => 'DESC']]);
    }

    public static function addReview($productId, $userName, $rating, $reviewText = null)
    {
        return Core::get()->db->insert(self::$tableName, [
            'product_id' => $productId,
            'user_name' => $userName,
            'rating' => $rating,
            'review_text' => $reviewText,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public static function getReviewById($reviewId)
    {
        return Core::get()->db->select(self::$tableName, '*', ['id' => $reviewId]);
    }

    public static function deleteReview($reviewId)
    {
        return Core::get()->db->delete(self::$tableName, ['id' => $reviewId]);
    }
}
