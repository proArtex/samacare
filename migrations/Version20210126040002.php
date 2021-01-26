<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210126040002 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tweet_replies (tweet_id INT NOT NULL, reply_id INT NOT NULL, INDEX IDX_2F8306421041E39B (tweet_id), INDEX IDX_2F8306428A0E4E7F (reply_id), PRIMARY KEY(tweet_id, reply_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tweet_replies ADD CONSTRAINT FK_2F8306421041E39B FOREIGN KEY (tweet_id) REFERENCES tweet (id)');
        $this->addSql('ALTER TABLE tweet_replies ADD CONSTRAINT FK_2F8306428A0E4E7F FOREIGN KEY (reply_id) REFERENCES tweet_reply (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE tweet_replies');
    }
}
