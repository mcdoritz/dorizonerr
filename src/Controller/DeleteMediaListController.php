<?php

namespace App\Controller;

use App\Repository\MediaListRepository;
use App\Service\FileManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DeleteMediaListController extends AbstractController
{
    #[Route('/delete/mediaList/{id}', name: 'delete.mediaList', requirements: ['id' => '\d+'])]
    public function delete(Request $request, EntityManagerInterface $emi, MediaListRepository $mlr, FileManager $fm): Response
    {
        $mediaList = $mlr->findOneBy(['id' => $request->get('id')]);
        $folderToDelete = $this->getParameter('kernel.project_dir') . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . $mediaList->getPath() . DIRECTORY_SEPARATOR . $mediaList->getTitle();
        $posterToDelete = $this->getParameter('kernel.project_dir') . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'downloaded' . DIRECTORY_SEPARATOR . 'posters' . DIRECTORY_SEPARATOR . $mediaList->getTitle() . '.jpg';

        $folderDeleted = $fm->deleteFolder($folderToDelete);
        $fileDeteted = $fm->deleteFile($posterToDelete);

        $emi->remove($mediaList);
        $emi->flush();

        $mediaListType = $mediaList->getType() == 0 ? 'playlist' : 'chaine';

        // Ajouter un message flash
        $this->addFlash('success', 'La '.$mediaListType.' a bien été supprimée.');

        return $this->redirectToRoute('index');
    }
}
