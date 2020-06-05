<?php

namespace App\Providers;

/**
 * One Provider
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
class TwoProvider implements ProviderInterface
{
    public function getTodos(): array
    {
        // Fetch Data
        $data = file_get_contents('http://www.mocky.io/v2/5d47f235330000623fa3ebf7');

        if (!empty($data)) {
            $data = json_decode($data, true);
            $newData = [];

            foreach ($data as $index => $todo) {
                $newData[] = [
                    'id' => key($todo),
                    'difficulty' => $todo[key($todo)]['level'],
                    'time' => $todo[key($todo)]['estimated_duration'],
                ];
            }
        }

        return $newData ?? [];
    }
}