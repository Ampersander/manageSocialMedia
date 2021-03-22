<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210322144841 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE social_media_account ADD fb_account_id INT DEFAULT NULL, ADD twitter_account_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE social_media_account ADD CONSTRAINT FK_AA5B5E79F6ACAFB8 FOREIGN KEY (fb_account_id) REFERENCES fb_account (id)');
        $this->addSql('ALTER TABLE social_media_account ADD CONSTRAINT FK_AA5B5E79322E56FB FOREIGN KEY (twitter_account_id) REFERENCES twitter_account (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AA5B5E79F6ACAFB8 ON social_media_account (fb_account_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AA5B5E79322E56FB ON social_media_account (twitter_account_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE social_media_account DROP FOREIGN KEY FK_AA5B5E79F6ACAFB8');
        $this->addSql('ALTER TABLE social_media_account DROP FOREIGN KEY FK_AA5B5E79322E56FB');
        $this->addSql('DROP INDEX UNIQ_AA5B5E79F6ACAFB8 ON social_media_account');
        $this->addSql('DROP INDEX UNIQ_AA5B5E79322E56FB ON social_media_account');
        $this->addSql('ALTER TABLE social_media_account DROP fb_account_id, DROP twitter_account_id');
    }
}
