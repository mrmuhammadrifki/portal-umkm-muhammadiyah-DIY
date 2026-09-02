<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Nama Usaha -->
        <div class="mt-4">
            <x-input-label for="business_name" :value="__('Nama Usaha')" />
            <x-text-input id="business_name" class="block mt-1 w-full" type="text" name="business_name" :value="old('business_name')" required autocomplete="organization" />
            <x-input-error :messages="$errors->get('business_name')" class="mt-2" />
        </div>

        <!-- Nomor WhatsApp -->
        <div class="mt-4">
            <x-input-label for="whatsapp" :value="__('Nomor WhatsApp')" />
            <x-text-input id="whatsapp" class="block mt-1 w-full" type="text" name="whatsapp" :value="old('whatsapp')" required autocomplete="tel" placeholder="08xxxxxxxxxx" />
            <x-input-error :messages="$errors->get('whatsapp')" class="mt-2" />
        </div>

        <!-- Password Utama -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <!-- ID diubah menjadi password_reg -->
            <x-text-input id="password_reg" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <!-- ID diubah menjadi password_confirm_reg -->
            <x-text-input id="password_confirm_reg" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- SATU CHECKBOX UNTUK MENGINTIP KEDUANYA SEKALIGUS -->
        <div class="block mt-2">
            <label class="inline-flex items-center cursor-pointer">
                <input type="checkbox" onclick="toggleRegisterPassword()" class="rounded border-gray-300 text-brand-600 shadow-sm focus:ring-brand-500">
                <span class="ms-2 text-sm text-gray-600">Lihat Password</span>
            </label>
        </div>

        <script>
        function toggleRegisterPassword() {
            // Mengambil kedua elemen input berdasarkan id masing-masing
            var pass = document.getElementById("password_reg");
            var confirm = document.getElementById("password_confirm_reg");
            
            // Ubah password utama
            if (pass.type === "password") {
                pass.type = "text";
            } else {
                pass.type = "password";
            }
            
            // Ubah confirm password bersamaan
            if (confirm.type === "password") {
                confirm.type = "text";
            } else {
                confirm.type = "password";
            }
        }
        </script>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
