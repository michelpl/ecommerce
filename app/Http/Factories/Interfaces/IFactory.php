<?php

namespace App\Http\Factories\Interfaces;

interface IFactory
{
    public function createFromJson(object $object);
}
