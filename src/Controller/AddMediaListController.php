<?php

namespace App\Controller;

use App\Entity\MediaList;
use App\Form\AddMediaListType;
use App\Repository\MediaListRepository;
use App\Service\FileManager;
use App\Service\MediaListManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Attribute\Route;

class AddMediaListController extends AbstractController
{
    #[Route('/add/mediaList', name: 'add.mediaList')]
    public function add(Request $request, EntityManagerInterface $emi, MediaListManager $mediaListManager, FileManager $fileManager): Response
    {
        $mediaList = new MediaList();
        $form = $this->createForm(AddMediaListType::class, $mediaList);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $mediaListManager->configurePath($mediaList);
            $mediaListManager->getMediaListInfos($mediaList);
            $mediaListManager->downloadMediaListInfos($mediaList);

            $mediaListManager->copyPoster($mediaList);
            $emi->persist($mediaList);
            $emi->flush();

            // Ajouter un message flash
            $this->addFlash('success', 'La '.$mediaList->getType() == 0 ? 'playlist' : 'chaîne' .' a bien été ajoutée !');
            return $this->redirectToRoute('show.mediaList', ['id' => $mediaList->getId()]);
        }
        return $this->render('add_mediaList.html.twig', [
            'controller_name' => 'MediaListController',
            'form' => $form->createView(),
        ]);
    }
}
