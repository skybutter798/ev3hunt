<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grid; // Assuming you've set up a model for Grid

class GameController extends Controller
{
    public function index()
    {
        $grids = Grid::all();
        return view('grid', compact('grids'));
    }

    public function checkGrid(Request $request)
    {
        $gridId = $request->input('id');
        $grid = Grid::find($gridId);

        if ($grid->reward_item_id) {
            // You'll want to ensure reward logic is fleshed out.
            return response()->json(['message' => 'Congratulations! You found a reward!']);
        } else {
            return response()->json(['message' => 'Try next time.']);
        }
    }
}
