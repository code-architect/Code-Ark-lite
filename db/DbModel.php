<?php


namespace codearchitect\codearklite\db;

use codearchitect\codearklite\Application;
use codearchitect\codearklite\Model;

/**
 * Class DbModel
 * @author: Indranil Samanta (code-architect)
 * @package codearchitect\codearklite
 */
abstract class DbModel extends Model
{
    /**
     * The database table name
     * @return string
     */
    abstract public function tableName(): string;

    abstract public function attributes(): array;

    abstract public function primaryKey(): string;

    public function save()
    {
        $tableName = $this->tableName();
        $attributes = $this->attributes();
        $params = array_map(fn($attr) => ":$attr", $attributes);
        $statement = self::prepare("INSERT INTO $tableName (".implode(',', $attributes).")
                                    VALUES(".implode(',', $params).")");
        foreach ($attributes as $attribute)
        {
            $statement->bindValue(":$attribute", $this->{$attribute});
        }
        $statement->execute();
        return true;
    }

    public function findOne($where)
    {
        $tableName = static::tableName();
        $attributes = array_keys($where);
        $sql = implode("AND", array_map(fn($attr) => "$attr = :$attr", $attributes));
        $statement = self::prepare("SELECT * FROM $tableName WHERE $sql");
        foreach($where as $key => $item){
            $statement->bindValue(":$key", $item);
        }
        $statement->execute();
        return $statement->fetchObject(static::class);
    }


    public static function prepare($sql)
    {
        return Application::$app->db->pdo->prepare($sql);
    }
}