<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211221075817 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category CHANGE picture picture VARCHAR(255) DEFAULT \'category_placeholder.jpg\' NOT NULL');
        $this->addSql('ALTER TABLE event CHANGE picture picture VARCHAR(255) DEFAULT \'event_placeholder.jpg\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category CHANGE picture picture VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'category_placeholder.png\' NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE event CHANGE picture picture VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'event_placeholder.png\' NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
