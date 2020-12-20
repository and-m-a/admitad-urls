<?php


namespace App\Entity;


abstract class BaseEntity
{
    /**
     * @TODO define common implementation for this method
     *
     * @return array
     */
    abstract public function toArray(): array;
}