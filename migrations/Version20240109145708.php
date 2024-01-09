<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240109145708 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fds (id INT AUTO_INCREMENT NOT NULL, chemin_pdf VARCHAR(255) NOT NULL, creer_le DATETIME NOT NULL, supprimer_le DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE produit ADD fds_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27E3578F39 FOREIGN KEY (fds_id) REFERENCES fds (id)');
        $this->addSql('CREATE INDEX IDX_29A5EC27E3578F39 ON produit (fds_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27E3578F39');
        $this->addSql('DROP TABLE fds');
        $this->addSql('DROP INDEX IDX_29A5EC27E3578F39 ON produit');
        $this->addSql('ALTER TABLE produit DROP fds_id');
    }
}
