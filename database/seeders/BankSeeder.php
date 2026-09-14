<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;

/**
 * Populates the admin-managed Bank directory (see App\Models\Bank and its
 * migration) with a starter set of real, well-known banks — both domestic
 * (type=external, offered from Send Money's "Another bank" tab and Link
 * Account's bank picker) and international (type=international, offered
 * from Send Money's "International bank" tab).
 *
 * The directory ships completely empty otherwise (there is no factory for
 * Bank), so without this, both bank pickers have nothing to show until an
 * admin manually adds entries one at a time at Admin > Banks.
 *
 * Routing numbers and SWIFT/BIC codes below are the real, publicly
 * published main-office codes for each institution (verified against
 * multiple routing-number/SWIFT lookup sources at the time this was
 * written). They identify the institution for directory/display purposes
 * in this app — they are not account numbers and carry no sensitive
 * financial data. That said, banks do occasionally update or add
 * additional branch-level codes, so if this app is ever used to actually
 * route real money, re-verify each code against the bank's own published
 * wire-transfer instructions first.
 *
 * Safe to re-run: uses updateOrCreate keyed on (name, type), so running
 * `php artisan db:seed --class=BankSeeder` again won't create duplicates —
 * it'll just refresh these rows to match the list below.
 */
class BankSeeder extends Seeder
{
    public function run(): void
    {
        foreach (self::domesticBanks() as $bank) {
            Bank::updateOrCreate(
                ['name' => $bank['name'], 'type' => 'external'],
                array_merge($bank, ['type' => 'external', 'country' => 'United States', 'currency' => 'USD', 'is_active' => true])
            );
        }

        foreach (self::internationalBanks() as $bank) {
            Bank::updateOrCreate(
                ['name' => $bank['name'], 'type' => 'international'],
                array_merge($bank, ['type' => 'international', 'is_active' => true])
            );
        }
    }

    /**
     * Major U.S. banks and credit unions, spanning the big national banks,
     * large regionals, and a few online-only/card-issuer banks, plus one
     * credit union — a realistic mix for the "Another bank" picker.
     */
    private static function domesticBanks(): array
    {
        return [
            ['name' => 'JPMorgan Chase Bank', 'routing_number' => '021000021'],
            ['name' => 'Bank of America', 'routing_number' => '026009593'],
            ['name' => 'Wells Fargo Bank', 'routing_number' => '121000248'],
            ['name' => 'Citibank', 'routing_number' => '021000089'],
            ['name' => 'U.S. Bank', 'routing_number' => '091000022'],
            ['name' => 'PNC Bank', 'routing_number' => '043000096'],
            ['name' => 'Truist Bank', 'routing_number' => '061000104'],
            ['name' => 'Capital One Bank', 'routing_number' => '051405515'],
            ['name' => 'TD Bank', 'routing_number' => '011103093'],
            ['name' => 'Fifth Third Bank', 'routing_number' => '042000314'],
            ['name' => 'Regions Bank', 'routing_number' => '062005690'],
            ['name' => 'KeyBank', 'routing_number' => '041001039'],
            ['name' => 'Citizens Bank', 'routing_number' => '011500120'],
            ['name' => 'Ally Bank', 'routing_number' => '124003116'],
            ['name' => 'Discover Bank', 'routing_number' => '031100649'],
            ['name' => 'American Express National Bank', 'routing_number' => '124085066'],
            ['name' => 'Charles Schwab Bank', 'routing_number' => '121202211'],
            ['name' => 'Synchrony Bank', 'routing_number' => '021213591'],
            ['name' => 'Navy Federal Credit Union', 'routing_number' => '256074974'],
        ];
    }

    /**
     * Major international banks spanning the regions Ledger's international
     * transfer feature is most likely to be used with — UK/EU, North
     * America, Africa, and Asia-Pacific.
     */
    private static function internationalBanks(): array
    {
        return [
            ['name' => 'HSBC UK Bank plc', 'country' => 'United Kingdom', 'swift_code' => 'HBUKGB4B', 'currency' => 'GBP'],
            ['name' => 'Barclays Bank UK PLC', 'country' => 'United Kingdom', 'swift_code' => 'BARCGB22', 'currency' => 'GBP'],
            ['name' => 'Standard Chartered Bank', 'country' => 'United Kingdom', 'swift_code' => 'SCBLGB2L', 'currency' => 'GBP'],
            ['name' => 'Deutsche Bank AG', 'country' => 'Germany', 'swift_code' => 'DEUTDEFF', 'currency' => 'EUR'],
            ['name' => 'BNP Paribas', 'country' => 'France', 'swift_code' => 'BNPAFRPP', 'currency' => 'EUR'],
            ['name' => 'Royal Bank of Canada', 'country' => 'Canada', 'swift_code' => 'ROYCCAT2', 'currency' => 'CAD'],
            ['name' => 'ICICI Bank Limited', 'country' => 'India', 'swift_code' => 'ICICINBB', 'currency' => 'INR'],
            ['name' => 'ANZ Bank', 'country' => 'Australia', 'swift_code' => 'ANZBAU3M', 'currency' => 'AUD'],
            ['name' => 'DBS Bank Ltd', 'country' => 'Singapore', 'swift_code' => 'DBSSSGSG', 'currency' => 'SGD'],
            ['name' => 'Access Bank Plc', 'country' => 'Nigeria', 'swift_code' => 'ABNGNGLA', 'currency' => 'NGN'],
            ['name' => 'Standard Bank of South Africa', 'country' => 'South Africa', 'swift_code' => 'SBZAZAJJ', 'currency' => 'ZAR'],
            ['name' => 'MUFG Bank, Ltd.', 'country' => 'Japan', 'swift_code' => 'BOTKJPJT', 'currency' => 'JPY'],
        ];
    }
}
