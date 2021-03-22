<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210322140612 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fb_account (id INT AUTO_INCREMENT NOT NULL, account_id INT NOT NULL, shortlivedtoken VARCHAR(255) DEFAULT NULL, page_access_token VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fb_page_and_insta (id INT AUTO_INCREMENT NOT NULL, fb_account_id INT NOT NULL, page_id INT NOT NULL, account_id_inst VARCHAR(255) DEFAULT NULL, name_page VARCHAR(255) NOT NULL, name_inst VARCHAR(255) DEFAULT NULL, INDEX IDX_114017D5F6ACAFB8 (fb_account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE twitter_account (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, consumer_key VARCHAR(255) NOT NULL, consumer_secret VARCHAR(255) NOT NULL, access_token VARCHAR(255) NOT NULL, access_token_secret VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fb_page_and_insta ADD CONSTRAINT FK_114017D5F6ACAFB8 FOREIGN KEY (fb_account_id) REFERENCES fb_account (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fb_page_and_insta DROP FOREIGN KEY FK_114017D5F6ACAFB8');
        $this->addSql('DROP TABLE fb_account');
        $this->addSql('DROP TABLE fb_page_and_insta');
        $this->addSql('DROP TABLE twitter_account');
    }
}
