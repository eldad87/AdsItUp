<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150807235407 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user_extended DROP FOREIGN KEY FK_66F174544F5D008');
        $this->addSql('CREATE TABLE brand (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, host VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE fos_user_extended');
        $this->addSql('ALTER TABLE fos_user ADD brand_id INT DEFAULT NULL, ADD username VARCHAR(255) NOT NULL, ADD username_canonical VARCHAR(255) NOT NULL, ADD email VARCHAR(255) NOT NULL, ADD email_canonical VARCHAR(255) NOT NULL, ADD salt VARCHAR(255) NOT NULL, ADD password VARCHAR(255) NOT NULL, ADD last_login DATETIME DEFAULT NULL, ADD locked TINYINT(1) NOT NULL, ADD expired TINYINT(1) NOT NULL, ADD expires_at DATETIME DEFAULT NULL, ADD confirmation_token VARCHAR(255) DEFAULT NULL, ADD password_requested_at DATETIME DEFAULT NULL, ADD roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', ADD credentials_expired TINYINT(1) NOT NULL, ADD credentials_expire_at DATETIME DEFAULT NULL, DROP name, DROP host, CHANGE is_active enabled TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE fos_user ADD CONSTRAINT FK_957A647944F5D008 FOREIGN KEY (brand_id) REFERENCES fos_user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_957A647992FC23A8 ON fos_user (username_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_957A6479A0D96FBF ON fos_user (email_canonical)');
        $this->addSql('CREATE INDEX IDX_957A647944F5D008 ON fos_user (brand_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE fos_user_extended (id INT AUTO_INCREMENT NOT NULL, brand_id INT DEFAULT NULL, username VARCHAR(255) NOT NULL, username_canonical VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, email_canonical VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', credentials_expired TINYINT(1) NOT NULL, credentials_expire_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_66F174592FC23A8 (username_canonical), UNIQUE INDEX UNIQ_66F1745A0D96FBF (email_canonical), INDEX IDX_66F174544F5D008 (brand_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fos_user_extended ADD CONSTRAINT FK_66F174544F5D008 FOREIGN KEY (brand_id) REFERENCES fos_user_extended (id)');
        $this->addSql('DROP TABLE brand');
        $this->addSql('ALTER TABLE fos_user DROP FOREIGN KEY FK_957A647944F5D008');
        $this->addSql('DROP INDEX UNIQ_957A647992FC23A8 ON fos_user');
        $this->addSql('DROP INDEX UNIQ_957A6479A0D96FBF ON fos_user');
        $this->addSql('DROP INDEX IDX_957A647944F5D008 ON fos_user');
        $this->addSql('ALTER TABLE fos_user ADD name VARCHAR(255) NOT NULL, ADD host VARCHAR(255) NOT NULL, ADD is_active TINYINT(1) NOT NULL, DROP brand_id, DROP username, DROP username_canonical, DROP email, DROP email_canonical, DROP enabled, DROP salt, DROP password, DROP last_login, DROP locked, DROP expired, DROP expires_at, DROP confirmation_token, DROP password_requested_at, DROP roles, DROP credentials_expired, DROP credentials_expire_at');
    }
}
