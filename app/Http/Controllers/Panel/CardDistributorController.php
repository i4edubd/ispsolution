<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CardDistributorController extends Controller
{
    /**
     * Display the card distributor dashboard.
     */
    public function dashboard(): View
    {
        $distributorId = auth()->id();
        
        $stats = [
            'total_cards' => 0, // To be implemented
            'sold_cards' => 0, // To be implemented
            'available_balance' => 0, // To be implemented
        ];

        return view('panels.card-distributor.dashboard', compact('stats'));
    }

    /**
     * Display cards listing.
     */
    public function cards(): View
    {
        // To be implemented with card system
        return view('panels.card-distributor.cards.index');
    }

    /**
     * Display sales listing.
     */
    public function sales(): View
    {
        // To be implemented with card system
        return view('panels.card-distributor.sales.index');
    }

    /**
     * Display balance.
     */
    public function balance(): View
    {
        return view('panels.card-distributor.balance');
    }
}
