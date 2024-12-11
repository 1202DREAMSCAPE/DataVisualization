<?php

namespace App\Http\Controllers;

use App\Models\SavedChart;
use Illuminate\Http\Request;

class DeleteController extends Controller
{
    /**
     * Delete a chart by its ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteChart($id)
    {
        $chart = SavedChart::find($id);

        if (!$chart) {
            return redirect()->back()->with('error', 'Chart not found.');
        }

        try {
            $chart->delete();
            return redirect()->back()->with('success', 'Chart deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete the chart. Please try again.');
        }
    }
}
