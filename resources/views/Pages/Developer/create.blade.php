<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Onboard New Organization') }}
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

                <form action="{{ route('developer.instances.store') }}" method="POST">
                    @csrf
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Organization Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Instance Name</label>
                            <input type="text" name="instance_name" value="{{ old('instance_name') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Instance Slug (URL Prefix)</label>
                            <input type="text" name="instance_slug" value="{{ old('instance_slug') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="text-xs text-gray-500 mt-1">e.g. rsud-jakarta. Only letters, numbers, and dashes.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Instance Code (UUID)</label>
                            <input type="text" name="instance_code" value="{{ old('instance_code', Str::uuid()->toString()) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" readonly>
                        </div>
                    </div>

                    <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Primary Admin Account</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Admin Name</label>
                            <input type="text" name="admin_name" value="{{ old('admin_name') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Admin Email</label>
                            <input type="email" name="admin_email" value="{{ old('admin_email') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Admin Password</label>
                            <input type="password" name="admin_password" required minlength="8" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <a href="{{ route('developer.instances.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded mr-2">Cancel</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Create Organization
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>