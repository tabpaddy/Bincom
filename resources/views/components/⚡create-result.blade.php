<?php

use App\Models\Lga;
use App\Models\Ward;
use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Party;
use App\Models\PollingUnit;
use App\Models\AnnouncedPuResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Carbon\Carbon;

new class extends Component
{
    public ?int $selectedLga = null;

    public ?int $selectedWard = null;

    public string $pollingUnitNumber = '';

    public string $pollingUnitName = '';

    public string $pollingUnitDescription = '';

    public array $scores = [];

    public function mount()
    {
        foreach ($this->parties as $party) {
            $this->scores[$party->partyid] = 0;
        }
    }

    public function updatedSelectedLga()
    {
        $this->selectedWard = null;
    }

    #[Computed]
    public function lgas()
    {
        return Lga::orderBy('lga_name')->get();
    }

    #[Computed]
    public function wards()
    {
        if ($this->selectedLga === null) {
            return collect();
        }

        return Ward::where('lga_id', $this->selectedLga)
            ->orderBy('ward_name')
            ->get();
    }

    #[Computed]
    public function parties()
    {
        return Party::orderBy('partyname')->get();
    }

    protected function rules(): array
    {
        return [
            'selectedLga' => ['required', 'integer'],
            'selectedWard' => ['required', 'integer'],
            'pollingUnitNumber' => ['required', 'max:50'],
            'pollingUnitName' => ['required', 'max:50'],
            'pollingUnitDescription' => ['nullable'],
            'scores.*' => ['required', 'integer', 'min:0'],
        ];
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {

            if (PollingUnit::where('polling_unit_number', $this->pollingUnitNumber)
                ->where('ward_id', $this->selectedWard)
                ->exists()
            ) {
                $this->addError('pollingUnitNumber', 'Polling Unit already exists.');

                return;
            }

            $pollingUnit = PollingUnit::create([
                'polling_unit_id' => 0,
                'ward_id' => $this->selectedWard,
                'lga_id' => $this->selectedLga,
                'uniquewardid' => null,
                'polling_unit_number' => $this->pollingUnitNumber,
                'polling_unit_name' => $this->pollingUnitName,
                'polling_unit_description' => $this->pollingUnitDescription,
                'lat' => null,
                'long' => null,
                'entered_by_user' => 'Bincom Test',
                'date_entered' => now(),
                'user_ip_address' => request()->ip(),
            ]);

            foreach ($this->scores as $party => $score) {
                 $abbr = $party === 'LABOUR'
                                ? 'LABO'
                                : $party;

                AnnouncedPuResult::create([
                    'polling_unit_uniqueid' => $pollingUnit->uniqueid,
                    'party_abbreviation'    => $abbr,
                    'party_score'           => $score,
                    'entered_by_user'       => 'Bincom Test',
                    'date_entered'          => Carbon::now(),
                    'user_ip_address'       => request()->ip(),
                ]);
            }
        });

        session()->flash('success', 'Polling Unit Result saved successfully.');

        $this->reset([
            'selectedLga',
            'selectedWard',
            'pollingUnitNumber',
            'pollingUnitName',
            'pollingUnitDescription',
        ]);

        foreach (Party::all() as $party) {
            $this->scores[$party->partyid] = 0;
        }
    }
};

?>

<div>
    <div class="min-h-screen bg-gray-100 py-10">

    <div class="mx-auto max-w-5xl">

        <h1 class="text-3xl font-bold">

            Create Polling Unit Result

        </h1>

        <p class="mt-2 text-gray-600">

            Add a new polling unit together with the results for all parties.

        </p>

    </div>

        <div class="mt-8 rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            @if(session('success'))

<div class="mb-6 rounded-lg border border-green-200 bg-green-100 p-4 text-green-700">
    {{ session('success') }}
</div>

@endif

            <div class="grid gap-6 md:grid-cols-2">
                <div>

<label class="mb-2 block font-medium">

Local Government

</label>

<select
    wire:model.live="selectedLga"
    class="w-full rounded-lg border-gray-300"
>

<option value="">Select LGA</option>

@foreach($this->lgas as $lga)

<option value="{{ $lga->lga_id }}">

{{ $lga->lga_name }}

</option>

@endforeach

</select>
@error('selectedLga')
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
@enderror

</div>
<div>

<label class="mb-2 block font-medium">

Ward

</label>

<select
    wire:model.live="selectedWard"
    class="w-full rounded-lg border-gray-300"
    @disabled($this->selectedLga === null)
>

<option value="">

Select Ward

</option>

@foreach($this->wards as $ward)

<option value="{{ $ward->ward_id }}">

{{ $ward->ward_name }}

</option>

@endforeach

</select>
@error('selectedWard')
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
@enderror

</div>
<div>

<label class="mb-2 block font-medium">

Polling Unit Number

</label>

<input
    type="text"
    wire:model.live="pollingUnitNumber"
    class="w-full rounded-lg border-gray-300 px-2 py-1"
/>
@error('pollingUnitNumber')
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
@enderror

</div>
<div class="md:col-span-2">

<label class="mb-2 block font-medium">

Description

</label>

<textarea
    wire:model.live="pollingUnitDescription"
    rows="3"
    class="w-full rounded-lg border-gray-300 px-2 py-1"
></textarea>
@error('pollingUnitDescription')
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
@enderror
</div>

</div>
<div>

<label class="mb-2 block font-medium">

Polling Unit Name

</label>

<input
    type="text"
    wire:model.live="pollingUnitName"
    class="w-full rounded-lg border-gray-300 px-2 py-1"
/>
@error('pollingUnitName')
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
@enderror
</div>
            </div>
            <div class="mt-8 rounded-xl bg-white p-6 shadow">

    <h2 class="mb-6 text-xl font-bold">

        Enter Party Scores

    </h2>

    <div class="grid gap-4 md:grid-cols-3">

        @foreach($this->parties as $party)

            <div>

                <label class="mb-2 block font-medium">

                    {{ $party->partyname }}

                </label>

                <input
                    type="number"
                    min="0"
                    wire:model.live="scores.{{ $party->partyid }}"
                    class="w-full rounded-lg border-gray-300 px-2 py-1"
                >
                @error('scores.' . $party->partyid)
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

            </div>

        @endforeach

    </div>

    <div class="mt-8 flex justify-end">

    <button
        wire:click="save"
        wire:loading.attr="disabled"
        class="rounded-lg bg-indigo-600 px-6 py-3 text-white hover:bg-indigo-700 disabled:opacity-50"
    >
        <span wire:loading.remove>
            Save Results
        </span>

        <span wire:loading>
            Saving...
        </span>
    </button>

</div>

</div>

        </div>
    </div>
</div>
