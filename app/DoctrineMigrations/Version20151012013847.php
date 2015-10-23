<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151012013847 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE pixel_log ADD offer_id INT DEFAULT NULL, ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pixel_log ADD CONSTRAINT FK_E162582153C674EE FOREIGN KEY (offer_id) REFERENCES offer (id)');
        $this->addSql('ALTER TABLE pixel_log ADD CONSTRAINT FK_E1625821A76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (id)');
        $this->addSql('CREATE INDEX IDX_E162582153C674EE ON pixel_log (offer_id)');
        $this->addSql('CREATE INDEX IDX_E1625821A76ED395 ON pixel_log (user_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE pixel_log DROP FOREIGN KEY FK_E162582153C674EE');
        $this->addSql('ALTER TABLE pixel_log DROP FOREIGN KEY FK_E1625821A76ED395');
        $this->addSql('DROP INDEX IDX_E162582153C674EE ON pixel_log');
        $this->addSql('DROP INDEX IDX_E1625821A76ED395 ON pixel_log');
        $this->addSql('ALTER TABLE pixel_log DROP offer_id, DROP user_id');
    }
}
