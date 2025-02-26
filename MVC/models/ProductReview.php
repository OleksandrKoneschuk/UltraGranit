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
        $result = Core::get()->db->insert(self::$tableName, [
            'product_id' => $productId,
            'user_name' => $userName,
            'rating' => $rating,
            'review_text' => $reviewText
        ]);
        return $result;
    }
}
