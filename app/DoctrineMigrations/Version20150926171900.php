<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150926171900 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user CHANGE phone phone VARCHAR(20) NOT NULL, CHANGE skype skype VARCHAR(255) DEFAULT NULL, CHANGE icq icq VARCHAR(255) DEFAULT NULL, CHANGE company company VARCHAR(255) DEFAULT NULL, CHANGE website website VARCHAR(255) DEFAULT NULL, CHANGE comment comment VARCHAR(500) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user CHANGE phone phone VARCHAR(255) NOT NULL, CHANGE skype skype VARCHAR(255) NOT NULL, CHANGE icq icq VARCHAR(255) NOT NULL, CHANGE company company VARCHAR(255) NOT NULL, CHANGE website website VARCHAR(255) NOT NULL, CHANGE comment comment VARCHAR(500) NOT NULL');
    }
}
