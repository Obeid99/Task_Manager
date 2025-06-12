<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250610082431 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE cv_upload (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, original_file_name VARCHAR(255) NOT NULL, file_name VARCHAR(255) NOT NULL, file_extension VARCHAR(50) NOT NULL, file_size INT NOT NULL, uploaded_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', is_active TINYINT(1) NOT NULL, description LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_9A3AEF3AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE education (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, institution VARCHAR(255) NOT NULL, degree VARCHAR(100) NOT NULL, field_of_study VARCHAR(150) NOT NULL, start_year INT DEFAULT NULL, end_year INT DEFAULT NULL, is_currently_studying TINYINT(1) NOT NULL, description LONGTEXT DEFAULT NULL, gpa DOUBLE PRECISION DEFAULT NULL, INDEX IDX_DB0A5ED2A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE skill (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(100) NOT NULL, category VARCHAR(50) NOT NULL, level VARCHAR(20) NOT NULL, years_of_experience INT DEFAULT NULL, is_endorsed TINYINT(1) NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_5E3DE477A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE work_experience (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, company VARCHAR(255) NOT NULL, position VARCHAR(150) NOT NULL, start_date DATE NOT NULL, end_date DATE DEFAULT NULL, is_current_position TINYINT(1) NOT NULL, description LONGTEXT DEFAULT NULL, location VARCHAR(100) DEFAULT NULL, employment_type VARCHAR(50) DEFAULT NULL, INDEX IDX_1EF36CD0A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE cv_upload ADD CONSTRAINT FK_9A3AEF3AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE education ADD CONSTRAINT FK_DB0A5ED2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE skill ADD CONSTRAINT FK_5E3DE477A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE work_experience ADD CONSTRAINT FK_1EF36CD0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE cv_upload DROP FOREIGN KEY FK_9A3AEF3AA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE education DROP FOREIGN KEY FK_DB0A5ED2A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE skill DROP FOREIGN KEY FK_5E3DE477A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE work_experience DROP FOREIGN KEY FK_1EF36CD0A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE cv_upload
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE education
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE skill
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE work_experience
        SQL);
    }
}
