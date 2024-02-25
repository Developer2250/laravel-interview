<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Prize;
use App\Http\Requests\PrizeRequest;
use Illuminate\Http\Request;
use Validator;

class PrizesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $prizes = Prize::all();
        return view('prizes.index', ['prizes' => $prizes]);
    }

    public function getProbabilityStatisticsData()
    {
        $prizes = Prize::all();
        $colors = [
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(255, 205, 86)'
            
        ];
        $labels = [];
        $data = [];
        foreach ($prizes as $prize) {
            $labels[] = $prize->title.'('.$prize->probability.') %'; 
            $data[] = $prize->probability; 
        }
        $response = [
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors,
        ];

        return response()->json($response);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('prizes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  PrizeRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PrizeRequest $request)
    {
        $currentTotalProbability = Prize::sum('probability');
        $newProbability = floatval($request->input('probability'));
        $totalProbabilityAfterAddition = $currentTotalProbability + $newProbability;

        if ($totalProbabilityAfterAddition > 100) {
            return redirect()->back()->withInput()->withErrors(['probability' => 'Total probability cannot exceed 100.']);
        }

        $prize = new Prize;
        $prize->title = $request->input('title');
        $prize->probability = $newProbability;
        $prize->save();

        return redirect()->route('prizes.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $prize = Prize::findOrFail($id);
        return view('prizes.edit', ['prize' => $prize]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  PrizeRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PrizeRequest $request, $id)
    {
        $currentTotalProbability = Prize::sum('probability');
        $newProbability = floatval($request->input('probability'));
        $totalProbabilityAfterAddition = $currentTotalProbability + $newProbability;

        if ($totalProbabilityAfterAddition > 100) {
            return redirect()->back()->withInput()->withErrors(['probability' => 'Total probability cannot exceed 100.']);
        }
        
        $prize = Prize::findOrFail($id);
        $prize->title = $request->input('title');
        $prize->probability = floatval($request->input('probability'));
        $prize->save();

        return redirect()->route('prizes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $prize = Prize::findOrFail($id);
        $prize->delete();
        return redirect()->route('prizes.index');
    }

    public function simulate(Request $request)
    {
        // Validate the 'number_of_prizes' parameter
        $validator = Validator::make($request->all(), [
            'number_of_prizes' => 'required|numeric|min:1|max:100', // Ensure it's a numeric value greater than or equal to 1
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            // Construct error response
            $errors = $validator->errors()->all();
            $response = [
                'error' => 'Validation Error',
                'message' => implode(', ', $errors),
            ];
            // Return error response
            return response()->json($response, 400);
        }

        $totalWinners = $request->number_of_prizes;
        $prizes = Prize::all();
        $totalProbability = $prizes->sum('probability');

        // Calculate expected winners and actual probability for each prize
        foreach ($prizes as $prize) {
            $expectedWinners = round(($prize->probability / $totalProbability) * $totalWinners);
            $prize->winners = $expectedWinners;
            $prize->actual_probability = ($prize->winners / $totalWinners * 100);
        }

        // Redistribute remaining winners among the prizes if any
        while ($totalWinners > 0) {
            $redistributionOccurred = false; 

            // Filter prizes that still have shortfall
            $remainingPrizes = $prizes->filter(function ($prize) use ($totalWinners, $totalProbability) {
                return $prize->winners < round(($prize->probability / $totalProbability) * $totalWinners);
            });

            // Calculate total remaining shortfall
            $totalRemainingShortfall = $remainingPrizes->sum(function ($prize) use ($totalProbability, $totalWinners) {
                return round(($prize->probability / $totalProbability) * $totalWinners) - $prize->winners;
            });

            // Redistribute winners among remaining prizes
            $remainingPrizes->each(function ($prize) use (&$totalWinners, $totalRemainingShortfall, $totalProbability, &$redistributionOccurred) {
                $expectedWinners = round(($prize->probability / $totalProbability) * $totalWinners);
                $expectedShortfall = $expectedWinners - $prize->winners;
                $redistributedWinners = round(($expectedShortfall / $totalRemainingShortfall) * $totalWinners);
                if ($redistributedWinners > 0) {
                    $prize->winners += $redistributedWinners;
                    $totalWinners -= $redistributedWinners;
                    $redistributionOccurred = true; // Set the flag if redistribution occurred
                }
            });

            // Exit loop if redistribution did not occur (no more winners to redistribute)
            if (!$redistributionOccurred) {
                break;
            }
        }

        // Prepare data for response
        $colors = [
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(255, 205, 86)'
        ];

        $labels = [];
        $data = [];

        foreach ($prizes as $prize) {
            $actual_probability = number_format($prize->actual_probability, 2);
            $data[] = $actual_probability;
            $labels[] = $prize->title.'('.$actual_probability.') % ';
        }

        // Construct and return response
        $response = [
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors,
        ];

        return response()->json($response);
    }
}
