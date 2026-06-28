<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function getPrivileges($roleName)
    {
        $roleSlug = str_replace('-', '_', Str::slug($roleName));
        $user = User::where('role', $roleSlug)->first();
        
        return response()->json([
            'menus' => $user ? $user->menus : []
        ]);
    }

    public function updatePrivileges(Request $request, $roleName)
    {
        $request->validate([
            'menus' => 'array'
        ]);

        $roleSlug = str_replace('-', '_', Str::slug($roleName));
        
        // Sync menus for all users with this role
        User::where('role', $roleSlug)->update([
            'menus' => $request->menus
        ]);
        
        return response()->json(['message' => 'Role privileges synced successfully']);
    }

    public function removeFromUsers($roleName)
    {
        $roleSlug = str_replace('-', '_', Str::slug($roleName));
        User::where('role', $roleSlug)->update([
            'role' => null
        ]);

        return response()->json(['message' => 'Role removed from all users']);
    }
}
