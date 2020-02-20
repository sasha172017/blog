<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200220101733 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user_post');
        $this->addSql('ALTER TABLE post CHANGE image image VARCHAR(25) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE date_of_birth date_of_birth DATE DEFAULT NULL, CHANGE verification_token verification_token VARCHAR(150) DEFAULT NULL, CHANGE api_token api_token VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_post (user_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_200B2044A76ED395 (user_id), INDEX IDX_200B20444B89032C (post_id), PRIMARY KEY(user_id, post_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user_post ADD CONSTRAINT FK_200B20444B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_post ADD CONSTRAINT FK_200B2044A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post CHANGE image image VARCHAR(25) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE date_of_birth date_of_birth DATE DEFAULT \'NULL\', CHANGE verification_token verification_token VARCHAR(150) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`, CHANGE api_token api_token VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
    }
}
