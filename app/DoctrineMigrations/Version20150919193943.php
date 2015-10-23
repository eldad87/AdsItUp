<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150919193943 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE commission_plan (id INT AUTO_INCREMENT NOT NULL, brand_id INT DEFAULT NULL, type INT NOT NULL, priority SMALLINT NOT NULL, name VARCHAR(255) NOT NULL, country LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', site_language LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', site_language_selected LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', sale_status LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', lead_status LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', min_amount NUMERIC(10, 0) NOT NULL, payment NUMERIC(10, 0) NOT NULL, INDEX IDX_98CB9C9F44F5D008 (brand_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commission_plan ADD CONSTRAINT FK_98CB9C9F44F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
        $this->addSql('DROP TABLE spot_commission_plan');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE spot_commission_plan (id INT AUTO_INCREMENT NOT NULL, brand_id INT DEFAULT NULL, type INT NOT NULL, name VARCHAR(255) NOT NULL, priority SMALLINT NOT NULL, country LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', site_language LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', site_language_selected LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', sale_status LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', lead_status LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', min_amount NUMERIC(10, 0) NOT NULL, payment NUMERIC(10, 0) NOT NULL, INDEX IDX_18D399EB44F5D008 (brand_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE spot_commission_plan ADD CONSTRAINT FK_18D399EB44F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
        $this->addSql('DROP TABLE commission_plan');
    }
}
