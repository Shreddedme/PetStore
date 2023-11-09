<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231102104237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE operation_history_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE operation_history (id INT NOT NULL, performed_by_id INT NOT NULL, operation_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_847BF0862E65C292 ON operation_history (performed_by_id)');
        $this->addSql('ALTER TABLE operation_history ADD CONSTRAINT FK_847BF0862E65C292 FOREIGN KEY (performed_by_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE operation_history_id_seq CASCADE');
        $this->addSql('ALTER TABLE operation_history DROP CONSTRAINT FK_847BF0862E65C292');
        $this->addSql('DROP TABLE operation_history');
    }
}
