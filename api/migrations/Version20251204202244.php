<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add due_date field to task table.
 *
 * Issue #2: Add due date to Task with future date validation
 */
final class Version20251204202244 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add due_date field to task table for issue #2';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE task ADD due_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN task.due_date IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE task DROP due_date');
    }
}
