<?php

use App\Models\Bill;
use App\Models\Pricing;
use Livewire\Volt\Component;
use App\Models\User;
use Livewire\Attributes;
use App\Livewire\Forms\BillForm;

new class extends Component {
    public BillForm $form;
    public Bill $bill;
    public array $datePickerConfig = [
        'dateFormat' => 'Y-m-d',
        'altFormat' => 'd/m/Y',
        'locale' => [
            'firstDayOfWeek' => 1
        ],

    ];

    public function mount()
    {
        $this->form->setBill($this->bill);
        $this->form->fillDefaultFields();
        $this->form->calculateTotals();
    }

    public function with(): array
    {
        return [
            'customers' => User::where('is_customer', true)->get()
        ];
    }

    public function updated($property)
    {
        match ($property) {
            'form.date_start', 'form.date_end' => $this->form->calculateDays(),
            'form.customer_id' => $this->form->addSite(),
            default => ''

        };
        $this->form->calculateTotals();
    }

    public function save()
    {
        $this->form->update();
        $this->redirect(route('bills'),navigate: true);
    }

}; ?>

<div>
    <x-header title="Update Bill" separator>
        <x-slot name="actions">
            <x-button label="Save" spinner="save" type="submit" form="billForm" class="btn-primary"/>
        </x-slot>
    </x-header>
    <div class="row">
        <div class="column-responsive column">
            <div class="bills form content">
                <x-form wire:submit="save" id="billForm" no-seperator>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="">
                            <x-choices-offline
                                label="Customer Name"
                                wire:model.live.debounce="form.customer_id"
                                :options="$customers"
                                option-avatar=""
                                option-label="name"
                                option-sub-label="site"
                                single
                                searchable/>
                        </div>
                        <div class="">
                            <x-input
                                label="Site"
                                wire:model="form.site"
                                placeholder="Site"/>
                        </div>
                        <div class="">
                            <x-input
                                label="Mukam"
                                wire:model="form.mukam"
                                placeholder="Mukam"/>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="">
                            <x-datepicker
                                label="From Date"
                                wire:model.live.debounce="form.date_start"
                                icon="o-calendar"
                                :config="$datePickerConfig"/>
                        </div>
                        <div class="">
                            <x-datepicker
                                label="To Date"
                                wire:model.live.debounce="form.date_end"
                                icon="o-calendar"
                                :config="$datePickerConfig"/>
                        </div>
                        <div class="">
                            <x-input
                                label="Days"
                                wire:model.live.debounce="form.days"
                                type="number"/>
                        </div>
                    </div>
                    <hr class="mt-4 mb-4"/>
                    <div class="grid grid-cols-4 gap-4">
                        <div class="">
                            <x-input
                                label="Brass Price"
                                wire:model.live.debounce="form.brass_price"
                                type="number"/>
                        </div>
                        <div class="">
                            <x-input
                                label="Plate Price"
                                wire:model.live.debounce="form.plate_price"
                                type="number"/>
                        </div>
                        <div class="">
                            <x-input
                                label="Aadi Price"
                                wire:model.live.debounce="form.aadi_price"
                                type="number"/>
                        </div>
                        <div class="">
                            <x-input
                                label="Teka Price"
                                wire:model.live.debounce="form.teka_price"
                                type="number"/>
                        </div>

                    </div>
                    <hr class="mt-4 mb-4"/>
                    <div class="grid grid-cols-4 gap-4">
                        <div class="">
                            <x-input
                                label="Plate 3X2"
                                wire:model.live.debounce="form.plates_3_2"
                                type="number"
                            />
                        </div>
                        <div class="">
                            <x-input
                                label="Plate 3X1ii"
                                wire:model.live.debounce="form.plates_3_1ii"
                                type="number"
                            />
                        </div>
                        <div class="">
                            <x-input
                                label="Plate 3X1i"
                                wire:model.live.debounce="form.plates_3_1i"
                                type="number"
                            />
                        </div>
                        <div class="">
                            <x-input
                                label="Plate 3X1"
                                wire:model.live.debounce="form.plates_3_1"
                                type="number"
                            />
                        </div>
                    </div>
                    <div class="grid grid-cols-4 gap-4">
                        <div class="">
                            <x-input
                                label="Teka"
                                wire:model.live.debounce="form.teka"
                                type="number"
                            />
                        </div>
                        <div class="">
                            <x-input
                                label="Aadi"
                                wire:model.live.debounce="form.aadi"
                                type="number"
                            />
                        </div>
                        <div class="">
                            <x-input
                                label="Majuri"
                                wire:model.live.debounce="form.majuri"
                                type="number"
                            />
                        </div>
                        <div class="">
                            <x-input
                                label="Deposit"
                                wire:model.live.debounce="form.deposit"
                                type="number"
                            />
                        </div>
                    </div>
                    <hr class="mt-4 mb-4"/>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-2">
                            <table class="table table-zebra text-right text-right-table">
                                <tbody>
                                <tr>
                                    <td><b>Total Brass</b></td>
                                    <td><span class="regularBrass">{{ $form->regularBrass }}</span></td>
                                    <td><span class="regularBrassAmt">{{ $form->regularBrassAmt }}</span></td>
                                </tr>
                                <tr>
                                    <td>Extra Plate Brass</td>
                                    <td><span class="extraPlateBrass">{{ $form->extraPlateBrass }}</span></td>
                                    <td><span class="extraPlateBrassAmt">{{ $form->extraPlateBrassAmt }}</span></td>
                                </tr>
                                <tr>
                                    <td>Extra Aadi Brass</td>
                                    <td><span class="extraAadiBrass">{{ $form->extraAadiBrass }}</span></td>
                                    <td><span class="extraAadiBrassAmt">{{ $form->extraAadiBrassAmt }}</span></td>
                                </tr>
                                <tr>
                                    <td>Extra Teka Brass</td>
                                    <td><span class="extraTekaBrass">{{ $form->extraTekaBrass }}</span></td>
                                    <td><span class="extraTekaBrassAmt">{{ $form->extraTekaBrassAmt }}</span></td>
                                </tr>
                                <tr>
                                    <td>Majuri</td>
                                    <td><span class="">&nbsp;</span></td>
                                    <td><span class="majuriAmt">{{ $form->majuri }}</span></td>
                                </tr>
                                <tr>
                                    <td><b>Grand Total</b></td>
                                    <td><span class="">&nbsp;</span></td>
                                    <td><span class="gTotalAmt">{{ $form->gTotalAmt }}</span></td>
                                </tr>
                                <tr>
                                    <td>Deposit</td>
                                    <td><span class="">&nbsp;</span></td>
                                    <td><span class="depositAmt">{{ $form->depositAmt }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Final</td>
                                    <td><span class="">&nbsp;</span></td>
                                    <td><span class="finalAmt">{{ $form->total }}</span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="">
                            <x-input
                                label="Total"
                                wire:model="form.total"
                                type="number" step="0.01"/>
                        </div>
                    </div>
                </x-form>
            </div>
        </div>
    </div>

</div>
