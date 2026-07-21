<?php

use App\Models\Lga;
use App\Models\PollingUnit;
use App\Models\Ward;
use Livewire\Attributes\Computed;
use Livewire\Component;


new class extends Component
{
    public ?int $selectedLga = null;

    public ?int $selectedWard = null;

    public ?int $selectedPollingUnit = null;

    public function updatedSelectedLga()
    {
        $this->selectedWard = null;
        $this->selectedPollingUnit = null;
    }

    public function updatedSelectedWard()
    {
        $this->selectedPollingUnit = null;
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
    public function pollingUnits()
    {
        if ($this->selectedWard === null) {
            return collect();
        }

        return PollingUnit::query()
                ->where('ward_id', $this->selectedWard)
                ->orderBy('polling_unit_name')
                ->orderBy('polling_unit_number')
                ->get();
    }

    #[Computed]
public function pollingUnit()
{
    if ($this->selectedPollingUnit === null) {
        return null;
    }

    return PollingUnit::query()
        ->with([
            'ward',
            'lga',
            'results' => fn ($query) => $query->orderBy('party_abbreviation'),
        ])
        ->find($this->selectedPollingUnit);
}

    #[Computed]
    public function totalVotes()
    {
        if ($this->pollingUnit === null) {
            return 0;
        }

        return $this->pollingUnit->results->sum('party_score');
    }
};

?>
<div>
    <div class="min-h-screen bg-gray-100 py-10">
        <div class="mx-auto max-w-7xl px-4">

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                Polling Unit Result
            </h1>

            <p class="mt-2 text-gray-600">
                View election results for an individual polling unit.
            </p>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">

            <div class="grid gap-6 md:grid-cols-3">

                <!-- LGA -->

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        Local Government
                    </label>

                    <select
                        wire:model.live="selectedLga"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">Select LGA</option>

                        @foreach($this->lgas as $lga)
                            <option value="{{ $lga->lga_id }}">
                                {{ $lga->lga_name }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <!-- Ward -->

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        Ward
                    </label>

                    <select
                        wire:model.live="selectedWard"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        @disabled($this->selectedLga === null)
                    >
                        <option value="">Select Ward</option>

                        @foreach($this->wards as $ward)
                            <option value="{{ $ward->ward_id }}">
                                {{ $ward->ward_name }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <!-- Polling Unit -->

                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">
                        Polling Unit
                    </label>
                    <select
                        wire:model.live="selectedPollingUnit"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        @disabled($this->selectedWard === null)
                    >
                        <option value="">Select Polling Unit</option>

                        @foreach($this->pollingUnits as $unit)
    <option value="{{ $unit->uniqueid }}">
        {{ $unit->polling_unit_number }}
        @if($unit->polling_unit_name)
            - {{ $unit->polling_unit_name }}
        @endif
    </option>
@endforeach

                    </select>
                </div>
<p>
    <span class="font-semibold">Total Votes:</span>
    {{ number_format($this->totalVotes) }}
</p>
            </div>

        </div>
    </div>
    @if ($this->pollingUnit)

<div class="mt-8 rounded-xl bg-white p-6 shadow">

    <div class="border-b pb-4">

        <h2 class="text-xl font-bold">
            {{ $this->pollingUnit->polling_unit_name }}
        </h2>

        <div class="mt-2 grid gap-2 text-sm text-gray-600 md:grid-cols-3">

            <p>
                <span class="font-semibold">Polling Unit Number:</span>

                {{ $this->pollingUnit->polling_unit_number }}
            </p>

            <p>
                <span class="font-semibold">Ward:</span>

                {{ $this->pollingUnit->ward?->ward_name }}
            </p>

            <p>
                <span class="font-semibold">LGA:</span>

                {{ $this->pollingUnit->lga?->lga_name }}
            </p>

        </div>

    </div>

    <div class="mt-6 overflow-x-auto">

        <table class="min-w-full divide-y divide-gray-200">

            <thead class="bg-gray-100">

                <tr>

                    <th class="px-4 py-3 text-left">
                        Party
                    </th>

                    <th class="px-4 py-3 text-right">
                        Score
                    </th>

                </tr>

            </thead>

            <tbody class="divide-y divide-gray-200 bg-white">

                @foreach($this->pollingUnit->results as $result)

                    <tr>

                        <td class="px-4 py-3">

                            {{ $result->party_abbreviation }}

                        </td>

                        <td class="px-4 py-3 text-right font-semibold">

                            {{ number_format($result->party_score) }}

                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>
@endif
@if($this->selectedWard !== null && $this->pollingUnits->isEmpty())
    <div class="mt-3 rounded-lg border border-yellow-300 bg-yellow-50 p-3 text-sm text-yellow-800">
        No polling units were found for the selected ward.
    </div>
@endif
</div>
</div>
