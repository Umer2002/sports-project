<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use Illuminate\Http\Request;

class RewardsController extends Controller
{
    public function index()
    {
        $rewards = Reward::all();
        return view('admin.reward.showreward', compact('rewards'));
    }

    public function create()
    {
        return view('admin.reward.create_reward');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:badge,certificate,bonus',
            'achievement' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Validate image
        ]);
        // Handle image upload
        $imageName = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            // Get the original file name and replace spaces with underscores (or hyphens)
            $originalName = $file->getClientOriginalName();
            $imageName = time() . '_' . str_replace(' ', '_', $originalName); // Replaces spaces with underscores

            // Alternatively, you can replace spaces with hyphens like this:
            // $imageName = time() . '-' . str_replace(' ', '-', $originalName); // Replaces spaces with hyphens

            // Define the path where you want to move the file
            $destinationPath = public_path('images/rewards');

            // Make sure the target directory exists
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            // Move the file to the desired location
            $file->move($destinationPath, $imageName);
        }


        // Save reward to the database
        Reward::create([
            'name' => $request->name,
            'type' => $request->type,
            'achievement' => $request->achievement,
            'image' => 'rewards/' . $imageName, // Save the path in the database
        ]);

        return redirect()->route('admin.rewards.index')->with('success', 'Reward Created successfully.');
    }

    public function show(Reward $reward)
    {
        return view('admin.reward.showreward', compact('reward'));
    }

    public function edit(Reward $reward)
    {
        return view('admin.reward.editreward', compact('reward'));
    }


    public function update(Request $request, Reward $reward)
    {
        // Validate the incoming data
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string',
            'achievement' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Optional: Validate image
        ]);

        // If a new image is uploaded, delete the old image
        if ($request->hasFile('image')) {
            if ($request->hasFile('image')) {
                // Delete the old image if it exists
                $oldImagePath = public_path('images/' . $reward->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath); // Delete the old image
                }

                // Handle the new image upload
                $file = $request->file('image');
                $imageName = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName()); // Generate a new name for the image

                // Define the path to store the image
                $destinationPath = public_path('images/rewards');

                // Ensure the target directory exists
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true); // Create the directory if it doesn't exist
                }

                // Move the new image to the directory
                $file->move($destinationPath, $imageName);

                // Update the image path in the database (use relative path from public directory)
                $reward->image = 'rewards/' . $imageName;
            }
        }
        // Update other fields
        $reward->name = $request->name;
        $reward->type = $request->type;
        $reward->achievement = $request->achievement;

        // Save the updated reward
        $reward->save();

        // Redirect with a success message
        return redirect()->route('admin.rewards.index')->with('success', 'Reward Created successfully.');

    }

    public function destroy(Reward $reward)
    {
        $reward->delete();
        return redirect()->route('admin.rewards.index')->with('success', 'Reward Deleted successfully.');
    }
}
