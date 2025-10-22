<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Team extends Model
{
    protected $fillable = [
        'club_id',
        'name',
        'sport_id',
        'age_group_id',
        'gender_id',
        'description',
        'logo',
        'division_id',
    ];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function players()
    {
        return $this->belongsToMany(Player::class, 'player_team')
                    ->withPivot(['position_id', 'created_at', 'updated_at'])
                    ->withTimestamps();
    }

    public function tournaments()
    {
        return $this->belongsToMany(Tournament::class, 'tournament_team')->withTimestamps();
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function ageGroup()
    {
        return $this->belongsTo(AgeGroup::class);
    }

    public function genderCategory()
    {
        return $this->belongsTo(Gender::class, 'gender_id');
    }

    public function classificationOptions()
    {
        return $this->belongsToMany(SportClassificationOption::class, 'team_classifications', 'team_id', 'option_id')
            ->withTimestamps();
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function coaches()
    {
        return $this->belongsToMany(Coach::class, 'team_coach');
    }

    public function invitations()
    {
        return $this->hasMany(Invite::class, 'reference_id')->where('type', 'team_invite');
    }

    /**
     * Get all players for this team with their positions
     */
    public function getTeamPlayers()
    {
        return $this->players()->with('position')->get();
    }

    /**
     * Get team players with additional pivot data
     */
    public function getTeamPlayersWithPivot()
    {
        return DB::table('player_team')
            ->join('players', 'player_team.player_id', '=', 'players.id')
            ->leftJoin('positions', 'player_team.position_id', '=', 'positions.id')
            ->where('player_team.team_id', $this->id)
            ->select(
                'players.*',
                'positions.position_name',
                'player_team.position_id',
                'player_team.created_at as joined_at',
                'player_team.updated_at'
            )
            ->get();
    }

    /**
     * Get players with their positions for this team
     */
    public function getPlayersWithPositions()
    {
        return DB::table('player_team')
            ->join('players', 'player_team.player_id', '=', 'players.id')
            ->leftJoin('positions', 'player_team.position_id', '=', 'positions.id')
            ->where('player_team.team_id', $this->id)
            ->select(
                'players.id as player_id',
                'players.name as player_name',
                'players.email as player_email',
                'players.photo',
                'positions.id as position_id',
                'positions.position_name',
                'player_team.created_at as joined_at'
            )
            ->get();
    }

    /**
     * Get players grouped by position
     */
    public function getPlayersByPosition()
    {
        return DB::table('player_team')
            ->join('players', 'player_team.player_id', '=', 'players.id')
            ->leftJoin('positions', 'player_team.position_id', '=', 'positions.id')
            ->where('player_team.team_id', $this->id)
            ->select(
                'positions.position_name',
                'positions.id as position_id',
                DB::raw('COUNT(players.id) as player_count'),
                DB::raw('GROUP_CONCAT(players.name) as player_names')
            )
            ->groupBy('positions.id', 'positions.position_name')
            ->get();
    }

    /**
     * Get players without positions
     */
    public function getPlayersWithoutPosition()
    {
        return DB::table('player_team')
            ->join('players', 'player_team.player_id', '=', 'players.id')
            ->where('player_team.team_id', $this->id)
            ->whereNull('player_team.position_id')
            ->select(
                'players.id as player_id',
                'players.name as player_name',
                'players.email as player_email',
                'player_team.created_at as joined_at'
            )
            ->get();
    }

}
