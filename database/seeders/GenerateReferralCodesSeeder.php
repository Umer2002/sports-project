<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class GenerateReferralCodesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate referral codes for all existing users who don't have one
        $users = User::whereNull('referral_code')->get();
        
        foreach ($users as $user) {
            $user->referral_code = 'PLAY' . str_pad($user->id, 4, '0', STR_PAD_LEFT);
            $user->save();
        }
        
        $this->command->info('Generated referral codes for ' . $users->count() . ' users.');
    }
}