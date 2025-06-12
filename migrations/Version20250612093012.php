<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Fix foreign key constraints to allow safe user deletion
 */
final class Version20250612093012 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix foreign key constraints to allow safe user deletion with CASCADE and SET NULL options';
    }

    public function up(Schema $schema): void
    {
        // Drop existing foreign key constraints
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25F4BD7827'); // assigned_to_id
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25DE12AB56'); // created_by_id

        // Modify the created_by column to allow NULL
        $this->addSql('ALTER TABLE task MODIFY created_by_id INT DEFAULT NULL');

        // Re-add foreign key constraints with proper CASCADE/SET NULL options
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25F4BD7827 FOREIGN KEY (assigned_to_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25DE12AB56 FOREIGN KEY (created_by_id) REFERENCES user (id) ON DELETE SET NULL');

        // Note: The OneToMany relationships (education, work_experience, skill, cv_upload)
        // will be handled by Doctrine's cascade options in the entities
    }

    public function down(Schema $schema): void
    {
        // Revert the changes
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25F4BD7827');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25DE12AB56');

        // Restore original constraints (without CASCADE/SET NULL)
        $this->addSql('ALTER TABLE task MODIFY created_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25F4BD7827 FOREIGN KEY (assigned_to_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25DE12AB56 FOREIGN KEY (created_by_id) REFERENCES user (id)');
    }
}
