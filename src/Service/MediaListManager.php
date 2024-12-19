<?php

namespace App\Service;

use App\Entity\MediaList;
use Symfony\Component\Filesystem\Filesystem;

class MediaListManager {
    private ProcessExecutor $processExecutor;
    private FileManager $fileManager;
    private string $projectDir;

    public function __construct(ProcessExecutor $processExecutor, FileManager $fileManager, string $projectDir) {
        $this->processExecutor = $processExecutor;
        $this->fileManager = $fileManager;
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
            yt-dlp --skip-download --flat-playlist --write-info-json --write-description --write-thumbnail --add-metadata -o "{$path}/{$title}/{$title}.%(ext)s" "{$url}"
            BASH;

        $output = $this->processExecutor->execute(['bash', '-c', $command]);
        //dd($output);

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
        $title = $mediaList->getTitle();
        $path = $mediaList->getPath();
        // Copier le fichier .jpg vers public
        $sourceFile = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR .
            $title . DIRECTORY_SEPARATOR . $title . '.jpg';
        $destinationDir = $this->projectDir . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'downloaded'. DIRECTORY_SEPARATOR .'posters'. DIRECTORY_SEPARATOR;
        $destinationFile = $destinationDir . $mediaList->getTitle() . '.jpg';
        $filesystem = new Filesystem();
        $filesystem->copy($sourceFile, $destinationFile, true);
        return true;
    }
}