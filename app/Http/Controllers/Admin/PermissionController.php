<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

use App\Http\Controllers\Controller;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::withCount('roles')
            ->orderBy('name')
            ->get()
            ->groupBy(function($permission) {
                $parts = explode('.', $permission->name);
                return $parts[0] ?? 'outros';
            });

        return view('admin.permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        Permission::create([
            'name'       => $validated['name'],
            'guard_name' => 'web',
        ]);

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', "Permissão {$validated['name']} criada com sucesso!");
    }

    public function edit(Permission $permission)
    {
        return view('admin.permissions.edit', compact('permission'));
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update(['name' => $validated['name']]);

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', "Permissão atualizada para {$validated['name']}!");
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()
            ->route('admin.permissions.index')
            ->with('success', 'Permissão excluída com sucesso!');
    }
}
