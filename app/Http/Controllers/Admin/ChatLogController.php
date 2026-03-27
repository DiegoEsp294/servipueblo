<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatLog;
use Illuminate\Http\Request;

class ChatLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ChatLog::latest();

        if ($request->filled('reason')) {
            $query->where('reason', $request->reason);
        }

        if ($request->has('reviewed')) {
            $query->where('reviewed', $request->reviewed === '1');
        }

        $logs   = $query->paginate(30)->withQueryString();
        $counts = [
            'total'      => ChatLog::count(),
            'pending'    => ChatLog::where('reviewed', false)->count(),
            'no_workers' => ChatLog::where('reason', 'no_workers_found')->count(),
            'ai_fail'    => ChatLog::where('reason', 'ai_could_not_answer')->count(),
            'injection'  => ChatLog::where('reason', 'injection_attempt')->count(),
        ];

        return view('admin.chat-logs.index', compact('logs', 'counts'));
    }

    public function update(Request $request, ChatLog $chatLog)
    {
        $request->validate([
            'reviewed'   => ['required', 'boolean'],
            'admin_note' => ['nullable', 'string', 'max:500'],
        ]);

        $chatLog->update([
            'reviewed'   => $request->reviewed,
            'admin_note' => $request->admin_note,
        ]);

        return back()->with('success', 'Registro actualizado.');
    }

    public function destroy(ChatLog $chatLog)
    {
        $chatLog->delete();
        return back()->with('success', 'Registro eliminado.');
    }
}
