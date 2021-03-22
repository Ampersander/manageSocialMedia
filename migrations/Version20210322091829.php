<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210322091829 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE post_social_media_account (post_id INT NOT NULL, social_media_account_id INT NOT NULL, INDEX IDX_106524994B89032C (post_id), INDEX IDX_10652499E5E06EE3 (social_media_account_id), PRIMARY KEY(post_id, social_media_account_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE post_social_media_account ADD CONSTRAINT FK_106524994B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_social_media_account ADD CONSTRAINT FK_10652499E5E06EE3 FOREIGN KEY (social_media_account_id) REFERENCES social_media_account (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE post_social_media_account');
    }
}
