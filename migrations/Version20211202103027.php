<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211202103027 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE favorite (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE favorite_event (favorite_id INT NOT NULL, event_id INT NOT NULL, INDEX IDX_8D262183AA17481D (favorite_id), INDEX IDX_8D26218371F7E88B (event_id), PRIMARY KEY(favorite_id, event_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE favorite_user (favorite_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_6395CF76AA17481D (favorite_id), INDEX IDX_6395CF76A76ED395 (user_id), PRIMARY KEY(favorite_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE favorite_event ADD CONSTRAINT FK_8D262183AA17481D FOREIGN KEY (favorite_id) REFERENCES favorite (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favorite_event ADD CONSTRAINT FK_8D26218371F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favorite_user ADD CONSTRAINT FK_6395CF76AA17481D FOREIGN KEY (favorite_id) REFERENCES favorite (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favorite_user ADD CONSTRAINT FK_6395CF76A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favorite_event DROP FOREIGN KEY FK_8D262183AA17481D');
        $this->addSql('ALTER TABLE favorite_user DROP FOREIGN KEY FK_6395CF76AA17481D');
        $this->addSql('DROP TABLE favorite');
        $this->addSql('DROP TABLE favorite_event');
        $this->addSql('DROP TABLE favorite_user');
    }
}
