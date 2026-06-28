<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RoleProfile;

class RoleProfileController extends Controller
{
    public function index()
    {
        $profiles = RoleProfile::all();
        return response()->json($profiles);
    }

    public function show($id)
    {
        $profile = RoleProfile::findOrFail($id);
        return response()->json($profile);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:role_profiles,name',
            'menus' => 'nullable|array'
        ]);

        $profile = RoleProfile::create($request->all());
        return response()->json($profile, 201);
    }

    public function update(Request $request, $id)
    {
        $profile = RoleProfile::findOrFail($id);
        $request->validate([
            'name' => 'required|string|unique:role_profiles,name,' . $profile->id,
            'menus' => 'nullable|array'
        ]);

        $profile->update($request->all());

        // Update all users who currently have this role profile assigned
        \App\Models\User::where('role_profile_id', $profile->id)->update([
            'menus' => $profile->menus
        ]);

        return response()->json($profile);
    }

    public function destroy($id)
    {
        $profile = RoleProfile::findOrFail($id);
        $profile->delete();
        return response()->json(['message' => 'Profile deleted']);
    }
}
