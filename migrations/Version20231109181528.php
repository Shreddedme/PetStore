<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231109181528 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE operation_history ADD pet_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE operation_history ADD CONSTRAINT FK_847BF086966F7FB6 FOREIGN KEY (pet_id) REFERENCES pet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_847BF086966F7FB6 ON operation_history (pet_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE operation_history DROP CONSTRAINT FK_847BF086966F7FB6');
        $this->addSql('DROP INDEX IDX_847BF086966F7FB6');
        $this->addSql('ALTER TABLE operation_history DROP pet_id');
    }
}
