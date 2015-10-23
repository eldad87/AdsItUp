<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151011234420 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE brand_record CHANGE total_positions_count total_games_count INT NOT NULL, CHANGE is_server_pixel_pending is_commission_granted TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE pixel_log ADD event INT DEFAULT 1 NOT NULL, ADD origin_type INT NOT NULL, ADD attempts INT NOT NULL, CHANGE type destination_type INT NOT NULL, CHANGE is_success status TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE fos_user ADD lead_pixel LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:object)\', ADD customer_pixel LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:object)\', ADD deposit_pixel LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:object)\', ADD game_pixel LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:object)\', DROP pixel_type, DROP pixel_url, DROP pixel_action');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE brand_record CHANGE total_games_count total_positions_count INT NOT NULL, CHANGE is_commission_granted is_server_pixel_pending TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE fos_user ADD pixel_type INT DEFAULT 1 NOT NULL, ADD pixel_url LONGTEXT NOT NULL, ADD pixel_action INT DEFAULT 1 NOT NULL, DROP lead_pixel, DROP customer_pixel, DROP deposit_pixel, DROP game_pixel');
        $this->addSql('ALTER TABLE pixel_log ADD type INT NOT NULL, DROP event, DROP destination_type, DROP origin_type, DROP attempts, CHANGE status is_success TINYINT(1) NOT NULL');
    }
}
