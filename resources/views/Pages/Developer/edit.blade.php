<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Organization: ') . $instance->instance_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('developer.instances.update', $instance->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Organization Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Instance Name</label>
                            <input type="text" name="instance_name" value="{{ old('instance_name', $instance->instance_name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Instance Slug (URL Prefix)</label>
                            <input type="text" name="instance_slug" value="{{ old('instance_slug', $instance->instance_slug) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="text-xs text-gray-500 mt-1">e.g. rsud-jakarta. Only letters, numbers, and dashes.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Instance Code (UUID)</label>
                            <input type="text" value="{{ $instance->instance_code }}" class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-gray-300 focus:ring-0 text-gray-500" readonly>
                        </div>
                    </div>

                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Admin Account Recovery / Creation</h3>
                    <p class="text-sm text-gray-600 mb-4">Fill these fields only if you need to create a new admin account or reset an existing admin's password.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Admin Name</label>
                            <input type="text" name="admin_name" value="{{ old('admin_name', $admin->name ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Admin Email</label>
                            <input type="email" name="admin_email" value="{{ old('admin_email', $admin->email ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="text-xs text-gray-500 mt-1">If you update the email or fill in a new password, the admin account will be updated.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">New Password</label>
                            <input type="password" name="admin_password" minlength="8" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Global Access</h3>
                    <div class="mb-8">
                        <label class="inline-flex items-center">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $instance->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700 font-bold">Active (Uncheck to suspend organization)</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1 ml-6">If suspended, no users (including admins and customers) will be able to access the system for this organization.</p>
                    </div>

                    <div class="flex justify-end">
                        <a href="{{ route('developer.instances.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded mr-2">Cancel</a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            Update Organization
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>