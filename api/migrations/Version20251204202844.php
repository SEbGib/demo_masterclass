<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add priority field to task table.
 *
 * Issue #4: Add priority field to Task (low/medium/high/urgent)
 */
final class Version20251204202844 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add priority field to task table with default value medium for issue #4';
    }

    public function up(Schema $schema): void
    {
        // Add priority column with default value 'medium' for existing rows
        $this->addSql("ALTER TABLE task ADD priority VARCHAR(20) NOT NULL DEFAULT 'medium'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE task DROP priority');
    }
}
