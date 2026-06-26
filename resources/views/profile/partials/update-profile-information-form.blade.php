<section>
    <header class="mb-6">
        <h2 class="text-xl font-bold text-gray-800 flex items-center">
            <i class="fas fa-id-card text-indigo-600 mr-2"></i>
            Informasi Profil
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            Perbarui informasi nama akun dan alamat email Anda.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block font-bold text-gray-700 mb-2 pl-1">Nama Lengkap</label>
            <input id="name" name="name" type="text" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition duration-200 font-medium text-gray-800" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            
            @if($errors->get('name'))
                <p class="text-red-500 text-sm mt-1 ml-1">{{ $errors->first('name') }}</p>
            @endif
        </div>

        <div>
             <label for="email" class="block font-bold text-gray-700 mb-2 pl-1">Alamat Email</label>
            <input id="email" name="email" type="email" class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition duration-200 font-medium text-gray-800" value="{{ old('email', $user->email) }}" required autocomplete="username" />
            
            @if($errors->get('email'))
                <p class="text-red-500 text-sm mt-1 ml-1">{{ $errors->first('email') }}</p>
            @endif

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-4 p-4 bg-yellow-50 rounded-xl border border-yellow-100">
                    <p class="text-sm text-yellow-800">
                        {{ __('Alamat email Anda belum terverifikasi.') }}

                        <button form="send-verification" class="underline font-bold hover:text-yellow-600 ml-1">
                            {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-bold text-sm text-green-600">
                            {{ __('Tautan verifikasi baru telah dikirim ke email Anda.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white font-bold rounded-xl shadow-lg hover:bg-indigo-700 transition transform hover:-translate-y-1">
                <i class="fas fa-save mr-2"></i> Simpan Perubahan
            </button>

            @if (session('status') === 'profile-updated')
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
