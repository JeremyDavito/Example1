<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211202105409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category CHANGE picture picture VARCHAR(255) DEFAULT \'category_placeholder.png\' NOT NULL');
        $this->addSql('ALTER TABLE event CHANGE picture picture VARCHAR(255) DEFAULT \'event_placeholder.png\' NOT NULL, CHANGE is_archived is_archived TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE avatar avatar VARCHAR(255) DEFAULT \'user_placeholder.png\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category CHANGE picture picture VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE event CHANGE picture picture VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE is_archived is_archived TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE avatar avatar VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
