<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCounter;
use App\Models\InstanceSlot;
use App\Models\Activity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class AdminInstanceController extends Controller
{
    /**
     * Display the Admin Instance dashboard.
     */
    public function index()
    {
        $instance = app(\App\Services\TenantManager::class)->getInstance();

        $config = [
            'tts_enabled' => (bool) $instance->tts_enabled,
            'max_online_bookings_per_day' => (int) $instance->max_online_bookings_per_day,
            'max_offline_bookings_per_day' => (int) $instance->max_offline_bookings_per_day,
            'operational_hours' => $instance->operational_hours,
            'tts_language' => $instance->tts_language ?? 'id-ID',
            'timezone' => $instance->timezone ?? 'Asia/Jakarta',
        ];

        $services = $instance->services()->with(['counters'])->latest()->get();
        $slots = $instance->slots()->orderBy('start_time')->get();

        return view('Pages.AdminInstansi.adminInstance', compact('config', 'services', 'slots'));
    }

    // ==========================================
    // Config Methods
    // ==========================================

    public function getConfig(): JsonResponse
    {
        $instance = app(\App\Services\TenantManager::class)->getInstance();

        return response()->json([
            'success' => true,
            'data' => [
                'tts_enabled' => (bool) $instance->tts_enabled,
                'max_offline_bookings_per_day' => (int) $instance->max_offline_bookings_per_day,
                'operational_hours' => $instance->operational_hours,
                'tts_language' => $instance->tts_language ?? 'id-ID',
                'timezone' => $instance->timezone ?? 'Asia/Jakarta',
            ],
        ]);
    }

    public function updateConfig(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tts_enabled' => ['required', 'boolean'],
            'max_offline_bookings_per_day' => ['required', 'integer', 'min:1', 'max:200'],
            'operational_hours' => ['nullable', 'array'],
            'tts_language' => ['nullable', 'string', 'max:20'],
            'timezone' => ['nullable', 'string', 'max:50'],
        ]);

        try {
            $instance = app(\App\Services\TenantManager::class)->getInstance();
            $instance->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Konfigurasi berhasil disimpan',
                'data' => [
                    'tts_enabled' => (bool) $instance->tts_enabled,
                    'max_offline_bookings_per_day' => (int) $instance->max_offline_bookings_per_day,
                    'operational_hours' => $instance->operational_hours,
                    'tts_language' => $instance->tts_language ?? 'id-ID',
                    'timezone' => $instance->timezone ?? 'Asia/Jakarta',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan konfigurasi: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ==========================================
    // Service Methods
    // ==========================================

    public function getServices(): JsonResponse
    {
        $services = app(\App\Services\TenantManager::class)->getInstance()
            ->services()
            ->with(['counters'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $services,
        ]);
    }

    public function storeService(Request $request): JsonResponse|RedirectResponse
    {
        $instanceId = app(\App\Services\TenantManager::class)->getInstanceId();

        $validated = $request->validate([
            'service_name' => ['required', 'string', 'max:255'],
            'queue_prefix' => [
                'required',
                'string',
                'max:5',
                Rule::unique('services', 'queue_prefix')->where('instance_id', $instanceId),
            ],
            'description' => ['nullable', 'string', 'max:500'],
            'slot_duration' => ['required', 'integer', 'min:1', 'max:480'],
            'slot_capacity' => ['required', 'integer', 'min:1', 'max:500'],
            'is_active' => ['boolean'],
            'fast_max' => ['required', 'integer', 'min:1'],
            'normal_max' => ['required', 'integer', 'min:2', 'gt:fast_max'],
            'counters' => ['nullable', 'array'],
            'counters.*.counter_number' => ['required', 'string', 'max:50'],
        ]);

        try {
            $service = Service::create([
                'instance_id' => $instanceId,
                'service_name' => $validated['service_name'],
                'queue_prefix' => $validated['queue_prefix'],
                'description' => $validated['description'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
                'performance_standards' => [
                    'fast' => ['max' => (int) $validated['fast_max']],
                    'normal' => ['min' => (int) $validated['fast_max'] + 1, 'max' => (int) $validated['normal_max']],
                    'slow' => ['min' => (int) $validated['normal_max'] + 1]
                ],
            ]);

            // Create counters untuk service ini
            if (!empty($validated['counters'])) {
                foreach ($validated['counters'] as $counter) {
                    ServiceCounter::create([
                        'instance_id' => $instanceId,
                        'service_id' => $service->id,
                        'counter_number' => $counter['counter_number'],
                        'is_active' => true,
                    ]);
                }
            }

            $service->load(['counters']);

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

    public function updateService(Request $request, string $instanceSlug, Service $service): JsonResponse|RedirectResponse
    {
        // Instance ownership check
        if ($service->instance_id !== app(\App\Services\TenantManager::class)->getInstanceId()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            return back()->withErrors(['error' => 'Unauthorized']);
        }

        $instanceId = app(\App\Services\TenantManager::class)->getInstanceId();

        $validated = $request->validate([
            'service_name' => ['required', 'string', 'max:255'],
            'queue_prefix' => [
                'required',
                'string',
                'max:5',
                Rule::unique('services', 'queue_prefix')
                    ->where('instance_id', $instanceId)
                    ->ignore($service->id),
            ],
            'description' => ['nullable', 'string', 'max:500'],
            'slot_duration' => ['required', 'integer', 'min:1', 'max:480'],
            'slot_capacity' => ['required', 'integer', 'min:1', 'max:500'],
            'is_active' => ['boolean'],
            'fast_max' => ['required', 'integer', 'min:1'],
            'normal_max' => ['required', 'integer', 'min:2', 'gt:fast_max'],
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
                'performance_standards' => [
                    'fast' => ['max' => (int) $validated['fast_max']],
                    'normal' => ['min' => (int) $validated['fast_max'] + 1, 'max' => (int) $validated['normal_max']],
                    'slow' => ['min' => (int) $validated['normal_max'] + 1]
                ],
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
                            'instance_id' => $instanceId,
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

            $service->load(['counters']);

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

    public function destroyService(string $instanceSlug, Service $service): JsonResponse|RedirectResponse
    {
        // Instance ownership check
        if ($service->instance_id !== app(\App\Services\TenantManager::class)->getInstanceId()) {
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            return back()->withErrors(['error' => 'Unauthorized']);
        }

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

    public function toggleService(string $instanceSlug, Service $service): JsonResponse
    {
        // Instance ownership check
        if ($service->instance_id !== app(\App\Services\TenantManager::class)->getInstanceId()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

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

    public function deleteCounter(string $instanceSlug, ServiceCounter $counter): JsonResponse
    {
        // Instance ownership check
        if ($counter->instance_id !== app(\App\Services\TenantManager::class)->getInstanceId()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

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


    public function storeSlot(Request $request): JsonResponse
    {
        $instanceId = app(\App\Services\TenantManager::class)->getInstanceId();

        $validated = $request->validate([
            'id' => ['nullable', 'integer', 'exists:instance_slots,id'],
            'start_time' => ['required', 'string', 'regex:/^\d{2}:\d{2}$/'],
            'end_time' => ['required', 'string', 'regex:/^\d{2}:\d{2}$/'],
            'capacity' => ['required', 'integer', 'min:1'],
        ]);

        if ($validated['start_time'] >= $validated['end_time']) {
            return response()->json([
                'success' => false,
                'message' => 'Waktu mulai harus lebih kecil dari waktu selesai.',
            ], 422);
        }

        // Check for overlapping slots
        $query = InstanceSlot::where('instance_id', $instanceId)
            ->where(function ($q) use ($validated) {
                $q->where('start_time', '<', $validated['end_time'])
                  ->where('end_time', '>', $validated['start_time']);
            });

        if (!empty($validated['id'])) {
            $query->where('id', '!=', $validated['id']);
        }

        if ($query->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Slot waktu bertabrakan dengan slot yang sudah ada.',
            ], 422);
        }

        try {
            if (!empty($validated['id'])) {
                $slot = InstanceSlot::where('id', $validated['id'])
                    ->where('instance_id', $instanceId)
                    ->firstOrFail();
                $slot->update([
                    'start_time' => $validated['start_time'],
                    'end_time' => $validated['end_time'],
                    'capacity' => $validated['capacity'],
                ]);
                $message = 'Slot berhasil diperbarui';
            } else {
                $slot = InstanceSlot::create([
                    'instance_id' => $instanceId,
                    'start_time' => $validated['start_time'],
                    'end_time' => $validated['end_time'],
                    'capacity' => $validated['capacity'],
                ]);
                $message = 'Slot berhasil dibuat';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $slot,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan slot: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroySlot(string $instanceSlug, InstanceSlot $slot): JsonResponse|RedirectResponse
    {
        if ($slot->instance_id !== app(\App\Services\TenantManager::class)->getInstanceId()) {
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
            return back()->withErrors(['error' => 'Unauthorized']);
        }

        try {
            $slot->delete();
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Slot berhasil dihapus',
                ]);
            }
            return back()->with('success', 'Slot berhasil dihapus');
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus slot',
                ], 500);
            }
            return back()->withErrors(['error' => 'Gagal menghapus slot']);
        }
    }
}
