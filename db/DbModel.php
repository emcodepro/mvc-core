<?php
/**
 * Created by PhpStorm.
 * User: grand
 * Date: 06-Mar-21
 * Time: 10:39
 */

namespace app\core\db;

use app\core\Application;

abstract class DbModel extends \app\core\Model
{
    abstract public static function tableName(): string;

    abstract public static function attributes(): array;


    public function save(){
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $bindAttributes = implode(",", array_map(fn($el) => ":$el", $attributes));

        $statement = self::prepare("INSERT INTO $tableName (".implode(",", $attributes).") VALUES (".$bindAttributes.")");
        foreach ($attributes as $attribute){
            $statement->bindValue(":$attribute", $this->{$attribute});
        }
        $statement->execute();
        return true;
    }

    public static function findOne(array $where)
    {
       $tableName = static::tableName();
       $attributes = array_keys($where);

       $whereArray = implode(" AND ", array_map(function($el){
           return "$el = :$el";
       }, $attributes));
       $statement = self::prepare("SELECT * FROM $tableName WHERE $whereArray");
       foreach ($where as $key=>$value){
           $statement->bindValue(":$key", $value);
       }
       $statement->execute();

       return $statement->fetchObject(static::class);
    }

    public static function prepare($sql)
    {
        return Application::$app->db->pdo->prepare($sql);
    }
}