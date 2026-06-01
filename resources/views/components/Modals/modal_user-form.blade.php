@props(['show' => false])

<div @keydown.escape.window="closeModal()">
    <x-modal name="user-form" :show="$show" maxWidth="xl">
        <div class="p-6 space-y-4">
            <!-- Header -->
            <div class="border-b pb-4">
                <h3 class="text-lg font-bold text-gray-900" x-text="isEditMode ? 'Edit User' : 'Tambah User Baru'"></h3>
                <p class="text-sm text-gray-500 mt-1"
                    x-text="isEditMode ? 'Perbarui informasi pengguna' : 'Isi semua field yang diperlukan untuk menambah pengguna baru'">
                </p>
            </div>

            <!-- Form -->
            <form @submit.prevent="submitForm()" class="space-y-4" id="userForm">
                @csrf
                <input type="hidden" name="_method" x-model="formMethod" />
                <input type="hidden" x-model="form.id" />

                <!-- Name Field -->
                <x-input-text id="user_name" name="name" type="text" label="Nama"
                    placeholder="Masukkan nama lengkap" x-model="form.name" :error="$errors->first('name')" required />

                <!-- Email Field -->
                <x-input-text id="user_email" name="email" type="email" label="Email" placeholder="nama@email.com"
                    x-model="form.email" :error="$errors->first('email')" required />

                <!-- Password Field - Add Mode -->
                <template x-if="!isEditMode">
                    <x-input-text id="user_password" name="password" type="password" label="Password"
                        placeholder="Minimal 8 karakter" x-model="form.password" :error="$errors->first('password')" required />
                </template>

                <!-- Password Field - Edit Mode -->
                <template x-if="isEditMode">
                    <x-input-text id="user_password" name="password" type="password" label="Password Baru (Opsional)"
                        placeholder="Kosongkan jika tidak ingin mengubah" x-model="form.password" :error="$errors->first('password')" />
                </template>

                <!-- Password Confirmation Field - Add Mode -->
                <template x-if="!isEditMode">
                    <x-input-text id="user_password_confirmation" name="password_confirmation" type="password"
                        label="Konfirmasi Password" placeholder="Ulangi password" x-model="form.password_confirmation"
                        :error="$errors->first('password_confirmation')" required />
                </template>

                <!-- Password Confirmation Field - Edit Mode -->
                <template x-if="isEditMode">
                    <x-input-text id="user_password_confirmation" name="password_confirmation" type="password"
                        label="Konfirmasi Password Baru" placeholder="Ulangi password baru"
                        x-model="form.password_confirmation" :error="$errors->first('password_confirmation')" />
                </template>

                <!-- Role Field -->
                <x-input-dropdown id="user_role" name="role" label="Role" :options="[
                    ['value' => 'admin_instansi', 'label' => 'Admin Instansi'],
                    ['value' => 'kepala_layanan', 'label' => 'Kepala Layanan'],
                    ['value' => 'staff_operator', 'label' => 'Staff Operator'],
                    ['value' => 'staff_konten', 'label' => 'Staff Konten'],
                ]" x-model="form.role"
                    :error="$errors->first('role')" required />

                <!-- Action Buttons -->
                <div class="flex gap-3 justify-between pt-4 border-t">
                    <!-- Close/Cancel Buttons -->
                    <div class="flex gap-3 justify-end ml-auto">
                        <x-button type="button" variant="secondary" @click="closeModal()">
                            Batal
                        </x-button>
                        <x-button type="submit" variant="primary" :xBind="['disabled' => 'isSubmitting']">
                            <span x-show="!isSubmitting" x-text="isEditMode ? 'Perbarui User' : 'Simpan User'"></span>
                            <span x-show="isSubmitting" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span x-text="isEditMode ? 'Memperbarui...' : 'Menyimpan...'"></span>
                            </span>
                        </x-button>
                    </div>
                </div>
            </form>
        </div>
    </x-modal>
</div>
