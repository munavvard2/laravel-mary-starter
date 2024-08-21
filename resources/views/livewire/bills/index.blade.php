<?php

use App\Models\Bill;
use Illuminate\Support\Collection;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;


new class extends Component {
    use Toast, WithPagination;

    public int $customer_id = 0;

    public string $search = '';

    public bool $drawer = false;

    public array $sortBy = ['column' => 'id', 'direction' => 'desc'];

    // Clear filters
    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }

    // Delete action
    public function delete($id): void
    {
//        $this->warning("Will delete #$id", 'It is fake.', position: 'toast-bottom');
    }

    // Table headers
    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'customer_name', 'label' => 'Customer', 'class' => 'w-64'],
            ['key' => 'customer_site', 'label' => 'Site', 'class' => 'w-64'],
            ['key' => 'mukam', 'label' => 'Mukam', 'class' => 'w-64'],
            ['key' => 'from_to_date', 'label' => 'Dates', 'sortable' => false],
            ['key' => 'total', 'label' => 'Total', 'sortable' => false],
        ];
    }

    public function bills(): LengthAwarePaginator
    {
        return Bill::query()
//            ->with(['customer'])
            ->withAggregate('customer', 'name')
            ->withAggregate('customer', 'site')
            ->when($this->search, fn(Builder $q) => $q->where('mukam', 'like', "%$this->search%"))
            ->when($this->customer_id, fn(Builder $q) => $q->where('customer_id', $this->customer_id))
            ->orderBy(...array_values($this->sortBy))
            ->paginate(10);
        // ->get();
        // return collect([
        //     ['id' => 1, 'name' => 'Mary', 'email' => 'mary@mary-ui.com', 'age' => 23],
        //     ['id' => 2, 'name' => 'Giovanna', 'email' => 'giovanna@mary-ui.com', 'age' => 7],
        //     ['id' => 3, 'name' => 'Marina', 'email' => 'marina@mary-ui.com', 'age' => 5],
        // ])
        //     ->sortBy([[...array_values($this->sortBy)]])
        //     ->when($this->search, function (Collection $collection) {
        //         return $collection->filter(fn(array $item) => str($item['name'])->contains($this->search, true));
        //     });
    }

    public function filterCount()
    {
        $c = 0;
        if (!empty($this->search)) {
            $c++;
        }
        if (!empty($this->customer_id) && $this->customer_id != 0) {
            $c++;
        }
        return $c;
    }

    public function with(): array
    {
        return [
            'bills' => $this->bills(),
            'headers' => $this->headers(),
            'customers' => User::where('is_customer', true)->get(),
            'filterCount' => $this->filterCount()
        ];
    }

    public function updated($property): void
    {
        if (!is_array($property) && $property != '') {
            $this->resetPage();
        }
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Hello" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass"/>
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Filters" @click="$wire.drawer = true" badge="{{ $filterCount }}" responsive
                      icon="o-funnel"/>
            <x-button label="Create" link="bills/create" responsive icon="o-plus" class="btn-primary"/>

        </x-slot:actions>
    </x-header>

    <!-- TABLE  -->
    <x-card>
        <x-table :headers="$headers" :rows="$bills" :sort-by="$sortBy" link="bills/{id}/edit" striped with-pagination>
            @scope('cell_from_to_date', $bill)
            {{ $bill->date_start }} - {{ $bill->date_end }}
            @endscope
            @scope('actions', $bill)
            <div class="flex">
                <x-button icon="o-pencil-square" link="bills/{{ $bill['id'] }}/edit"
                          class="btn-ghost btn-sm text-white-500"/>
                <x-button icon="o-trash" wire:click="delete({{ $bill['id'] }})" wire:confirm="Are you sure?" spinner
                          class="btn-ghost btn-sm text-red-500"/>
            </div>
            @endscope
        </x-table>
    </x-card>

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <div class="grid gap-5">
            <x-input placeholder="Search By Mukam..." wire:model.live.debounce="search" icon="o-magnifying-glass"
                     @keydown.enter="$wire.drawer = false"/>
            <x-select placeholder="Customer" wire:model.live="customer_id" :options="$customers" icon="o-flag"
                      placeholder-value="0"/>
        </div>


        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner/>
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false"/>
        </x-slot:actions>
    </x-drawer>
</div>
