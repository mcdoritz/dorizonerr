<?php

namespace App\Controller;

use App\Repository\MediaListRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ArchMediaListController extends AbstractController
{
    #[Route('/arch/mediaList/{id}', name: 'arch.mediaList', requirements: ['id' => '\d+'])]
    public function archive(Request $request, EntityManagerInterface $emi, MediaListRepository $mlr): Response
    {
        $mediaList = $mlr->findOneBy(['id' => $request->get('id')]);
        $action = 'activée';
        if($mediaList->isArchived()) {
            $mediaList->setArchived(false);
        } else{
            $action = 'archivée';
            $mediaList->setArchived(true);
        }


        $emi->persist($mediaList);
        $emi->flush();

        $mediaListType = $mediaList->getType() == 0 ? 'playlist' : 'chaine';

        // Ajouter un message flash
        $this->addFlash('success', 'La '.$mediaListType.' a bien été '. $action);

        return $this->redirectToRoute('show.mediaList', ['id' => $mediaList->getId()]);
    }
}
