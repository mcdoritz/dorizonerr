<?php

namespace App\Controller;

use App\Form\AddMediaListType;
use App\Repository\MediaListRepository;
use App\Service\MediaListManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EditMediaListController extends AbstractController
{
    #[Route('/edit/mediaList/{id}', name: 'edit.mediaList', requirements: ['id' => '\d+'])]
    public function edit(Request $request, EntityManagerInterface $emi, MediaListRepository $mlr, MediaListManager $mediaListManager): Response
    {

        $mediaList = $mlr->findOneBy(['id' => $request->get('id')]);
        $form = $this->createForm(AddMediaListType::class, $mediaList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mediaListManager->persistMediaList($mediaList);

            // Ajouter un message flash
            $mediaListType = $mediaList->getType() == 0 ? 'playlist' : 'chaine';
            $this->addFlash('success', 'La '.$mediaListType.' a bien été modifiée.');
            return $this->redirectToRoute('show.mediaList', ['id' => $mediaList->getId()]);
        }

        return $this->render('edit_mediaList.html.twig', [
            'controller_name' => 'EditMediaListController',
            'form' => $form->createView(),
        ]);
    }
}
