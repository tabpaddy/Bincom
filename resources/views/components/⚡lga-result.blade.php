<?php

use App\Models\Lga;
use App\Models\AnnouncedPuResult;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;

new class extends Component
{
    public ?int $selectedLga = null;

    #[Computed]
    public function lgas()
    {
        return Lga::orderBy('lga_name')->get();
    }

    #[Computed]
    public function lgaResults()
    {
        if ($this->selectedLga === null) {
            return collect();
        }

        return AnnouncedPuResult::query()
            ->join(
                'polling_unit',
                'polling_unit.uniqueid',
                '=',
                'announced_pu_results.polling_unit_uniqueid'
            )
            ->where('polling_unit.lga_id', $this->selectedLga)
            ->select(
                'party_abbreviation',
                DB::raw('SUM(party_score) as total_score')
            )
            ->groupBy('party_abbreviation')
            ->orderBy('party_abbreviation')
            ->get();
    }

    #[Computed]
    public function totalVotes()
    {
        return $this->lgaResults()->sum('total_score');
    }
};
?>

<div>
    <div class="min-h-screen bg-gray-100 py-10">

    <div class="mx-auto max-w-6xl px-4">

        <h1 class="text-3xl font-bold">
            LGA Result Summary
        </h1>

        <p class="mt-2 text-gray-600">
            View the summed results of all polling units under an LGA.
        </p>
    </div>

    <div class="mt-8 rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">

    <label class="mb-2 block font-medium">
        Select Local Government
    </label>

    <select
        wire:model.live="selectedLga"
        class="w-full rounded-lg border-gray-300"
    >

        <option value="">
            Select LGA
        </option>

        @foreach($this->lgas as $lga)

            <option value="{{ $lga->lga_id }}">
                {{ $lga->lga_name }}
            </option>

        @endforeach

    </select>

</div>
    @if($this->selectedLga !== null)

<div class="mt-8 rounded-xl bg-white p-6 shadow">

    <table class="min-w-full">

        <thead>

            <tr class="border-b">

                <th class="px-4 py-3 text-left">
                    Party
                </th>

                <th class="px-4 py-3 text-right">
                    Total Votes
                </th>

            </tr>

        </thead>

        <tbody>

            @forelse($this->lgaResults as $result)

                <tr class="border-b">

                    <td class="px-4 py-3">
                        {{ $result->party_abbreviation }}
                    </td>

                    <td class="px-4 py-3 text-right font-semibold">
                        {{ number_format($result->total_score) }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="2" class="py-6 text-center text-gray-500">

                        No results found.

                    </td>

                </tr>

            @endforelse

        </tbody>

        <tfoot>

            <tr class="bg-gray-100 font-bold">

                <td class="px-4 py-3">

                    TOTAL

                </td>

                <td class="px-4 py-3 text-right">

                    {{ number_format($this->totalVotes) }}

                </td>

            </tr>

        </tfoot>

    </table>

</div>

@endif
    </div>
</div>
