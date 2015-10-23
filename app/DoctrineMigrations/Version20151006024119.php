<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151006024119 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE pixel_log (id INT AUTO_INCREMENT NOT NULL, brand_id INT DEFAULT NULL, type INT NOT NULL, action VARCHAR(4) NOT NULL, url LONGTEXT NOT NULL, response LONGTEXT DEFAULT NULL, is_success TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_E162582144F5D008 (brand_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pixel_log ADD CONSTRAINT FK_E162582144F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
        $this->addSql('ALTER TABLE commission_plan ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE brand_record ADD referrer_id INT DEFAULT NULL, ADD commission_plan_id INT DEFAULT NULL, ADD total_deposits_amount NUMERIC(10, 0) NOT NULL, ADD total_positions_count INT NOT NULL, ADD payout NUMERIC(10, 0) NOT NULL, ADD is_server_pixel_pending TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE brand_record ADD CONSTRAINT FK_18BCE39F798C22DB FOREIGN KEY (referrer_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE brand_record ADD CONSTRAINT FK_18BCE39F414CE44B FOREIGN KEY (commission_plan_id) REFERENCES brand (id)');
        $this->addSql('CREATE INDEX IDX_18BCE39F798C22DB ON brand_record (referrer_id)');
        $this->addSql('CREATE INDEX IDX_18BCE39F414CE44B ON brand_record (commission_plan_id)');
        $this->addSql('ALTER TABLE offer_click ADD parameters LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE fos_user ADD pixel_type INT NOT NULL, ADD pixel_url LONGTEXT NOT NULL, ADD pixel_action VARCHAR(4) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE pixel_log');
        $this->addSql('ALTER TABLE brand_record DROP FOREIGN KEY FK_18BCE39F798C22DB');
        $this->addSql('ALTER TABLE brand_record DROP FOREIGN KEY FK_18BCE39F414CE44B');
        $this->addSql('DROP INDEX IDX_18BCE39F798C22DB ON brand_record');
        $this->addSql('DROP INDEX IDX_18BCE39F414CE44B ON brand_record');
        $this->addSql('ALTER TABLE brand_record DROP referrer_id, DROP commission_plan_id, DROP total_deposits_amount, DROP total_positions_count, DROP payout, DROP is_server_pixel_pending');
        $this->addSql('ALTER TABLE commission_plan DROP name');
        $this->addSql('ALTER TABLE fos_user DROP pixel_type, DROP pixel_url, DROP pixel_action');
        $this->addSql('ALTER TABLE offer_click DROP parameters');
    }
}
