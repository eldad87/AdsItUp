<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150915135151 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE offer_click CHANGE ua ua VARCHAR(50) DEFAULT NULL, CHANGE ua_version ua_version VARCHAR(50) DEFAULT NULL, CHANGE os os VARCHAR(50) DEFAULT NULL, CHANGE os_version os_version VARCHAR(50) DEFAULT NULL, CHANGE device device VARCHAR(50) DEFAULT NULL, CHANGE ua_raw ua_raw VARCHAR(500) DEFAULT NULL, CHANGE country_code country_code CHAR(2) DEFAULT NULL, CHANGE subdivision_code subdivision_code CHAR(2) DEFAULT NULL, CHANGE city city VARCHAR(50) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE offer_click CHANGE ua ua VARCHAR(50) NOT NULL, CHANGE ua_version ua_version VARCHAR(50) NOT NULL, CHANGE os os VARCHAR(50) NOT NULL, CHANGE os_version os_version VARCHAR(50) NOT NULL, CHANGE device device VARCHAR(50) NOT NULL, CHANGE ua_raw ua_raw VARCHAR(500) NOT NULL, CHANGE country_code country_code CHAR(2) NOT NULL, CHANGE subdivision_code subdivision_code CHAR(2) NOT NULL, CHANGE city city VARCHAR(50) NOT NULL');
    }
}
