<?php

namespace App\Service;

use App\Entity\MediaList;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;

class MediaListManager {
    private ProcessExecutor $processExecutor;
    private FileManager $fileManager;
    private string $projectDir;

    private EntityManagerInterface $entityManager;

    public function __construct(ProcessExecutor $processExecutor, FileManager $fileManager, EntityManagerInterface $entityManager, string $projectDir) {
        $this->processExecutor = $processExecutor;
        $this->fileManager = $fileManager;
        $this->entityManager = $entityManager;
        $this->projectDir = $projectDir;
    }

    public function getMediaListInfos(MediaList $mediaList): void {
        $url = $mediaList->getUrl();
        $mediaList->setType(str_contains($url, 'https://www.youtube.com/@') ? 1 : 0);

        if($mediaList->getType() === 0){ // playlist
            $command = [
                'yt-dlp',
                $url,
                '--flat',
                '--lazy-playlist',
                '--playlist-items', '1',
                '-O', '%(playlist_title)s'
            ];

        } else { // chaine
            $command = [
                'yt-dlp',
                $url,
                '--playlist-items', '1',
                '-O', '%(uploader)s',
            ];
        }

        $output = $this->processExecutor->execute($command);
        $mediaList->setTitle(trim($output));
    }

    public function downloadMediaListInfos(MediaList $mediaList): void {
        $path = dirname(__DIR__, 2). DIRECTORY_SEPARATOR . $mediaList->getPath();
        $title = $mediaList->getTitle();
        $url = $mediaList->getUrl();

        $command = <<<BASH
            yt-dlp --skip-download --flat-playlist --write-info-json --write-description --write-thumbnail --add-metadata -o "$path/$title/$title.%(ext)s" "$url"
            BASH;

        $this->processExecutor->execute(['bash', '-c', $command]);

    }

    /*
     * Vérifier comment se termine le path entré. S'il y a déjà un slash alors on en rajoute pas et vice versa. Ajouter 'data'
     */
    public function configurePath(MediaList $mediaList): bool
    {
        $path = $mediaList->getPath();

        if($path[strlen($path) - 1] == DIRECTORY_SEPARATOR) {
            $path = substr($path, 0, strlen($path) - 1);
        }

        if($path[0] != DIRECTORY_SEPARATOR) {
            $path = DIRECTORY_SEPARATOR . $path;
        }
        $path = 'data' . $path;
        $this->fileManager->createDirectory($path);
        $this->fileManager->isWritable($path);
        $mediaList->setPath($path);
        return true;
    }

    public function copyPoster(MediaList $mediaList): bool
    {

        $sourceFile = $this->getPosterFileSource($mediaList);

        $destinationFile = $this->getCopiedPosterFileDestination($mediaList);
        $filesystem = new Filesystem();
        $filesystem->copy($sourceFile, $destinationFile, true);
        return true;
    }

    public function deleteMediaListFiles(MediaList $mediaList): bool
    {
        $folderToDelete = $this->getMediaListFolder($mediaList);
        $posterToDelete = $this->getCopiedPosterFileDestination($mediaList);

        $folderDeleted = $this->fileManager->deleteFolder($folderToDelete);
        $fileDeteted = $this->fileManager->deleteFile($posterToDelete);

        $this->entityManager->remove($mediaList);
        $this->entityManager->flush();

        if($folderDeleted && $fileDeteted) {
            return true;
        } else {
            return false;
        }
    }

    public function getCopiedPosterFileDestination(MediaList $mediaList) : string
    {
        $destinationDir = $this->projectDir . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'downloaded'. DIRECTORY_SEPARATOR .'posters'. DIRECTORY_SEPARATOR;
        return $destinationDir . $mediaList->getTitle() . '.jpg';
    }

    public function getMediaListFolder(MediaList $mediaList) : string
    {
        return $this->projectDir . DIRECTORY_SEPARATOR . $mediaList->getPath() . DIRECTORY_SEPARATOR . $mediaList->getTitle();
    }

    public function getPosterFileSource(MediaList $mediaList) : string
    {
        return $this->getMediaListFolder($mediaList) . DIRECTORY_SEPARATOR . $mediaList->getTitle() . '.jpg';
    }

    public function archiveMediaList(MediaList $mediaList): bool
    {
        if($mediaList->isArchived()) {
            $mediaList->setArchived(false);
        } else{
            $mediaList->setArchived(true);
        }
        $this->persistMediaList($mediaList);

        return true;
    }

    public function persistMediaList($mediaList): bool{
        $this->entityManager->persist($mediaList);
        $this->entityManager->flush();
        return true;
    }

    public function deleteMediaList($mediaList): bool{
        $this->entityManager->remove($mediaList);
        $this->entityManager->flush();
        return true;
    }
}