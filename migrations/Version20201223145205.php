<?php

/**
 * @author      Philippe Vandermoere <vandermoere.philippe@gmail.com>
 * @copyright   (c) Philippe Vandermoere
 * @license     MIT
 */

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20201223145205 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create user table';
    }

    public function up(Schema $schema): void
    {
        $this->checkPlatform();
        $this->addSql(
            <<<'SQL'
CREATE TABLE users (
    id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)',
    lastname VARCHAR(250) NOT NULL,
    firstname VARCHAR(250) NOT NULL,
    PRIMARY KEY(id)
)
DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB;
SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->checkPlatform();
        $this->addSql('DROP TABLE users;');
    }

    // CREATE TABLE or DROP TABLE or ALTER TABLE aren't transactional
    public function isTransactional(): bool
    {
        return false;
    }

    protected function checkPlatform(): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );
    }
}
