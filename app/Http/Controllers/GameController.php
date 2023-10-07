<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grid; // Assuming you've set up a model for Grid
use Illuminate\Support\Facades\Auth;


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
        
        if (!$grid->user_id) { // Check if the grid is not already clicked by another user
            $grid->user_id = Auth::id(); // Set the user_id to the currently authenticated user's ID
            $grid->save();
        }
        
        if (!$grid) {
            return response()->json(['message' => 'Invalid grid.']);
        }
    
        if ($grid->clicked) {
            return response()->json(['message' => 'Grid already clicked.']);
        }
    
        $grid->clicked = true;
        $grid->user_id = Auth::id();
        $grid->save();
        
        if ($grid->reward_item_id) {
            // You can customize this to return the actual reward title or any other details.
            return response()->json(['message' => 'Congratulations! You found a reward!']);
        }
    
        return response()->json(['message' => 'Try next time']);
    }

}
