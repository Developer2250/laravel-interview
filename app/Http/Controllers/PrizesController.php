<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Prize;
use App\Models\DistributionData;
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
        $colors = [];
        $labels = [];
        $data = [];

        // Generate random colors for graph
        for ($i = 0; $i < count($prizes); $i++) {
            $colors[] = 'rgb(' . rand(0, 255) . ', ' . rand(0, 255) . ', ' . rand(0, 255) . ')';
        }

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
        $prize = Prize::findOrFail($id);

        $currentTotalProbability = Prize::sum('probability');
        $newProbability = floatval($request->input('probability'));

        $totalProbabilityAfterEdit = $currentTotalProbability - $prize->probability + $newProbability;

        if ($currentTotalProbability == 100 && $totalProbabilityAfterEdit > 100) {
            return redirect()->back()->withInput()->withErrors(['probability' => 'Total probability cannot exceed 100%.']);
        }

        // Check if the total probability exceeds 100% after the edit
        if ($totalProbabilityAfterEdit > 100) {
            return redirect()->back()->withInput()->withErrors(['probability' => 'Total probability cannot exceed 100.']);
        }

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

        $data = [];
        $labels = [];
        $colors = [];

        foreach ($prizes as $prize) {
            $expectedWinners = round(($prize->probability / $totalProbability) * $totalWinners);
            $actualProbability = ($expectedWinners / $totalWinners * 100);

            // Save actual probability and winners in database
            $distributionData = new DistributionData();
            $distributionData->prize_id = $prize->id;
            $distributionData->winners = $expectedWinners;
            $distributionData->actual_probability = $actualProbability;
            $distributionData->save();

            // Populate data for graph
            $labels[] = $prize->title . ' (' . number_format($actualProbability, 2) . '%)';
            $data[] = $actualProbability;
        }

        // Generate random colors for graph
        for ($i = 0; $i < count($prizes); $i++) {
            $colors[] = 'rgb(' . rand(0, 255) . ', ' . rand(0, 255) . ', ' . rand(0, 255) . ')';
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
