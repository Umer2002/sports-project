<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TournamentTeam extends Model
{
    protected $table = 'tournament_team';
    protected $fillable = ['tournament_id', 'team_id'];

}
