<?php
namespace App\Core\Interfaces\Model\QueryBuilder;

trait Conditions
{
    private function addCondition(string $column, string $value, string $operator, string $conditionType): self
    {
        // Initialize $sql if it's not yet defined (to avoid the error)
        if (!isset($this->sql)) {
            $this->sql = "SELECT * FROM {$this->table}";
            $this->whereUsed = false;
        }

        // Use WHERE for the first condition and $conditionType for subsequent conditions
        $this->sql .= $this->whereUsed ? " $conditionType " : " WHERE ";
        $this->sql .= "$column $operator ?";
        $this->bindings[] = $value;

        // Mark that WHERE has been used
        $this->whereUsed = true;

        return $this;
    }

    public function where(string $column, string $value, string $operator = '='): self
    {
        return $this->addCondition($column, $value, $operator, 'AND');
    }

    public function orWhere(string $column, string $value, string $operator = '='): self
    {
        return $this->addCondition($column, $value, $operator, 'OR');
    }

    public function between(string $column, $value1, $value2): self
    {
        if (!isset($this->sql)) {
            $this->sql = "SELECT * FROM {$this->table}";
            $this->whereUsed = false;
        }

        $this->sql .= $this->whereUsed ? " AND " : " WHERE ";
        $this->sql .= "$column BETWEEN ? AND ?";
        array_push($this->bindings, $value1, $value2);

        $this->whereUsed = true;

        return $this;
    }

    public function leftJoin(string $table, string $first, string $operator, string $second): self
    {
        $this->sql .= " LEFT JOIN $table ON $first $operator $second";
        return $this;
    }

    public function innerJoin(string $table, string $first, string $operator, string $second): self
    {
        $this->sql .= " INNER JOIN $table ON $first $operator $second";
        return $this;
    }

    public function rightJoin(string $table, string $first, string $operator, string $second): self
    {
        $this->sql .= " RIGHT JOIN $table ON $first $operator $second";
        return $this;
    }

    public function crossJoin(string $table): self
    {
        $this->sql .= " CROSS JOIN $table";
        return $this;
    }

    public function union(self $model): self
    {
        $this->sql .= " UNION {$model->sql}";
        $this->bindings = array_merge($this->bindings, $model->bindings);
        return $this;
    }

    public function having(string $column, string $operator, string $value): self
    {
        $this->sql .= " HAVING $column $operator ?";
        $this->bindings[] = $value;
        return $this;
    }

}
