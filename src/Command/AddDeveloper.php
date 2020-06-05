<?php

namespace App\Command;

use App\Entity\Developer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Create New Developer
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
class AddDeveloper extends Command
{
    protected static $defaultName = 'app:add-developer';

    /**
     * @required EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this->setDescription('Yeni developer oluşturur.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        // Get Name
        $question = new Question('Geliştirici Adı:', 'Ramazan');
        $name = $helper->ask($input, $output, $question);

        // Get Difficulty
        $question = new ChoiceQuestion('Zorluk Seviyesi:', [1, 2, 3, 4, 5], 1);
        $difficulty = $helper->ask($input, $output, $question);

        // Save
        $developer = (new Developer())->setName($name)->setDifficulty($difficulty);
        $this->entityManager->persist($developer);
        $this->entityManager->flush();

        // End
        return Command::SUCCESS;
    }
}