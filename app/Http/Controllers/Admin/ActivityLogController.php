<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

use App\Http\Controllers\Controller;
use App\Models\User;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::with('causer')
            ->orderBy('created_at', 'desc');

        // Filtro por usuário
        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id);
        }

        // Filtro por evento
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        // Filtro por data
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(30)->withQueryString();

        // Lista de eventos únicos para o filtro
        $events = Activity::distinct()->pluck('event')->sort();

        // Lista de usuários para o filtro
        $users = User::orderBy('name')->get(['id', 'name']);

        return view('admin.activity-logs.index', compact('logs', 'events', 'users'));
    }
}
