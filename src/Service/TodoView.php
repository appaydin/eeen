<?php

namespace App\Service;

use App\Entity\Developer;
use App\Entity\Todo;
use App\Repository\DeveloperRepository;
use App\Repository\TodoRepository;

class TodoView
{
    /**
     * @var DeveloperRepository
     */
    private $developerRepo;
    /**
     * @var TodoRepository
     */
    private $todoRepo;

    private $tasks = [];

    private $taskPersist = false;

    private $devDayLoads;

    public function __construct(DeveloperRepository $developerRepo, TodoRepository $todoRepo)
    {
        $this->developerRepo = $developerRepo;
        $this->todoRepo = $todoRepo;
    }

    /**
     * Get Total Estimate Count
     *
     * @return array
     */
    public function getTotalEstimate(): array
    {
        return [
            'week' => round(floor($this->todoRepo->getWorkLoad() / $this->developerRepo->getHourlyWorkLoad() / Developer::WORK_HOUR) / 5),
            'day' => floor($this->todoRepo->getWorkLoad() / $this->developerRepo->getHourlyWorkLoad() / Developer::WORK_HOUR),
            'hour' => round($this->todoRepo->getWorkLoad() / $this->developerRepo->getHourlyWorkLoad()) % Developer::WORK_HOUR
        ];
    }

    /**
     * Get Week Days
     *
     * @return array
     */
    public function getWeekDay(): array
    {
        $days = [];
        for ($i = 1; $i <= (int)$this->getTotalEstimate()['day']; $i++) {
            $day = $i % 5;
            if ($day === 0) {
                $days[] = 'Cuma';
            } elseif ($day === 4) {
                $days[] = 'Perşembe';
            } elseif ($day === 3) {
                $days[] = 'Çarşamba';
            } elseif ($day === 2) {
                $days[] = 'Salı';
            } elseif ($day === 1) {
                $days[] = 'Pazartesi';
            }
        }

        return $days;
    }

    /**
     * Get Developer List
     *
     * @return Developer[]
     */
    public function getDevelopers(): ?array
    {
        return $this->developerRepo->findAll();
    }

    /**
     * Get Task for Developer
     *
     * @param Developer $developer
     * @param int $weekNumber
     * @param int $hours
     *
     * @return Todo|array|null
     */
    public function getTask(Developer $developer, int $weekNumber)
    {
        // Load Tasks
        if (!$this->taskPersist) {
            $this->taskPersist = true;
            foreach ($this->todoRepo->findAll() as $todo) {
                $this->tasks[$todo->getDifficulty()][] = [
                    'id' => $todo->getTitle(),
                    'difficulty' => $todo->getDifficulty(),
                ];
            }
        }

        // Work Load
        $remainLoad = (Developer::WORK_HOUR * $developer->getDifficulty()) - ($this->devDayLoads[$developer->getId()][$weekNumber] ?? 0);
        if ($remainLoad > 0) {
            $maxLoad = $remainLoad > 5 ? 5 : $remainLoad;

            $task = $this->selectTodo($maxLoad);

            if ($task) {
                $difficulty = $task['difficulty'];
                $remainLoad -= $difficulty;

                // Append More
                if ($difficulty < $developer->getDifficulty() && $remainLoad) {
                    $moreTask = $this->selectTodo($developer->getDifficulty() - $difficulty);

                    if ($moreTask) {
                        $difficulty += $moreTask['difficulty'];
                    }
                }

                $this->devDayLoads[$developer->getId()][$weekNumber] = ($this->devDayLoads[$developer->getId()][$weekNumber] ?? 0) + $difficulty;
                return isset($moreTask) ? [$task, $moreTask] : [$task];
            }
        }

        return null;
    }

    private function selectTodo(int $maxLoad)
    {
        if ($this->tasks && !count($this->tasks[$maxLoad])) {
            if ($maxLoad - 1 === 0) {
                return null;
            }

            return $this->selectTodo($maxLoad - 1);
        }

        return array_pop($this->tasks[$maxLoad]);
    }

    /**
     * Get Developer Day Load
     *
     * @param Developer $developer
     * @param int $weekNumber
     * @return int
     */
    public function getTotalDayLoad(Developer $developer, int $weekNumber): int
    {
        return $this->devDayLoads[$developer->getId()][$weekNumber] ?? 0;
    }
}