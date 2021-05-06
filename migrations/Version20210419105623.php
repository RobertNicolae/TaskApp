<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210419105623 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE objective ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE objective ADD CONSTRAINT FK_B996F101A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_B996F101A76ED395 ON objective (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE objective DROP FOREIGN KEY FK_B996F101A76ED395');
        $this->addSql('DROP INDEX IDX_B996F101A76ED395 ON objective');
        $this->addSql('ALTER TABLE objective DROP user_id');
    }
}
