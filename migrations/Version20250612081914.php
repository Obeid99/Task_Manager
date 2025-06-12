<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Performance optimization migration: Add database indexes for better query performance
 */
final class Version20250612081914 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add performance indexes for tasks, users, and related entities';
    }

    public function up(Schema $schema): void
    {
        // Helper function to safely create indexes
        $this->createIndexSafely('task', 'IDX_527EDB25E3BD61CE', ['created_at']);
        $this->createIndexSafely('task', 'IDX_527EDB2589C24C6E', ['finished']);
        $this->createIndexSafely('task', 'IDX_527EDB25AA9E377A', ['due_date']);
        $this->createIndexSafely('task', 'IDX_527EDB25F4BD7827_89C24C6E', ['assigned_to_id', 'finished']);

        // User table indexes
        $this->createIndexSafely('user', 'IDX_8D93D649A9D1C132', ['first_name']);
        $this->createIndexSafely('user', 'IDX_8D93D649C808BA5A', ['last_name']);
        $this->createIndexSafely('user', 'IDX_8D93D6496C6E55B5', ['job_title']);

        // Education table indexes (skip user_id as it already exists via FK)
        $this->createIndexSafely('education', 'IDX_DB0A59818C7A1510', ['start_year']);
        $this->createIndexSafely('education', 'IDX_DB0A5981F7A2C2FC', ['degree']);

        // Work Experience table indexes (skip user_id as it already exists via FK)
        $this->createIndexSafely('work_experience', 'IDX_1EF36CD08C7A1510', ['start_year']);

        // Skills table indexes (skip user_id as it already exists via FK)
        $this->createIndexSafely('skill', 'IDX_D531167064C19C1', ['category']);
        $this->createIndexSafely('skill', 'IDX_D53116705E237E06', ['name']);
        $this->createIndexSafely('skill', 'IDX_D53116709A1887DC', ['level']);
    }

    private function createIndexSafely(string $tableName, string $indexName, array $columns): void
    {
        try {
            $columnList = implode(', ', $columns);
            $this->addSql("CREATE INDEX {$indexName} ON {$tableName} ({$columnList})");
        } catch (\Exception $e) {
            // Index might already exist, ignore the error
            $this->write("Index {$indexName} on {$tableName} already exists or could not be created: " . $e->getMessage());
        }
    }

    public function down(Schema $schema): void
    {
        // Remove the indexes safely
        $this->dropIndexSafely('task', 'IDX_527EDB25E3BD61CE');
        $this->dropIndexSafely('task', 'IDX_527EDB2589C24C6E');
        $this->dropIndexSafely('task', 'IDX_527EDB25AA9E377A');
        $this->dropIndexSafely('task', 'IDX_527EDB25F4BD7827_89C24C6E');

        $this->dropIndexSafely('user', 'IDX_8D93D649A9D1C132');
        $this->dropIndexSafely('user', 'IDX_8D93D649C808BA5A');
        $this->dropIndexSafely('user', 'IDX_8D93D6496C6E55B5');

        $this->dropIndexSafely('education', 'IDX_DB0A59818C7A1510');
        $this->dropIndexSafely('education', 'IDX_DB0A5981F7A2C2FC');

        $this->dropIndexSafely('work_experience', 'IDX_1EF36CD08C7A1510');

        $this->dropIndexSafely('skill', 'IDX_D531167064C19C1');
        $this->dropIndexSafely('skill', 'IDX_D53116705E237E06');
        $this->dropIndexSafely('skill', 'IDX_D53116709A1887DC');
    }

    private function dropIndexSafely(string $tableName, string $indexName): void
    {
        try {
            $this->addSql("DROP INDEX {$indexName} ON {$tableName}");
        } catch (\Exception $e) {
            // Index might not exist, ignore the error
            $this->write("Index {$indexName} on {$tableName} does not exist or could not be dropped: " . $e->getMessage());
        }
    }
}
