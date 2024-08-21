<?php

namespace App\Livewire\Forms;

use App\Models\Bill;
use App\Models\Pricing;
use App\Models\User;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Number;
use Livewire\Attributes\Validate;
use Livewire\Form;
use phpDocumentor\Reflection\Type;

class BillForm extends Form
{
    public ?Bill $bill;

    #[Validate('required|integer')]
    public int $customer_id = 0;
    public int $user_id = 0;

    public string $site = "";
    public string $mukam = "";
    public string $date_start = "";
    public string $date_end = "";
    public int $days = 0;
    public array $priceConfig = [];
    public ?float $brass_price = 0.00;
    public ?float $plate_price = 0.00;
    public ?float $aadi_price = 0.00;
    public ?float $teka_price = 0.00;

    public ?int $plates_3_2 = 0;
    public ?int $plates_3_1ii = 0;
    public ?int $plates_3_1i = 0;
    public ?int $plates_3_1 = 0;
    public ?int $teka = 0;
    public ?int $aadi = 0;
    public ?float $deposit = 0.00;

    public ?float $regularBrass = 0;
    public ?float $regularBrassAmt = 0.00;
    public ?float $extraPlateBrass = 0.00;
    public ?float $extraPlateBrassAmt = 0.00;
    public ?float $extraAadiBrass = 0.00;
    public ?float $extraAadiBrassAmt = 0.00;
    public ?float $extraTekaBrass = 0.00;
    public ?float $extraTekaBrassAmt = 0.00;
    public ?float $majuri = 0.00;
    public ?float $gTotalAmt = 0.00;
    public ?float $depositAmt = 0.00;
    public ?float $total = 0.00;


    public function store()
    {
        $this->validate();
        Bill::create($this->all());
    }

    public function setBill(Bill $bill)
    {
        $this->bill = $bill;
        $this->fill($bill);
    }

    public function update()
    {
        $this->validate();
        $this->bill->update($this->all());
    }

    public function calculateDays()
    {
        $this->days = (strtotime($this->date_end) - strtotime($this->date_start)) / (60 * 60 * 24) + 1;
    }

    public function getAmount($brass, $days, $type)
    {
        return round($brass * ($this->priceConfig[$type] ?? 0) * $days, 2);
    }

    public function calculateTotals()
    {
        $totalPlates = $this->plates_3_2 + $this->plates_3_1ii + $this->plates_3_1i + $this->plates_3_1;
        $totalAadi = $this->aadi;
        $totalTeka = $this->teka;

        $plateBrass = round($totalPlates / 16, 2);
        $aadiBrass = round($totalAadi / 40, 2);
        $tekaBrass = round($totalTeka / 16, 2);

        $minBrass = min($plateBrass, $aadiBrass, $tekaBrass);
        $this->regularBrass = $minBrass;
        $this->extraPlateBrass = $plateBrass - $minBrass;
        $this->extraAadiBrass = $aadiBrass - $minBrass;
        $this->extraTekaBrass = $tekaBrass - $minBrass;

        $this->priceConfig = [
            'brass' => $this->brass_price / 15,
            'plate' => $this->plate_price / 15,
            'aadi' => $this->aadi_price / 15,
            'teka' => $this->teka_price / 15,
        ];

        $this->regularBrassAmt = $this->getAmount($minBrass, $this->days, 'brass');
        $this->extraAadiBrassAmt = $this->getAmount($this->extraAadiBrass, $this->days, 'aadi');
        $this->extraPlateBrassAmt = $this->getAmount($this->extraPlateBrass, $this->days, 'plate');
        $this->extraTekaBrassAmt = $this->getAmount($this->extraTekaBrass, $this->days, 'teka');

        $this->gTotalAmt = $this->regularBrassAmt + $this->extraPlateBrassAmt + $this->extraAadiBrassAmt + $this->extraTekaBrassAmt + $this->majuri;
        $this->depositAmt = $this->deposit;
        $this->total = $this->gTotalAmt - $this->depositAmt;
    }

    public function fillDefaultFields()
    {
        $pricings = Pricing::first();
        $this->fill([
            'date_start' => date('Y-m-d', strtotime('today')),
            'date_end' => date('Y-m-d', strtotime('+15 day')),
            'user_id' => auth()->user()->id,
            'days' => 15,
            'brass_price' => $pricings->brass_price,
            'plate_price' => $pricings->extra_plate_price,
            'aadi_price' => $pricings->extra_aadi_price,
            'teka_price' => $pricings->extra_teka_price,
            'majuri_price' => $pricings->majuri_price
        ]);
    }

    public function addSite()
    {
        $this->site = User::find($this->customer_id)->site;
    }

}
