<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210408072247 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE artiste (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, name_facebook VARCHAR(255) NOT NULL, name_twitter VARCHAR(255) NOT NULL, name_insta VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE artiste_post (artiste_id INT NOT NULL, post_id INT NOT NULL, INDEX IDX_C242080E21D25844 (artiste_id), INDEX IDX_C242080E4B89032C (post_id), PRIMARY KEY(artiste_id, post_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fb_account (id INT AUTO_INCREMENT NOT NULL, social_media_account_id INT NOT NULL, shortlivedtoken VARCHAR(500) DEFAULT NULL, longlivedtoken VARCHAR(500) DEFAULT NULL, account_id VARCHAR(255) NOT NULL, client_secret VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_21BADDD3E5E06EE3 (social_media_account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fb_page (id INT AUTO_INCREMENT NOT NULL, fb_account_id INT NOT NULL, social_media_account_id INT NOT NULL, page_id VARCHAR(255) NOT NULL, page_access_token VARCHAR(500) NOT NULL, name_page VARCHAR(255) NOT NULL, INDEX IDX_BF3B39D6F6ACAFB8 (fb_account_id), UNIQUE INDEX UNIQ_BF3B39D6E5E06EE3 (social_media_account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE insta_account (id INT AUTO_INCREMENT NOT NULL, social_media_account_id INT NOT NULL, fb_page_id INT NOT NULL, name VARCHAR(255) NOT NULL, id_account VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_F857C565E5E06EE3 (social_media_account_id), UNIQUE INDEX UNIQ_F857C56550CAE893 (fb_page_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, template_post_id INT DEFAULT NULL, image LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', date DATETIME DEFAULT NULL, description VARCHAR(500) NOT NULL, INDEX IDX_5A8A6C8DA76ED395 (user_id), INDEX IDX_5A8A6C8D18317815 (template_post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_social_media_account (post_id INT NOT NULL, social_media_account_id INT NOT NULL, INDEX IDX_106524994B89032C (post_id), INDEX IDX_10652499E5E06EE3 (social_media_account_id), PRIMARY KEY(post_id, social_media_account_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE social_media_account (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, social_media VARCHAR(255) DEFAULT NULL, INDEX IDX_AA5B5E79A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE template_post (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, description VARCHAR(500) NOT NULL, title VARCHAR(255) NOT NULL, INDEX IDX_8F4446E0A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE twitter_account (id INT AUTO_INCREMENT NOT NULL, social_media_account_id INT NOT NULL, consumer_key VARCHAR(255) NOT NULL, consumer_secret VARCHAR(255) NOT NULL, access_token VARCHAR(255) NOT NULL, access_token_secret VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_B147A236E5E06EE3 (social_media_account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE artiste_post ADD CONSTRAINT FK_C242080E21D25844 FOREIGN KEY (artiste_id) REFERENCES artiste (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE artiste_post ADD CONSTRAINT FK_C242080E4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fb_account ADD CONSTRAINT FK_21BADDD3E5E06EE3 FOREIGN KEY (social_media_account_id) REFERENCES social_media_account (id)');
        $this->addSql('ALTER TABLE fb_page ADD CONSTRAINT FK_BF3B39D6F6ACAFB8 FOREIGN KEY (fb_account_id) REFERENCES fb_account (id)');
        $this->addSql('ALTER TABLE fb_page ADD CONSTRAINT FK_BF3B39D6E5E06EE3 FOREIGN KEY (social_media_account_id) REFERENCES social_media_account (id)');
        $this->addSql('ALTER TABLE insta_account ADD CONSTRAINT FK_F857C565E5E06EE3 FOREIGN KEY (social_media_account_id) REFERENCES social_media_account (id)');
        $this->addSql('ALTER TABLE insta_account ADD CONSTRAINT FK_F857C56550CAE893 FOREIGN KEY (fb_page_id) REFERENCES fb_page (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D18317815 FOREIGN KEY (template_post_id) REFERENCES template_post (id)');
        $this->addSql('ALTER TABLE post_social_media_account ADD CONSTRAINT FK_106524994B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post_social_media_account ADD CONSTRAINT FK_10652499E5E06EE3 FOREIGN KEY (social_media_account_id) REFERENCES social_media_account (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE social_media_account ADD CONSTRAINT FK_AA5B5E79A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE template_post ADD CONSTRAINT FK_8F4446E0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE twitter_account ADD CONSTRAINT FK_B147A236E5E06EE3 FOREIGN KEY (social_media_account_id) REFERENCES social_media_account (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artiste_post DROP FOREIGN KEY FK_C242080E21D25844');
        $this->addSql('ALTER TABLE fb_page DROP FOREIGN KEY FK_BF3B39D6F6ACAFB8');
        $this->addSql('ALTER TABLE insta_account DROP FOREIGN KEY FK_F857C56550CAE893');
        $this->addSql('ALTER TABLE artiste_post DROP FOREIGN KEY FK_C242080E4B89032C');
        $this->addSql('ALTER TABLE post_social_media_account DROP FOREIGN KEY FK_106524994B89032C');
        $this->addSql('ALTER TABLE fb_account DROP FOREIGN KEY FK_21BADDD3E5E06EE3');
        $this->addSql('ALTER TABLE fb_page DROP FOREIGN KEY FK_BF3B39D6E5E06EE3');
        $this->addSql('ALTER TABLE insta_account DROP FOREIGN KEY FK_F857C565E5E06EE3');
        $this->addSql('ALTER TABLE post_social_media_account DROP FOREIGN KEY FK_10652499E5E06EE3');
        $this->addSql('ALTER TABLE twitter_account DROP FOREIGN KEY FK_B147A236E5E06EE3');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D18317815');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DA76ED395');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE social_media_account DROP FOREIGN KEY FK_AA5B5E79A76ED395');
        $this->addSql('ALTER TABLE template_post DROP FOREIGN KEY FK_8F4446E0A76ED395');
        $this->addSql('DROP TABLE artiste');
        $this->addSql('DROP TABLE artiste_post');
        $this->addSql('DROP TABLE fb_account');
        $this->addSql('DROP TABLE fb_page');
        $this->addSql('DROP TABLE insta_account');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE post_social_media_account');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE social_media_account');
        $this->addSql('DROP TABLE template_post');
        $this->addSql('DROP TABLE twitter_account');
        $this->addSql('DROP TABLE user');
    }
}
