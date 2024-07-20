<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240719235327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alert ADD COLUMN finished BOOLEAN NOT NULL default 0');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__alert AS SELECT id, author_id, created_at FROM alert');
        $this->addSql('DROP TABLE alert');
        $this->addSql('CREATE TABLE alert (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_17FD46C1F675F31B FOREIGN KEY (author_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO alert (id, author_id, created_at) SELECT id, author_id, created_at FROM __temp__alert');
        $this->addSql('DROP TABLE __temp__alert');
        $this->addSql('CREATE INDEX IDX_17FD46C1F675F31B ON alert (author_id)');
    }
}
