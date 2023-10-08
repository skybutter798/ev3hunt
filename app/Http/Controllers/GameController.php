<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grid;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Support\Facades\Log;

class GameController extends Controller
{
    public function index()
    {
        $remainingClicks = 0; // Default value
    
        if (Auth::check()) {
            $user = Auth::user();
    
            // Check how many grids the user has clicked today
            $todayClicks = DB::table('grids')
                         ->where('user_id', $user->id)
                         ->whereDate('updated_at', Carbon::now('Asia/Kuala_Lumpur')->toDateString())
                         ->count();
                         
            if ($todayClicks >2){
                $remainingClicks = 0;
            } else {
                $remainingClicks = 2 - $todayClicks;
            }
        }
    
        $grids = Grid::all();
        return view('grid', compact('grids', 'remainingClicks'));
    }


    public function checkGrid(Request $request)
    {
        $user = Auth::user();
    
        $todayClicks = DB::table('grids')
                     ->where('user_id', $user->id)
                     ->whereDate('updated_at', Carbon::now('Asia/Kuala_Lumpur')->toDateString())
                     ->count();
    
        if ($todayClicks >= 2 && $user->share == 0) {
            return response()->json(['message' => 'limit']);
        } elseif ($todayClicks >= 3 || ($todayClicks >= 2 && $user->share == 2)) {
            return response()->json(['message' => 'shared']);
        }

    
        $gridId = $request->input('id');
        $grid = Grid::find($gridId);
    
        if (!$grid) {
            return response()->json(['message' => 'Invalid grid.']);
        }
    
        if ($grid->clicked) {
            return response()->json(['message' => 'repeat']);
        }
    
        $grid->clicked = true;
        $grid->user_id = Auth::id();
        $grid->save();
        if ($user->share == 1) {
            $user->share = 2;
            $user->save();
        }
        
        // Log the click
        $userId = Auth::id();
        $userName = $user->name;
        Log::channel('grid_clicks')->info("User {$userId}, {$userName} clicked on grid {$gridId}");
    
        if ($grid->reward_item_id) {
            return response()->json(['message' => 'reward']);
        }

        return response()->json(['message' => 'none']);
    }
    
    public function updateStatus(Request $request) {
        $user = Auth::user();
        if ($user->share == 0) {
            $user->share = 1;
        } elseif ($user->share == 1) {
            $user->share = 2;
        }
        $user->save();
        return response()->json(['message' => 'Share status updated']);
    }


    
    public function saveWalletAddress(Request $request)
    {
        $request->validate([
            'wallet_address' => 'required|string|max:255', // You can adjust the validation rules as needed
        ]);
    
        $user = Auth::user();
        $user->wallet_address = $request->wallet_address;
        $user->save();
    
        return response()->json(['success' => true, 'message' => 'Wallet address saved successfully!']);
    }

}
