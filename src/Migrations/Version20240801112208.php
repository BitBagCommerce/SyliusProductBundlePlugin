<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusProductBundlePlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240801112208 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE bitbag_product_bundle (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, is_packed_product TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_9EBE7ABF4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitbag_product_bundle_item (id INT AUTO_INCREMENT NOT NULL, product_variant_id INT NOT NULL, product_bundle_id INT NOT NULL, quantity INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_F429FEB6A80EF684 (product_variant_id), INDEX IDX_F429FEB69F5A6F5E (product_bundle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bitbag_product_bundle_order_item (id INT AUTO_INCREMENT NOT NULL, product_variant_id INT NOT NULL, order_item_id INT NOT NULL, product_bundle_item_id INT NOT NULL, quantity INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_A615CDA9A80EF684 (product_variant_id), INDEX IDX_A615CDA9E415FB15 (order_item_id), INDEX IDX_A615CDA9B7FE950B (product_bundle_item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bitbag_product_bundle ADD CONSTRAINT FK_9EBE7ABF4584665A FOREIGN KEY (product_id) REFERENCES sylius_product (id)');
        $this->addSql('ALTER TABLE bitbag_product_bundle_item ADD CONSTRAINT FK_F429FEB6A80EF684 FOREIGN KEY (product_variant_id) REFERENCES sylius_product_variant (id)');
        $this->addSql('ALTER TABLE bitbag_product_bundle_item ADD CONSTRAINT FK_F429FEB69F5A6F5E FOREIGN KEY (product_bundle_id) REFERENCES bitbag_product_bundle (id)');
        $this->addSql('ALTER TABLE bitbag_product_bundle_order_item ADD CONSTRAINT FK_A615CDA9A80EF684 FOREIGN KEY (product_variant_id) REFERENCES sylius_product_variant (id)');
        $this->addSql('ALTER TABLE bitbag_product_bundle_order_item ADD CONSTRAINT FK_A615CDA9E415FB15 FOREIGN KEY (order_item_id) REFERENCES sylius_order_item (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bitbag_product_bundle_order_item ADD CONSTRAINT FK_A615CDA9B7FE950B FOREIGN KEY (product_bundle_item_id) REFERENCES bitbag_product_bundle_item (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bitbag_product_bundle DROP FOREIGN KEY FK_9EBE7ABF4584665A');
        $this->addSql('ALTER TABLE bitbag_product_bundle_item DROP FOREIGN KEY FK_F429FEB6A80EF684');
        $this->addSql('ALTER TABLE bitbag_product_bundle_item DROP FOREIGN KEY FK_F429FEB69F5A6F5E');
        $this->addSql('ALTER TABLE bitbag_product_bundle_order_item DROP FOREIGN KEY FK_A615CDA9A80EF684');
        $this->addSql('ALTER TABLE bitbag_product_bundle_order_item DROP FOREIGN KEY FK_A615CDA9E415FB15');
        $this->addSql('ALTER TABLE bitbag_product_bundle_order_item DROP FOREIGN KEY FK_A615CDA9B7FE950B');
        $this->addSql('DROP TABLE bitbag_product_bundle');
        $this->addSql('DROP TABLE bitbag_product_bundle_item');
        $this->addSql('DROP TABLE bitbag_product_bundle_order_item');
    }
}
