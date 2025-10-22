<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function clubMerchandise()
    {
        $user = Auth::user();
        $player = $user->player?->loadMissing('club', 'sport', 'position');
        $club = $player?->club;

        $products = Product::with(['category', 'club'])
            ->whereNotNull('club_id')
            ->when($club, fn ($query) => $query->where('club_id', $club->id))
            ->where('is_active', true)
            ->latest()
            ->paginate(12);

        return view('players.products.index', [
            'player' => $player,
            'title' => 'Club Merchandise',
            'subtitle' => $club
                ? 'Official gear from ' . $club->name
                : 'Browse merchandise from clubs across the platform.',
            'products' => $products,
            'emptyMessage' => $club
                ? 'Your club has not listed any merchandise yet.'
                : 'No club merchandise is available right now.',
        ]);
    }

    public function storeFront()
    {
        $user = Auth::user();
        $player = $user->player?->loadMissing('club', 'sport', 'position');

        $products = Product::with('category')
            ->whereNull('club_id')
            ->where('is_active', true)
            ->latest()
            ->paginate(12);

        return view('players.products.index', [
            'player' => $player,
            'title' => 'Play2Earn Store',
            'subtitle' => 'Curated products imported from our WooCommerce catalog.',
            'products' => $products,
            'emptyMessage' => 'Store products are currently unavailable. Check back soon!',
        ]);
    }
}
