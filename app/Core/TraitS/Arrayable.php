<?php

namespace App\Core\TraitS;

trait Arrayable
{

    public function toArray(): array
    {
        $data = [];
        foreach ($this->toArray as $property) {
            $data[$property] = $this->{$property};
        }
        return $data;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function __toString()
    {
        return json_encode($this);
    }



}
