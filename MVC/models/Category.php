<?php

namespace MVC\models;

use core\Core;
use core\Model;

/**
 * @property int $id Id
 * @property string $name Назва категорії
 * @property string $photo Фото категорії
 */
class Category extends Model
{
    public static $tableName = 'category';

    public static function addCategory($name, $photoPath)
    {
        $category = new Category();

        do {
            $fileName = uniqid() . '.jpg';
            $newPath = "files/category/{$fileName}";
        } while (file_exists($newPath));

        move_uploaded_file($photoPath, $newPath);

        $category->name = $name;
        $category->photo = $fileName;
        $category->save();
    }

    public static function changePhoto($id, $newPhoto)
    {
        $row = self::getCategoryById($id);
        $photoPath = 'files/category/' . $row->photo;

        if (is_file($photoPath))
            unlink($photoPath);

        do {
            $fileName = uniqid() . '.jpg';
            $newPath = "files/category/{$fileName}";
        } while (file_exists($newPath));

        move_uploaded_file($newPhoto, $newPath);

        Core::get()->db->update(self::$tableName, [
            'photo' => $fileName
        ], [
            'id' => $id
        ]);
    }

    public static function getCategoryById($id)
    {
        $rows = Core::get()->db->select(self::$tableName, '*', [
            'id' => $id
        ]);
        if (!empty($rows))
            return (object) $rows[0];
        else
            return null;
    }

    public static function deleteCategory($id)
    {
        Core::get()->db->delete(self::$tableName, [
            'id' => $id
        ]);
    }

    public static function updateCategory($id, $newName)
    {
        Core::get()->db->update(self::$tableName, [
            'name' => $newName
        ], [
            'id' => $id
        ]);
    }

    public static function getCategories()
    {
        $rows = Core::get()->db->select(self::$tableName);
        $arrayRows = [];
        foreach ($rows as $row) {
            $arrayRows[] = (array)$row;
        }
        return $arrayRows;
    }
}