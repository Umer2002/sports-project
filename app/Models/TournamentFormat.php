<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class TournamentFormat
{
    public $id;
    public $name;
    public $description;
    public $type;
    public $games_per_team;
    public $group_count;
    public $elimination_type;

    public function __construct($data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->type = $data['type'] ?? null;
        $this->games_per_team = $data['games_per_team'] ?? null;
        $this->group_count = $data['group_count'] ?? null;
        $this->elimination_type = $data['elimination_type'] ?? null;
    }

    public static function all(): Collection
    {
        return collect([
            new self([
                'id' => 1,
                'name' => 'Round Robin',
                'description' => 'Every team plays every other team once',
                'type' => 'round_robin',
                'games_per_team' => null,
                'group_count' => 1,
                'elimination_type' => null,
            ]),
            new self([
                'id' => 2,
                'name' => 'Knockout',
                'description' => 'Single elimination tournament',
                'type' => 'knockout',
                'games_per_team' => null,
                'group_count' => null,
                'elimination_type' => 'single',
            ]),
            new self([
                'id' => 3,
                'name' => 'Group Stage',
                'description' => 'Teams divided into groups for round robin play',
                'type' => 'group',
                'games_per_team' => null,
                'group_count' => 2,
                'elimination_type' => null,
            ]),
        ]);
    }

    public static function find($id)
    {
        return self::all()->firstWhere('id', $id);
    }

    public static function pluck($column, $key = null): Collection
    {
        $collection = self::all();
        
        if ($key) {
            return $collection->pluck($column, $key);
        }
        
        return $collection->pluck($column);
    }

    // For compatibility with Tournament model relationship
    public function tournaments()
    {
        return Tournament::where('tournament_format_id', $this->id);
    }

    public function getAttribute($key)
    {
        return $this->$key ?? null;
    }
}
