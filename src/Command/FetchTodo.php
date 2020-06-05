<?php

namespace App\Command;

use App\Entity\Todo;
use App\Providers\OneProvider;
use App\Providers\ProviderInterface;
use App\Providers\TwoProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Load Todo to DB
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
class FetchTodo extends Command
{
    protected static $defaultName = 'app:fetch-todo';

    /**
     * @required EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ProviderInterface
     */
    private $provider;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this->setDescription('Todo listesini DB\'yükler');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $providers = [OneProvider::class, TwoProvider::class];
        foreach ($providers as $provider) {
            $todos = (new $provider())->getTodos();

            foreach ($todos as $todo) {
                $todo = (new Todo())
                    ->setTitle($todo['id'])
                    ->setDifficulty($todo['difficulty'])
                    ->setEstimateTime($todo['time']);

                $this->entityManager->persist($todo);
            }
        }

        // Save
        $this->entityManager->flush();

        // End
        return Command::SUCCESS;
    }
}