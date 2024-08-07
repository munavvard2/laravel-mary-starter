<?php

use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Country;


new class extends Component {
    use Toast, WithPagination;

    public int $country_id = 0;

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
        $this->warning("Will delete #$id", 'It is fake.', position: 'toast-bottom');
    }

    // Table headers
    public function headers(): array
    {
        return [
            ['key' => 'avatar', 'label' => '', 'class' => 'w-1'],
            ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'name', 'label' => 'Name', 'class' => 'w-64'],
            ['key' => 'country_name', 'label' => 'Country'],
            ['key' => 'email', 'label' => 'E-mail', 'sortable' => false],

        ];
    }

    /**
     * For demo purpose, this is a static collection.
     *
     * On real projects you do it with Eloquent collections.
     * Please, refer to maryUI docs to see the eloquent examples.
     */
    public function users(): LengthAwarePaginator
    {
        return User::query()
            ->with(['country'])
            ->withAggregate('country', 'name')
            ->when($this->search, fn(Builder $q) => $q->where('name', 'like', "%$this->search%"))
            ->when($this->country_id, fn(Builder $q) => $q->where('country_id', $this->country_id))
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
        if (!empty($this->country_id) && $this->country_id != 0) {
            $c++;
        }
        return $c;
    }

    public function with(): array
    {
        return [
            'users' => $this->users(),
            'headers' => $this->headers(),
            'countries' => Country::all(),
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
              <x-button label="Create" link="users/create" responsive icon="o-plus" class="btn-primary" />

        </x-slot:actions>
    </x-header>

    <!-- TABLE  -->
    <x-card>
        <x-table :headers="$headers" :rows="$users" :sort-by="$sortBy" link="users/{id}/edit" with-pagination>
            @scope('cell_avatar', $user)
            <x-avatar image="{{ $user->avatar ?? '/empty-user.jpg' }}" class="!w-10"/>
            @endscope
            @scope('actions', $user)
            <div class="flex">
                <x-button icon="o-pencil-square" link="users/{{ $user['id'] }}/edit"
                          class="btn-ghost btn-sm text-white-500"/>
                <x-button icon="o-trash" wire:click="delete({{ $user['id'] }})" wire:confirm="Are you sure?" spinner
                          class="btn-ghost btn-sm text-red-500"/>
            </div>
            @endscope
        </x-table>
    </x-card>

    <!-- FILTER DRAWER -->
    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <div class="grid gap-5">
            <x-input placeholder="Search..." wire:model.live.debounce="search" icon="o-magnifying-glass"
                     @keydown.enter="$wire.drawer = false"/>
            <x-select placeholder="Country" wire:model.live="country_id" :options="$countries" icon="o-flag"
                      placeholder-value="0"/>
        </div>


        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner/>
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false"/>
        </x-slot:actions>
    </x-drawer>
</div>
