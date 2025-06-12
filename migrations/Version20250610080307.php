<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250610080307 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // First, add the column as nullable
        $this->addSql('ALTER TABLE task ADD created_by_id INT NULL');

        // Create admin user if it doesn't exist
        $this->addSql("INSERT IGNORE INTO user (email, username, password, roles) VALUES ('admin@jems.com', 'admin', '\$2y\$13\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '[\"ROLE_ADMIN\"]')");

        // Set created_by_id to admin user for existing tasks
        $this->addSql('UPDATE task SET created_by_id = (SELECT id FROM user WHERE email = "admin@jems.com" LIMIT 1) WHERE created_by_id IS NULL');

        // Now make the column NOT NULL
        $this->addSql('ALTER TABLE task MODIFY created_by_id INT NOT NULL');

        // Add foreign key constraint
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');

        // Create index
        $this->addSql('CREATE INDEX IDX_527EDB25B03A8386 ON task (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE task DROP FOREIGN KEY FK_527EDB25B03A8386
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_527EDB25B03A8386 ON task
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE task DROP created_by_id
        SQL);
    }
}
