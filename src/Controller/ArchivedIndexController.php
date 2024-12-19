<?php

namespace App\Controller;

use App\Repository\MediaListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArchivedIndexController extends AbstractController
{
    #[Route('/archivedIndex', name: 'index.archived')]
    public function index(Request $request, MediaListRepository $mlr): Response
    {
        $playLists = $mlr->findBy(['type' => 0, 'archived' => true]);
        $channels = $mlr->findBy(['type' => 1, 'archived' => true]);

        return $this->render('index.html.twig', [
            'controller_name' => 'IndexController',
            'playLists' => $playLists,
            'channels' => $channels,
            'archive' => true
        ]);
    }
}
