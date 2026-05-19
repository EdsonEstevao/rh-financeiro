<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Spatie\Permission\Models\{Permission, Role};

use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')
            ->withCount('users')
            ->orderBy('name')
            ->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        // Agrupa permissões por módulo
        $permissions = Permission::orderBy('name')->get();
        $groupedPermissions = $permissions->groupBy(function($permission) {
            $parts = explode('.', $permission->name);
            return $parts[0] ?? 'outros';
        });

        return view('admin.roles.create', compact('groupedPermissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name'       => $validated['name'],
            'guard_name' => 'web',
        ]);

        if (!empty($validated['permissions'])) {
            $permissionNames = Permission::whereIn('id', $validated['permissions'])->pluck('name');
            $role->syncPermissions($permissionNames);
        }

        return redirect()
            ->route('admin.roles.index')
            ->with('success', "Perfil {$role->name} criado com sucesso!");
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();
        $groupedPermissions = $permissions->groupBy(function($permission) {
            $parts = explode('.', $permission->name);
            return $parts[0] ?? 'outros';
        });
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'groupedPermissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Não permite alterar o nome do perfil admin
        if ($role->name === 'admin' && $validated['name'] !== 'admin') {
            return redirect()
                ->back()
                ->with('error', 'Não é possível renomear o perfil admin.');
        }

        $role->update(['name' => $validated['name']]);

        if (isset($validated['permissions'])) {
            $permissionNames = Permission::whereIn('id', $validated['permissions'])->pluck('name');
            $role->syncPermissions($permissionNames);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()
            ->route('admin.roles.index')
            ->with('success', "Perfil {$role->name} atualizado com sucesso!");
    }

    public function destroy(Role $role)
    {
        // Não permite excluir perfil admin
        if ($role->name === 'admin') {
            return redirect()
                ->back()
                ->with('error', 'Não é possível excluir o perfil admin.');
        }

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('success', 'Perfil excluído com sucesso!');
    }
}
