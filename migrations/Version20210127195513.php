<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210127195513 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE blocked_followers (author_id INT NOT NULL, blocked_follower_id INT NOT NULL, INDEX IDX_8587EB24F675F31B (author_id), INDEX IDX_8587EB24485E8705 (blocked_follower_id), PRIMARY KEY(author_id, blocked_follower_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE blocked_followers ADD CONSTRAINT FK_8587EB24F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE blocked_followers ADD CONSTRAINT FK_8587EB24485E8705 FOREIGN KEY (blocked_follower_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE blocked_followers');
    }
}
