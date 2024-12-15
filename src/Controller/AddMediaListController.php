<?php

namespace App\Controller;

use App\Entity\MediaList;
use App\Form\AddMediaListType;
use App\Repository\MediaListRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Attribute\Route;

class AddMediaListController extends AbstractController
{
    #[Route('/add/mediaList', name: 'add.mediaList')]
    public function add(Request $request, EntityManagerInterface $emi): Response
    {
        $mediaList = new MediaList();
        $form = $this->createForm(AddMediaListType::class, $mediaList);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //Récupérer le titre de la medialist
            $url = $mediaList->getUrl();
            // Distinguer channel / playlist

            // Utiliser yt-dlp pour récupérer le titre
            $type = 0;
            $command = [
                'yt-dlp',
                $url,
                '--flat',
                '--lazy',
                '--playlist-items', '1',
                '-O', '%(playlist_title)s'
            ];

            if (str_contains($url, 'https://www.youtube.com/@')) {
                $type = 1;
                $command = [
                    'yt-dlp',
                    $url,
                    '--playlist-items', '1',
                    '-O', '%(uploader)s',
                ];
            }

            // Exécuter la commande avec Symfony Process
            $process = new Process($command);
            $process->run();

            // Vérifier si la commande a réussi
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
            // Récupérer le titre
            $title = trim($process->getOutput());
            $mediaList->setTitle($title);
            $mediaList->setPath('/TOUTUBE/TT');
            $mediaList->setType($type);

            // -----------
            $emi->persist($mediaList);
            $emi->flush();
            return $this->redirectToRoute('index');
        }
        return $this->render('add_media_list/index.html.twig', [
            'controller_name' => 'MediaListController',
            'form' => $form->createView(),
        ]);
    }
}
