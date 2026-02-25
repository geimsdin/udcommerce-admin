<?php

namespace Unusualdope\LaravelEcommerce\Database\Seeders;

use Illuminate\Database\Seeder;
use Unusualdope\LaravelEcommerce\Models\Language;
use Unusualdope\LaravelEcommerce\Models\Tax\Tax;
use Unusualdope\LaravelEcommerce\Models\Tax\TaxLang;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get languages
        $languages = Language::all();
        $defaultLang = Language::where('is_default', true)->first();
        $defaultLangId = $defaultLang ? $defaultLang->id : $languages->first()->id;

        // Create Taxes
        $taxes = [
            [
                'rate' => 20.000000,
                'active' => true,
                'names' => [
                    'en' => 'VAT 20%',
                    'it' => 'IVA 20%',
                ],
            ],
            [
                'rate' => 10.000000,
                'active' => true,
                'names' => [
                    'en' => 'VAT 10%',
                    'it' => 'IVA 10%',
                ],
            ],
            [
                'rate' => 22.000000,
                'active' => true,
                'names' => [
                    'en' => 'VAT 22%',
                    'it' => 'IVA 22%',
                ],
            ],
            [
                'rate' => 5.000000,
                'active' => true,
                'names' => [
                    'en' => 'VAT 5%',
                    'it' => 'IVA 5%',
                ],
            ],
            [
                'rate' => 0.000000,
                'active' => true,
                'names' => [
                    'en' => 'No Tax',
                    'it' => 'Esente',
                ],
            ],
        ];

        $createdTaxes = [];
        foreach ($taxes as $taxData) {
            $tax = Tax::create([
                'rate' => $taxData['rate'],
                'active' => $taxData['active'],
            ]);

            // Create translations
            foreach ($languages as $language) {
                $name = $taxData['names'][$language->iso_code] ?? $taxData['names']['en'] ?? 'Tax';
                TaxLang::create([
                    'id_tax' => $tax->id,
                    'id_lang' => $language->id,
                    'name' => $name,
                ]);
            }

            $createdTaxes[] = $tax;
        }

        $this->command->info('Taxes seeded successfully!');
        $this->command->info('Created:');
        $this->command->info('- ' . count($createdTaxes) . ' Taxes');
    }
}

