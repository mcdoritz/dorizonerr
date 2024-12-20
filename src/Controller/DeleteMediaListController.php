<?php

namespace App\Controller;

use App\Repository\MediaListRepository;
use App\Service\FileManager;
use App\Service\MediaListManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DeleteMediaListController extends AbstractController
{
    #[Route('/delete/mediaList/{id}', name: 'delete.mediaList', requirements: ['id' => '\d+'])]
    public function delete(Request $request, EntityManagerInterface $emi, MediaListRepository $mlr, MediaListManager $mediaListManager): Response
    {
        $mediaList = $mlr->findOneBy(['id' => $request->get('id')]);
        $mediaListManager->deleteMediaListFiles($mediaList);
        $mediaListManager->deleteMediaList($mediaList);

        $mediaListType = $mediaList->getType() == 0 ? 'playlist' : 'chaine';
        // Ajouter un message flash
        $this->addFlash('success', 'La '.$mediaListType.' a bien été supprimée.');

        return $this->redirectToRoute('index');
    }
}
