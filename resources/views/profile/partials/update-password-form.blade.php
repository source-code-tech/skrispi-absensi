<section>
    <header class="mb-6">
        <h2 class="text-xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-lock text-purple-600 mr-2"></i>
            Ubah Password
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            Pastikan akun menggunakan password yang panjang dan acak agar tetap aman.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="current_password" class="block font-bold text-gray-700 mb-2 pl-1">Password Saat Ini</label>
            <div class="relative">
                <input id="current_password" name="current_password" type="password" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition duration-200 font-medium text-gray-800" autocomplete="current-password" placeholder="••••••••" />
                <i class="fas fa-key absolute right-4 top-4 text-gray-300"></i>
            </div>
            @if($errors->updatePassword->get('current_password'))
                 <p class="text-red-500 text-sm mt-1 ml-1">{{ $errors->updatePassword->first('current_password') }}</p>
            @endif
        </div>

        <div>
            <label for="password" class="block font-bold text-gray-700 mb-2 pl-1">Password Baru</label>
            <div class="relative">
                <input id="password" name="password" type="password" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition duration-200 font-medium text-gray-800" autocomplete="new-password" placeholder="Min. 8 karakter" />
            </div>
             @if($errors->updatePassword->get('password'))
                 <p class="text-red-500 text-sm mt-1 ml-1">{{ $errors->updatePassword->first('password') }}</p>
            @endif
        </div>

        <div>
            <label for="password_confirmation" class="block font-bold text-gray-700 mb-2 pl-1">Konfirmasi Password</label>
            <div class="relative">
                 <input id="password_confirmation" name="password_confirmation" type="password" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-purple-500 focus:ring-4 focus:ring-purple-100 transition duration-200 font-medium text-gray-800" autocomplete="new-password" placeholder="Ulangi password baru" />
            </div>
            @if($errors->updatePassword->get('password_confirmation'))
                 <p class="text-red-500 text-sm mt-1 ml-1">{{ $errors->updatePassword->first('password_confirmation') }}</p>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="px-6 py-3 bg-purple-600 text-white font-bold rounded-xl shadow-lg hover:bg-purple-700 transition transform hover:-translate-y-1">
                <i class="fas fa-save mr-2"></i> Update Password
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm font-bold text-green-600 flex items-center"
                >
                    <i class="fas fa-check-circle mr-1"></i> {{ __('Berhasil disimpan.') }}
                </p>
            @endif
        </div>
    </form>
</section>
