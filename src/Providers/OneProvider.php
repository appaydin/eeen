<?php

namespace App\Providers;

/**
 * One Provider
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
class OneProvider implements ProviderInterface
{
    public function getTodos(): array
    {
        // Fetch Data
        $data = file_get_contents('http://www.mocky.io/v2/5d47f24c330000623fa3ebfa');

        if (!empty($data)) {
            $data = json_decode($data, true);

            foreach ($data as $index => $todo) {
                $data[$index] = [
                    'id' => $todo['id'],
                    'difficulty' => $todo['zorluk'],
                    'time' => $todo['sure'],
                ];
            }
        }

        return $data ?? [];
    }
}