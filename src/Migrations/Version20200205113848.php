<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200205113848 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE category CHANGE color color INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post CHANGE image image VARCHAR(25) DEFAULT NULL, CHANGE views views INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD color INT DEFAULT NULL, CHANGE roles roles JSON NOT NULL, CHANGE date_of_birth date_of_birth DATE DEFAULT NULL, CHANGE verification_token verification_token VARCHAR(150) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE category CHANGE color color INT DEFAULT NULL');
        $this->addSql('ALTER TABLE post CHANGE views views INT DEFAULT NULL, CHANGE image image VARCHAR(25) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user DROP color, CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE date_of_birth date_of_birth DATE DEFAULT \'NULL\', CHANGE verification_token verification_token VARCHAR(150) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
    }
}
