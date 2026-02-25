-- Example Tax Data for PrestaShop-like Tax System
-- Run this SQL after migrations if you prefer SQL over Seeder

-- Insert Taxes
INSERT INTO `taxes` (`id`, `rate`, `active`, `created_at`, `updated_at`) VALUES
(1, 20.000000, 1, NOW(), NOW()),
(2, 10.000000, 1, NOW(), NOW()),
(3, 22.000000, 1, NOW(), NOW()),
(4, 5.000000, 1, NOW(), NOW()),
(5, 0.000000, 1, NOW(), NOW());

-- Insert Tax Translations (assuming language IDs: 1 = English, 2 = Italian)
-- Adjust language IDs based on your languages table
INSERT INTO `tax_langs` (`id_tax`, `id_lang`, `name`, `created_at`, `updated_at`) VALUES
-- VAT 20%
(1, 1, 'VAT 20%', NOW(), NOW()),
(1, 3, 'IVA 20%', NOW(), NOW()),
-- VAT 10%
(2, 1, 'VAT 10%', NOW(), NOW()),
(2, 3, 'IVA 10%', NOW(), NOW()),
-- VAT 22%
(3, 1, 'VAT 22%', NOW(), NOW()),
(3, 3, 'IVA 22%', NOW(), NOW()),
-- VAT 5%
(4, 1, 'VAT 5%', NOW(), NOW()),
(4, 3, 'IVA 5%', NOW(), NOW()),
-- No Tax
(5, 1, 'No Tax', NOW(), NOW()),
(5, 3, 'Esente', NOW(), NOW());

-- Insert Tax Rule Groups
INSERT INTO `tax_rule_groups` (`id`, `name`, `active`, `created_at`, `updated_at`) VALUES
(1, 'Standard Tax Rules', 1, NOW(), NOW()),
(2, 'Italy Tax Rules', 1, NOW(), NOW()),
(3, 'EU Tax Rules', 1, NOW(), NOW()),
(4, 'No Tax Rules', 1, NOW(), NOW());

-- Insert Tax Rules
INSERT INTO `tax_rules` (`id`, `id_tax_rule_group`, `id_country`, `id_state`, `zipcode_from`, `zipcode_to`, `id_tax`, `behavior`, `description`, `created_at`, `updated_at`) VALUES
-- Standard Tax Rules: VAT 20% for all
(1, 1, NULL, NULL, NULL, NULL, 1, 0, 'Standard VAT 20%', NOW(), NOW()),
-- Standard Tax Rules: Reduced VAT 10% for zip codes 10000-19999
(2, 1, NULL, NULL, '10000', '19999', 2, 1, 'Reduced VAT 10% for zip codes 10000-19999', NOW(), NOW()),
-- Italy Tax Rules: VAT 22% for Italy (set id_country when you have countries table)
(3, 2, NULL, NULL, NULL, NULL, 3, 0, 'Italy VAT 22%', NOW(), NOW()),
-- EU Tax Rules: VAT 20% for EU countries
(4, 3, NULL, NULL, NULL, NULL, 1, 0, 'EU VAT 20%', NOW(), NOW()),
-- No Tax Rules: 0% tax
(5, 4, NULL, NULL, NULL, NULL, 5, 0, 'No Tax Applied', NOW(), NOW());

