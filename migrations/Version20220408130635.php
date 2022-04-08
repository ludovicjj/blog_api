<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220408130635 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_356577D47E3C61F9');
        $this->addSql('CREATE TEMPORARY TABLE __temp__cheese_listing AS SELECT id, owner_id, title, description, price, created_at, is_published FROM cheese_listing');
        $this->addSql('DROP TABLE cheese_listing');
        $this->addSql('CREATE TABLE cheese_listing (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER NOT NULL, title VARCHAR(255) NOT NULL, description CLOB NOT NULL, price INTEGER NOT NULL, created_at DATETIME NOT NULL, is_published BOOLEAN NOT NULL, CONSTRAINT FK_356577D47E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO cheese_listing (id, owner_id, title, description, price, created_at, is_published) SELECT id, owner_id, title, description, price, created_at, is_published FROM __temp__cheese_listing');
        $this->addSql('DROP TABLE __temp__cheese_listing');
        $this->addSql('CREATE INDEX IDX_356577D47E3C61F9 ON cheese_listing (owner_id)');
        $this->addSql('DROP INDEX IDX_D33F5BC5B167220F');
        $this->addSql('CREATE TEMPORARY TABLE __temp__cheese_notification AS SELECT id, cheese_listing_id, notification_text FROM cheese_notification');
        $this->addSql('DROP TABLE cheese_notification');
        $this->addSql('CREATE TABLE cheese_notification (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, cheese_listing_id INTEGER NOT NULL, notification_text VARCHAR(255) NOT NULL, CONSTRAINT FK_D33F5BC5B167220F FOREIGN KEY (cheese_listing_id) REFERENCES cheese_listing (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO cheese_notification (id, cheese_listing_id, notification_text) SELECT id, cheese_listing_id, notification_text FROM __temp__cheese_notification');
        $this->addSql('DROP TABLE __temp__cheese_notification');
        $this->addSql('CREATE INDEX IDX_D33F5BC5B167220F ON cheese_notification (cheese_listing_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, email, roles, password, username, phone_number FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, phone_number VARCHAR(50) DEFAULT NULL, uuid CHAR(36) NOT NULL --(DC2Type:uuid)
        )');
        $this->addSql('INSERT INTO user (id, email, roles, password, username, phone_number) SELECT id, email, roles, password, username, phone_number FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649D17F50A6 ON user (uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_356577D47E3C61F9');
        $this->addSql('CREATE TEMPORARY TABLE __temp__cheese_listing AS SELECT id, owner_id, title, description, price, created_at, is_published FROM cheese_listing');
        $this->addSql('DROP TABLE cheese_listing');
        $this->addSql('CREATE TABLE cheese_listing (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, owner_id INTEGER NOT NULL, title VARCHAR(255) NOT NULL, description CLOB NOT NULL, price INTEGER NOT NULL, created_at DATETIME NOT NULL, is_published BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO cheese_listing (id, owner_id, title, description, price, created_at, is_published) SELECT id, owner_id, title, description, price, created_at, is_published FROM __temp__cheese_listing');
        $this->addSql('DROP TABLE __temp__cheese_listing');
        $this->addSql('CREATE INDEX IDX_356577D47E3C61F9 ON cheese_listing (owner_id)');
        $this->addSql('DROP INDEX IDX_D33F5BC5B167220F');
        $this->addSql('CREATE TEMPORARY TABLE __temp__cheese_notification AS SELECT id, cheese_listing_id, notification_text FROM cheese_notification');
        $this->addSql('DROP TABLE cheese_notification');
        $this->addSql('CREATE TABLE cheese_notification (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, cheese_listing_id INTEGER NOT NULL, notification_text VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO cheese_notification (id, cheese_listing_id, notification_text) SELECT id, cheese_listing_id, notification_text FROM __temp__cheese_notification');
        $this->addSql('DROP TABLE __temp__cheese_notification');
        $this->addSql('CREATE INDEX IDX_D33F5BC5B167220F ON cheese_notification (cheese_listing_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649D17F50A6');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74');
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, email, roles, password, username, phone_number FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, phone_number VARCHAR(50) DEFAULT NULL)');
        $this->addSql('INSERT INTO user (id, email, roles, password, username, phone_number) SELECT id, email, roles, password, username, phone_number FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
    }
}
