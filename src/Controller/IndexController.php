<?php

namespace App\Controller;

use App\Repository\MediaListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, MediaListRepository $mlr): Response
    {
        $playLists = $mlr->findBy(['type' => 0, 'archived' => false]);
        $channels = $mlr->findBy(['type' => 1, 'archived' => false]);
        //dd($mediaList);

        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'playLists' => $playLists,
            'channels' => $channels
        ]);
    }
}
