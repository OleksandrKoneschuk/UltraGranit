<?php

namespace MVC\models;

use core\Core;
use core\Model;

/**
 * @property string $title Заголовок новини
 * @property string $text Текст новини новини
 * @property string $short_text Короткий текст новини
 * @property string $date Дата та час новини
 * @property int $id ID новини
 */
class News extends Model
{
    public static $tableName = 'news';
}