<?php

namespace App\Providers;

/**
 * Todo Provider Interface
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
interface ProviderInterface
{
    public function getTodos(): array;
}