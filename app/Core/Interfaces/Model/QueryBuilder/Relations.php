<?php
namespace App\Core\Interfaces\Model\QueryBuilder;

use PDO;

trait Relations {
   
    public function belongsTo($relatedModel, $foreignKey, $ownerKey)
    {
        $relatedModelInstance = new $relatedModel;
        $sql = "SELECT * FROM {$relatedModelInstance->getTableName()} WHERE {$ownerKey} = ?";
        $stmt = $this->getPDO()->prepare($sql);
        $stmt->bindValue(1, $this->{$foreignKey});
        $stmt->execute();

        return $stmt->fetchObject($relatedModel) ?: null;
    }

    private function hasOne($relatedModel, $foreignKey, $ownerKey)
    {
        $relatedModelInstance = new $relatedModel;
        $sql = "SELECT * FROM {$relatedModelInstance->getTableName()} WHERE {$foreignKey} = ?";
        $stmt = $this->getPDO()->prepare($sql);
        $stmt->bindValue(1, $this->{$ownerKey});
        $stmt->execute();

        return $stmt->fetchObject($relatedModel) ?: null;
    }

    public function hasMany($relatedModel, $foreignKey, $ownerKey)
    {
        $relatedModelInstance = new $relatedModel;
        $sql = "SELECT * FROM {$relatedModelInstance->getTableName()} WHERE {$foreignKey} = ?";
        $stmt = $this->getPDO()->prepare($sql);
        $stmt->bindValue(1, $this->{$ownerKey});
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS, $relatedModel);
    }

    public function belongsToMany($relatedModel, $junctionTable, $foreignPivotKey, $relatedPivotKey, $ownerKey, $relatedKey)
    {
        $relatedModelInstance = new $relatedModel;
        $sql = "SELECT * FROM {$relatedModelInstance->getTableName()} 
                INNER JOIN {$junctionTable} ON {$relatedModelInstance->getTableName()}.{$relatedKey} = {$junctionTable}.{$relatedPivotKey} 
                WHERE {$junctionTable}.{$foreignPivotKey} = ?";
        $stmt = $this->getPDO()->prepare($sql);
        $stmt->bindValue(1, $this->{$ownerKey});
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS, $relatedModel);
    }
}
