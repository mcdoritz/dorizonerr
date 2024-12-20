<?php

namespace App\Controller;

use App\Repository\MediaListRepository;
use App\Service\MediaListManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArchMediaListController extends AbstractController
{
    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/arch/mediaList/{id}', name: 'arch.mediaList', requirements: ['id' => '\d+'])]
    public function archive(Request $request, MediaListRepository $mlr, MediaListManager $mediaListManager): Response
    {
        $mediaList = $mlr->findOneBy(['id' => $request->get('id')]);
        $mediaListManager->archiveMediaList($mediaList);

        // Ajouter un message flash
        $mediaListType = $mediaList->getType() == 0 ? 'playlist' : 'chaine';
        $action = $mediaList->isArchived() ? 'archivée' : 'activée';
        $this->addFlash('success', 'La '.$mediaListType.' a bien été '. $action);

        return $this->redirectToRoute('show.mediaList', ['id' => $mediaList->getId()]);
    }
}
