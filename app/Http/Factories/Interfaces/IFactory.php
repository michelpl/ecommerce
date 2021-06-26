<?php

namespace App\Http\Factories\Interfaces;

interface IFactory
{
    public function createFromObject(object $item): array;
    public function createFromArray(array $item): array;
}
