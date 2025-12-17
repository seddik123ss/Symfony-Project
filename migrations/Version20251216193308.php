<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251216193308 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Corriger les statuts invalides "payement en cours" en "en attente"';
    }

    public function up(Schema $schema): void
    {
        // Corriger les statuts invalides "payement en cours" en "en attente"
        // (car c'est un statut intermédiaire qui n'est pas encore "paye")
        $this->addSql("UPDATE sale SET status = 'en attente' WHERE status = 'payement en cours'");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
