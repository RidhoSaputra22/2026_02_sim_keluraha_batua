<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user')->latest();

        // Search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('user_name', 'like', "%{$search}%")
                    ->orWhere('auditable_type', 'like', "%{$search}%");
            });
        }

        // Filter by event type
        if ($event = $request->get('event')) {
            $query->where('event', $event);
        }

        // Filter by user
        if ($userId = $request->get('user_id')) {
            $query->where('user_id', $userId);
        }

        // Filter by model type
        if ($modelType = $request->get('model_type')) {
            $query->where('auditable_type', $modelType);
        }

        // Filter by date range
        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $logs = $query->paginate(20)->withQueryString();

        // Data for filters
        $users = User::orderBy('name')->get(['id', 'name']);
        $modelTypes = AuditLog::distinct()
            ->whereNotNull('auditable_type')
            ->pluck('auditable_type')
            ->map(fn ($type) => [
                'value' => $type,
                'label' => AuditLog::getModelLabel(new $type),
            ])
            ->sortBy('label')
            ->values();

        $stats = [
            'total'   => AuditLog::count(),
            'today'   => AuditLog::whereDate('created_at', today())->count(),
            'created' => AuditLog::where('event', 'created')->count(),
            'updated' => AuditLog::where('event', 'updated')->count(),
            'deleted' => AuditLog::where('event', 'deleted')->count(),
        ];

        return view('admin.audit-log.index', compact('logs', 'users', 'modelTypes', 'stats'));
    }

    public function show(AuditLog $auditLog)
    {
        $auditLog->load('user');

        return view('admin.audit-log.show', compact('auditLog'));
    }

    public function destroy(Request $request)
    {
        $days = $request->get('older_than', 90);

        $deleted = AuditLog::where('created_at', '<', now()->subDays($days))->delete();

        return redirect()->route('admin.audit-log')
            ->with('success', "Berhasil menghapus {$deleted} log yang lebih dari {$days} hari.");
    }
}
