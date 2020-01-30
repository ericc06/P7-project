<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200129171135 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE end_user DROP FOREIGN KEY FK_A3515A0D19EB6921');
        $this->addSql('DROP INDEX IDX_A3515A0D19EB6921 ON end_user');
        $this->addSql('ALTER TABLE end_user CHANGE client_id reseller_id INT NOT NULL');
        $this->addSql('ALTER TABLE end_user ADD CONSTRAINT FK_A3515A0D91E6A19D FOREIGN KEY (reseller_id) REFERENCES reseller (id)');
        $this->addSql('CREATE INDEX IDX_A3515A0D91E6A19D ON end_user (reseller_id)');
        $this->addSql('ALTER TABLE oauth2_clients DROP type');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE end_user DROP FOREIGN KEY FK_A3515A0D91E6A19D');
        $this->addSql('DROP INDEX IDX_A3515A0D91E6A19D ON end_user');
        $this->addSql('ALTER TABLE end_user CHANGE reseller_id client_id INT NOT NULL');
        $this->addSql('ALTER TABLE end_user ADD CONSTRAINT FK_A3515A0D19EB6921 FOREIGN KEY (client_id) REFERENCES oauth2_clients (id)');
        $this->addSql('CREATE INDEX IDX_A3515A0D19EB6921 ON end_user (client_id)');
        $this->addSql('ALTER TABLE oauth2_clients ADD type VARCHAR(150) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
