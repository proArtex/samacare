<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210125161416 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tweet (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, message VARCHAR(140) NOT NULL, timestamp INT NOT NULL, INDEX IDX_3D660A3BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tweet_reply (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, message VARCHAR(140) NOT NULL, timestamp INT NOT NULL, INDEX IDX_61631BAFA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, token VARCHAR(24) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:simple_array)\', registered_at DATETIME NOT NULL, UNIQUE INDEX user_unique_username (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tweet ADD CONSTRAINT FK_3D660A3BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tweet_reply ADD CONSTRAINT FK_61631BAFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tweet DROP FOREIGN KEY FK_3D660A3BA76ED395');
        $this->addSql('ALTER TABLE tweet_reply DROP FOREIGN KEY FK_61631BAFA76ED395');
        $this->addSql('DROP TABLE tweet');
        $this->addSql('DROP TABLE tweet_reply');
        $this->addSql('DROP TABLE user');
    }
}
