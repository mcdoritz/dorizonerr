<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241215113901 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__media_list AS SELECT id, title, url, x_last_videos, delete_after, cronjob, quality, path, type, archived FROM media_list');
        $this->addSql('DROP TABLE media_list');
        $this->addSql('CREATE TABLE media_list (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, url CLOB NOT NULL, x_last_videos INTEGER NOT NULL, delete_after INTEGER NOT NULL, cronjob VARCHAR(20) NOT NULL, quality INTEGER NOT NULL, path CLOB NOT NULL, type INTEGER NOT NULL, archived BOOLEAN DEFAULT 0 NOT NULL)');
        $this->addSql('INSERT INTO media_list (id, title, url, x_last_videos, delete_after, cronjob, quality, path, type, archived) SELECT id, title, url, x_last_videos, delete_after, cronjob, quality, path, type, archived FROM __temp__media_list');
        $this->addSql('DROP TABLE __temp__media_list');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__media_list AS SELECT id, title, url, x_last_videos, delete_after, cronjob, quality, path, type, archived FROM media_list');
        $this->addSql('DROP TABLE media_list');
        $this->addSql('CREATE TABLE media_list (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, url CLOB NOT NULL, x_last_videos INTEGER NOT NULL, delete_after INTEGER NOT NULL, cronjob VARCHAR(20) NOT NULL, quality INTEGER NOT NULL, path CLOB NOT NULL, type INTEGER NOT NULL, archived BOOLEAN DEFAULT 1 NOT NULL)');
        $this->addSql('INSERT INTO media_list (id, title, url, x_last_videos, delete_after, cronjob, quality, path, type, archived) SELECT id, title, url, x_last_videos, delete_after, cronjob, quality, path, type, archived FROM __temp__media_list');
        $this->addSql('DROP TABLE __temp__media_list');
    }
}
