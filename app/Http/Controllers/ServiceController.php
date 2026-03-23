<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCounter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ServiceController extends Controller
{
    /**
     * Display a listing of services for JSON API (dengan counters).
     */
    public function index(): JsonResponse
    {
        $services = auth()->user()->instance
            ->services()
            ->with('counters')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $services,
        ]);
    }

    /**
     * Store a newly created service dengan counters.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'service_name' => ['required', 'string', 'max:255'],
            'queue_prefix' => ['required', 'string', 'max:5', 'unique:services,queue_prefix'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'counters' => ['nullable', 'array'],
            'counters.*.counter_number' => ['required', 'string', 'max:50'],
        ]);

        try {
            $service = Service::create([
                'instance_id' => auth()->user()->instance_id,
                'service_name' => $validated['service_name'],
                'queue_prefix' => $validated['queue_prefix'],
                'description' => $validated['description'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            // Create counters untuk service ini
            if (!empty($validated['counters'])) {
                foreach ($validated['counters'] as $counter) {
                    ServiceCounter::create([
                        'instance_id' => auth()->user()->instance_id,
                        'service_id' => $service->id,
                        'counter_number' => $counter['counter_number'],
                        'is_active' => true,
                    ]);
                }
            }

            $service->load('counters');

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Layanan berhasil dibuat',
                    'data' => $service,
                ], 201);
            }

            return redirect()
                ->route('services.index')
                ->with('success', 'Layanan berhasil dibuat');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat layanan: ' . $e->getMessage(),
                ], 500);
            }

            return back()->withErrors(['error' => 'Gagal membuat layanan']);
        }
    }

    /**
     * Update service dan counters.
     */
    public function update(Request $request, Service $service): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $service);

        $validated = $request->validate([
            'service_name' => ['required', 'string', 'max:255'],
            'queue_prefix' => ['required', 'string', 'max:5', "unique:services,queue_prefix,{$service->id}"],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'counters' => ['nullable', 'array'],
            'counters.*.id' => ['nullable', 'exists:service_counters,id'],
            'counters.*.counter_number' => ['required', 'string', 'max:50'],
        ]);

        try {
            $service->update([
                'service_name' => $validated['service_name'],
                'queue_prefix' => $validated['queue_prefix'],
                'description' => $validated['description'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            // Sync counters
            if (!empty($validated['counters'])) {
                $counterIds = [];
                foreach ($validated['counters'] as $counter) {
                    if (!empty($counter['id'])) {
                        // Update existing counter
                        ServiceCounter::find($counter['id'])->update([
                            'counter_number' => $counter['counter_number'],
                        ]);
                        $counterIds[] = $counter['id'];
                    } else {
                        // Create new counter
                        $newCounter = ServiceCounter::create([
                            'instance_id' => auth()->user()->instance_id,
                            'service_id' => $service->id,
                            'counter_number' => $counter['counter_number'],
                            'is_active' => true,
                        ]);
                        $counterIds[] = $newCounter->id;
                    }
                }

                // Delete counters yang tidak ada di form
                $service->counters()->whereNotIn('id', $counterIds)->delete();
            } else {
                // Hapus semua counter jika tidak ada di form
                $service->counters()->delete();
            }

            $service->load('counters');

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Layanan berhasil diperbarui',
                    'data' => $service,
                ]);
            }

            return redirect()
                ->route('services.index')
                ->with('success', 'Layanan berhasil diperbarui');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui layanan: ' . $e->getMessage(),
                ], 500);
            }

            return back()->withErrors(['error' => 'Gagal memperbarui layanan']);
        }
    }

    /**
     * Remove service dan counters.
     */
    public function destroy(Service $service): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $service);

        try {
            $service->delete();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Layanan berhasil dihapus',
                ]);
            }

            return back()->with('success', 'Layanan berhasil dihapus');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus layanan',
                ], 500);
            }

            return back()->withErrors(['error' => 'Gagal menghapus layanan']);
        }
    }

    /**
     * Toggle active status.
     */
    public function toggle(Service $service): JsonResponse
    {
        $this->authorize('update', $service);

        try {
            $service->update([
                'is_active' => !$service->is_active,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status layanan berhasil diubah',
                'data' => $service,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status layanan',
            ], 500);
        }
    }

    /**
     * Delete a specific counter.
     */
    public function deleteCounter(ServiceCounter $counter): JsonResponse
    {
        try {
            $counter->delete();

            return response()->json([
                'success' => true,
                'message' => 'Counter berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus counter',
            ], 500);
        }
    }
}
