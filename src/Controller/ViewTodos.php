<?php

namespace App\Controller;

use App\Service\TodoView;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ViewTodos extends AbstractController
{
    /**
     * Home
     *
     * @Route(path="/", name="homepage")
     *
     * @param TodoView $todoView
     * @return Response
     */
    public function view(TodoView $todoView): Response
    {
        return $this->render('base.html.twig', [
            'todo' => $todoView
        ]);
    }
}