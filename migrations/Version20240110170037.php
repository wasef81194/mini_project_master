<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240110170037 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fds ADD produit_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE fds ADD CONSTRAINT FK_7B86E0A5F347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('CREATE INDEX IDX_7B86E0A5F347EFB ON fds (produit_id)');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27E3578F39');
        $this->addSql('DROP INDEX IDX_29A5EC27E3578F39 ON produit');
        $this->addSql('ALTER TABLE produit DROP fds_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit ADD fds_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27E3578F39 FOREIGN KEY (fds_id) REFERENCES fds (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_29A5EC27E3578F39 ON produit (fds_id)');
        $this->addSql('ALTER TABLE fds DROP FOREIGN KEY FK_7B86E0A5F347EFB');
        $this->addSql('DROP INDEX IDX_7B86E0A5F347EFB ON fds');
        $this->addSql('ALTER TABLE fds DROP produit_id');
    }
}
