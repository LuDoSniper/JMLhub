<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241109193429 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE playlist (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE playlist_movie (playlist_id INT NOT NULL, movie_id INT NOT NULL, INDEX IDX_BE42EB2C6BBD148 (playlist_id), INDEX IDX_BE42EB2C8F93B6FC (movie_id), PRIMARY KEY(playlist_id, movie_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE playlist_movie ADD CONSTRAINT FK_BE42EB2C6BBD148 FOREIGN KEY (playlist_id) REFERENCES playlist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE playlist_movie ADD CONSTRAINT FK_BE42EB2C8F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE movie RENAME INDEX fk_1d5ef26f12469de2 TO IDX_1D5EF26F12469DE2');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE playlist_movie DROP FOREIGN KEY FK_BE42EB2C6BBD148');
        $this->addSql('ALTER TABLE playlist_movie DROP FOREIGN KEY FK_BE42EB2C8F93B6FC');
        $this->addSql('DROP TABLE playlist');
        $this->addSql('DROP TABLE playlist_movie');
        $this->addSql('ALTER TABLE movie RENAME INDEX idx_1d5ef26f12469de2 TO FK_1D5EF26F12469DE2');
    }
}
