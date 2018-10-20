<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181020164950 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE micro_posts ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE micro_posts ADD CONSTRAINT FK_98F7A52CA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_98F7A52CA76ED395 ON micro_posts (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE micro_posts DROP FOREIGN KEY FK_98F7A52CA76ED395');
        $this->addSql('DROP INDEX IDX_98F7A52CA76ED395 ON micro_posts');
        $this->addSql('ALTER TABLE micro_posts DROP user_id');
    }
}
