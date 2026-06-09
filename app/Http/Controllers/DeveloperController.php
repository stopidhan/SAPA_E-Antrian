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
            'admin_name'    => 'required|string|max:255',
            'admin_email'   => 'required|email|unique:users,email',
            'admin_password'=> 'required|string|min:8',
        ]);

        DB::transaction(function () use ($request) {
            $instance = Instance::create([
                'instance_name' => $request->instance_name,
                'instance_code' => $request->instance_code,
                'instance_slug' => $request->instance_slug,
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
        return view('Pages.Developer.edit', compact('instance'));
    }

    public function update(Request $request, Instance $instance)
    {
        $request->validate([
            'instance_name' => 'required|string|max:255',
            'instance_slug' => ['required', 'string', 'max:255', Rule::unique('instances')->ignore($instance->id)],
            'is_active'     => 'boolean',
            'brand_color'   => 'nullable|string|max:50',
            'timezone'      => 'required|string|max:100',
        ]);

        $instance->update([
            'instance_name' => $request->instance_name,
            'instance_slug' => $request->instance_slug,
            'is_active'     => $request->boolean('is_active'),
            'brand_color'   => $request->brand_color,
            'timezone'      => $request->timezone,
        ]);

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
