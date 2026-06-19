<?php

namespace App\Http\Controllers;

use App\Models\Instance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class DeveloperController extends Controller
{
    public function index()
    {
        $instances = Instance::withCount('users')->latest()->paginate(15);
        return view('Pages.Developer.index', compact('instances'));
    }

    public function create()
    {
        return view('Pages.Developer.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'instance_name' => 'required|string|max:255',
            'instance_code' => 'required|uuid|unique:instances,instance_code',
            'instance_slug' => 'required|string|max:255|unique:instances,instance_slug',
            'whatsapp_number' => 'required|string|max:255',
            'admin_name'    => 'required|string|max:255',
            'admin_email'   => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:8',
        ]);

        DB::transaction(function () use ($request) {
            $instance = Instance::create([
                'instance_name' => $request->instance_name,
                'instance_code' => $request->instance_code,
                'instance_slug' => $request->instance_slug,
                'whatsapp_number' => $request->whatsapp_number,
                'is_active'     => true,
            ]);

            User::create([
                'name'        => $request->admin_name,
                'email'       => $request->admin_email,
                'password'    => Hash::make($request->admin_password),
                'role'        => 'admin_instansi',
                'instance_id' => $instance->id,
                'is_active'   => true,
            ]);
        });

        return redirect()->route('developer.instances.index')->with('success', 'Organization created successfully.');
    }

    public function edit(Instance $instance)
    {
        $admin = $instance->users()->where('role', 'admin_instansi')->first();
        return view('Pages.Developer.edit', compact('instance', 'admin'));
    }

    public function update(Request $request, Instance $instance)
    {
        $adminUser = $instance->users()->where('role', 'admin_instansi')->first();

        $adminEmailRule = ['nullable', 'email'];
        if ($adminUser) {
            $adminEmailRule[] = Rule::unique('users', 'email')->ignore($adminUser->id);
        } else {
            $adminEmailRule[] = Rule::unique('users', 'email');
        }

        $request->validate([
            'instance_name'  => 'required|string|max:255',
            'instance_slug'  => ['required', 'string', 'max:255', Rule::unique('instances')->ignore($instance->id)],
            'whatsapp_number' => 'required|string|max:255',
            'is_active'      => 'boolean',
            'admin_name'     => 'nullable|string|max:255',
            'admin_email'    => $adminEmailRule,
            'admin_password' => 'nullable|string|min:8',
        ]);

        DB::transaction(function () use ($request, $instance, $adminUser) {
            $instance->update([
                'instance_name' => $request->instance_name,
                'instance_slug' => $request->instance_slug,
                'whatsapp_number' => $request->whatsapp_number,
                'is_active'     => $request->boolean('is_active'),
            ]);

            if ($adminUser) {
                $adminData = [];
                if ($request->filled('admin_name')) {
                    $adminData['name'] = $request->admin_name;
                }
                if ($request->filled('admin_email')) {
                    $adminData['email'] = $request->admin_email;
                }
                if ($request->filled('admin_password')) {
                    $adminData['password'] = Hash::make($request->admin_password);
                }
                
                if (!empty($adminData)) {
                    $adminUser->update($adminData);
                }
            } else if ($request->filled('admin_email') && $request->filled('admin_password')) {
                User::create([
                    'name'        => $request->admin_name ?? 'Admin',
                    'email'       => $request->admin_email,
                    'password'    => Hash::make($request->admin_password),
                    'role'        => 'admin_instansi',
                    'instance_id' => $instance->id,
                    'is_active'   => true,
                ]);
            }
        });

        return redirect()->route('developer.instances.index')->with('success', 'Organization updated successfully.');
    }

    public function impersonate(Instance $instance)
    {
        Session::put('impersonate_instance_id', $instance->id);
        Session::put('impersonate_instance_slug', $instance->instance_slug);

        return redirect()->route('admininstance.dashboard', ['instance_slug' => $instance->instance_slug])
            ->with('success', "You are now impersonating {$instance->instance_name}.");
    }

    public function stopImpersonating()
    {
        Session::forget('impersonate_instance_id');
        Session::forget('impersonate_instance_slug');
        return redirect()->route('developer.instances.index')->with('success', 'Stopped impersonating.');
    }
}
