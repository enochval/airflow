<?php

namespace Database\Seeders;

use App\Enums\PaymentPlatformCountriesEnum;
use App\Enums\PaymentTypesEnum;
use App\Models\Country;
use App\Models\PaymentType;
use Illuminate\Database\Seeder;

class SeedPaymentTypes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (PaymentPlatformCountriesEnum::valueArray() as $country) {
            $this->seedCountrySpecificPaymentType($country);
        }

    }

    private function seedCountrySpecificPaymentType(string $country)
    {
        $data = [];
        $countryModel = Country::where('name', $country)->first();

        if (PaymentType::where('country_id', $countryModel->id)->count() == 0) {
            collect($this->paymentTypes()[$country])->map(function ($type) use (&$data, $countryModel) {
                $data[] = [
                    'country_id' => $countryModel->id,
                    'name' => $type
                ];
            });
        }

        PaymentType::insert($data);
    }

    public function paymentTypes() : array
    {
        return [
           'nigeria' => [
               PaymentTypesEnum::NET->value,
               PaymentTypesEnum::ITF->value,
               PaymentTypesEnum::PENSION->value,
               PaymentTypesEnum::NHF->value,
               PaymentTypesEnum::TAX->value,
               PaymentTypesEnum::NSITF->value,
               PaymentTypesEnum::ITF->value,
           ]
        ];
    }
}
