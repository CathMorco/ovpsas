<section>
    <header class="mb-4">
        <h2 class="text-xl font-black text-[#800000] uppercase tracking-tight">{{ __('Update Password') }}</h2>
        <p class="text-xs text-gray-400">{{ __('Ensure your account is using a long, random password to stay secure.') }} </p>
        <hr class="mt-2 border-gray-100 w-full">
    </header>

    <form method="post" action="{{ route('password.update') }}#update-password" class="mt-6 space-y-6 max-w-sm">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="bg-[#800000] text-white px-10 py-2 rounded-md text-sm font-black uppercase w-48 shadow-md hover:bg-red-900 transition">
                {{ __('SAVE') }}
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600 font-bold"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>