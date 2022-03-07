<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220307152132 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__cheese_listing AS SELECT id, title, description, price, created_at, is_published FROM cheese_listing');
        $this->addSql('DROP TABLE cheese_listing');
        $this->addSql('CREATE TABLE cheese_listing (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER NOT NULL, title VARCHAR(255) NOT NULL, description CLOB NOT NULL, price INTEGER NOT NULL, created_at DATETIME NOT NULL, is_published BOOLEAN NOT NULL, CONSTRAINT FK_356577D47E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO cheese_listing (id, title, description, price, created_at, is_published) SELECT id, title, description, price, created_at, is_published FROM __temp__cheese_listing');
        $this->addSql('DROP TABLE __temp__cheese_listing');
        $this->addSql('CREATE INDEX IDX_356577D47E3C61F9 ON cheese_listing (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_356577D47E3C61F9');
        $this->addSql('CREATE TEMPORARY TABLE __temp__cheese_listing AS SELECT id, title, description, price, created_at, is_published FROM cheese_listing');
        $this->addSql('DROP TABLE cheese_listing');
        $this->addSql('CREATE TABLE cheese_listing (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description CLOB NOT NULL, price INTEGER NOT NULL, created_at DATETIME NOT NULL, is_published BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO cheese_listing (id, title, description, price, created_at, is_published) SELECT id, title, description, price, created_at, is_published FROM __temp__cheese_listing');
        $this->addSql('DROP TABLE __temp__cheese_listing');
    }
}
