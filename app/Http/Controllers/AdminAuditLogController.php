<?php

namespace App\Http\Controllers;

use App\Models\AdminAuditLog;
use App\Services\AdminRbacService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminAuditLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware(function ($request, $next) {
            $admin = Auth::guard('admin')->user();
            $rbac = app(AdminRbacService::class);
            if ($rbac->usesRbac($admin)) {
                if (! $rbac->canAccessRoute($admin, 'get:admin:audit_logs')) {
                    abort(403);
                }
            } elseif ((int) $admin->roles !== 1) {
                abort(403);
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        if (!Schema::hasTable('admin_audit_logs')) {
            return view('admin.pages.super_admin.audit_logs.index', [
                'logs' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 50),
                'actions' => collect(),
                'schema_missing' => true,
            ]);
        }

        $query = AdminAuditLog::query()->orderByDesc('id');

        if ($request->filled('admin_id')) {
            $query->where('admin_id', (int) $request->get('admin_id'));
        }
        if ($request->filled('action')) {
            $query->where('action', 'like', '%' . $request->get('action') . '%');
        }
        if ($request->filled('from')) {
            $query->where('created_at', '>=', $request->get('from'));
        }
        if ($request->filled('to')) {
            $query->where('created_at', '<=', $request->get('to') . ' 23:59:59');
        }

        $logs = $query->paginate(50)->withQueryString();
        $actions = AdminAuditLog::query()->select('action')->distinct()->orderBy('action')->pluck('action');

        return view('admin.pages.super_admin.audit_logs.index', compact('logs', 'actions') + ['schema_missing' => false]);
    }

    public function export(Request $request): StreamedResponse
    {
        if (!Schema::hasTable('admin_audit_logs')) {
            abort(503, 'Audit log table is not available. Run database migrations.');
        }

        $query = AdminAuditLog::query()->orderByDesc('id');
        if ($request->filled('admin_id')) {
            $query->where('admin_id', (int) $request->get('admin_id'));
        }
        if ($request->filled('action')) {
            $query->where('action', $request->get('action'));
        }

        $filename = 'admin-audit-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['id', 'admin_id', 'admin_email', 'action', 'subject_type', 'subject_id', 'ip', 'path', 'created_at']);
            $query->chunk(500, function ($rows) use ($handle) {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->id,
                        $row->admin_id,
                        $row->admin_email,
                        $row->action,
                        $row->subject_type,
                        $row->subject_id,
                        $row->ip,
                        $row->request_path,
                        $row->created_at,
                    ]);
                }
            });
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
