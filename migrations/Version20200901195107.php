<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200901195107 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participant DROP INDEX UNIQ_D79F6B11A76ED395, ADD INDEX IDX_D79F6B11A76ED395 (user_id)');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B1171F7E88B');
        $this->addSql('DROP INDEX UNIQ_D79F6B1171F7E88B ON participant');
        $this->addSql('ALTER TABLE participant DROP event_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participant DROP INDEX IDX_D79F6B11A76ED395, ADD UNIQUE INDEX UNIQ_D79F6B11A76ED395 (user_id)');
        $this->addSql('ALTER TABLE participant ADD event_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B1171F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D79F6B1171F7E88B ON participant (event_id)');
    }
}
