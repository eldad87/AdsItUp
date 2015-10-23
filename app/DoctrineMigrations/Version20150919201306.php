<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150919201306 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE commission_plan ADD `condition` LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', ADD payout NUMERIC(10, 0) NOT NULL, DROP country, DROP site_language, DROP site_language_selected, DROP sale_status, DROP lead_status, DROP min_amount, DROP payment');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE commission_plan ADD site_language LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', ADD site_language_selected LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', ADD sale_status LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', ADD lead_status LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', ADD payment NUMERIC(10, 0) NOT NULL, CHANGE condition country LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', CHANGE payout min_amount NUMERIC(10, 0) NOT NULL');
    }
}
