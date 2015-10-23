<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150919172425 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE spot_commission (id INT AUTO_INCREMENT NOT NULL, brand_id INT DEFAULT NULL, type INT NOT NULL, name VARCHAR(255) NOT NULL, priority SMALLINT NOT NULL, country LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', site_language LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', site_language_selected LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', sale_status LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', lead_status LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', min_amount NUMERIC(10, 0) NOT NULL, INDEX IDX_30FBF55B44F5D008 (brand_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE spot_commission ADD CONSTRAINT FK_30FBF55B44F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
        $this->addSql('DROP TABLE spot_brand_setting');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE spot_brand_setting (id INT AUTO_INCREMENT NOT NULL, brand_id INT DEFAULT NULL, campaign_id INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_1D6E396C44F5D008 (brand_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE spot_brand_setting ADD CONSTRAINT FK_1D6E396C44F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
        $this->addSql('DROP TABLE spot_commission');
    }
}
