<section>
    <header class="mb-4">
        <h2 class="text-xl font-black text-[#800000] uppercase tracking-tight">{{ __('PROFILE INFORMATION') }}</h2>
        <p class="text-xs text-gray-400">{{ __("Manage your account's profile information.") }}</p>
        <hr class="mt-2 border-gray-100 w-full">
    </header>

    {{-- Form submits to 'settings.update' to match your Controller --}}
    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-12 gap-x-12 gap-y-6">
        @csrf
        @method('patch')

        {{-- LEFT COLUMN: Avatar & Name --}}
        <div class="md:col-span-4 flex flex-col items-center">
            <div class="flex flex-col items-center w-full">
                
                {{-- Avatar Circle --}}
                <div class="w-52 h-52 md:w-64 md:h-64 rounded-full bg-[#800000] shadow-lg flex items-center justify-center overflow-hidden mb-6 relative group">
                    {{-- ID for JS preview --}}
                    <img id="avatar-preview" src="{{ $user->avatar ? asset('storage/' . $user->avatar) : '' }}" class="w-full h-full object-cover {{ $user->avatar ? '' : 'hidden' }}">
                    <div id="avatar-placeholder" class="w-full h-full flex items-center justify-center bg-[#800000] text-white text-6xl font-black {{ $user->avatar ? 'hidden' : '' }}">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                </div>
                
                {{-- Upload Controls --}}
                <div class="flex flex-col items-center mb-10">
                    <div class="flex gap-2">
                        <label class="cursor-pointer bg-[#FCD116] text-[#800000] px-4 py-2 rounded-lg text-[11px] font-black uppercase shadow-sm hover:bg-yellow-300 transition">
                            Choose File
                            <input type="file" id="avatar-input" name="avatar" class="hidden" accept="image/*">
                        </label>
                    </div>
                    <p id="file-name-display" class="text-[10px] text-gray-500 mt-2 mb-3 italic text-center">
                        Accepted: .png, .jpg. Max size: 1 MB.
                    </p>
                    <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
                </div>

                {{-- Name & Suffix --}}
                <div class="w-full space-y-4 px-2">
                    <div>
                        <x-input-label for="name" :value="__('Full Name')" class="text-[#800000] font-bold text-xs" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full bg-[#F3F4F6] border border-gray-200 focus:ring-[#FCD116]" :value="old('name', $user->name)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>
                    <div>
                        <x-input-label for="suffix" :value="__('Suffix')" class="text-[#800000] font-bold text-xs" />
                        <x-text-input id="suffix" name="suffix" type="text" placeholder="e.g. PhD" class="mt-1 block w-full bg-[#F3F4F6] border border-gray-200 focus:ring-[#FCD116]" :value="old('suffix', $user->suffix)" />
                        <x-input-error class="mt-2" :messages="$errors->get('suffix')" />
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Professional Details --}}
        <div class="md:col-span-8 space-y-6">
            
            {{-- Office Field --}}
            <div>
                <x-input-label for="office_id" :value="__('Office')" class="text-[#800000] font-bold text-xs" />
                <select id="office_id" name="office_id" class="mt-1 block w-full bg-[#F3F4F6] border border-gray-200 rounded-md py-3 px-4 focus:ring-[#FCD116] pr-10">
                    <option value="">Select an office</option>
                    @foreach($offices as $office)
                        <option value="{{ $office->id }}" {{ old('office_id', $user->office_id) == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('office_id')" />
            </div>

            {{-- Designation Field --}}
            <div>
                <x-input-label for="designation" :value="__('Designation')" class="text-[#800000] font-bold text-xs" />
                <x-text-input id="designation" name="designation" type="text" class="mt-1 block w-full bg-[#F3F4F6] border border-gray-200 rounded-md py-3 focus:ring-[#FCD116]" :value="old('designation', $user->designation)" />
                <x-input-error class="mt-2" :messages="$errors->get('designation')" />
            </div>

            {{-- Email Field (With Verification Logic Restored) --}}
            <div>
                <x-input-label for="email" :value="__('Professional/Work Email')" class="text-[#800000] font-bold text-xs" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full bg-[#F3F4F6] border border-gray-200 rounded-md py-3 focus:ring-[#FCD116]" :value="old('email', $user->email)" required />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />

                {{-- Verification Check --}}
                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2">
                        <p class="text-sm text-gray-800">
                            {{ __('Your email address is unverified.') }}
                            <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </p>
                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-2 font-medium text-sm text-green-600">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Phone Field --}}
            <div>
                <x-input-label for="phone" :value="__('Office Telephone Number')" class="text-[#800000] font-bold text-xs" />
                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full bg-[#F3F4F6] border border-gray-200 rounded-md py-3 focus:ring-[#FCD116]" :value="old('phone', $user->phone)" />
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>

            {{-- Save Button Area --}}
            <div class="flex items-center justify-end gap-4 pt-6">
                @if (session('status') === 'profile-updated')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 2000)"
                        class="text-sm text-green-600 font-bold"
                    >{{ __('Saved.') }}</p>
                @endif

                <button type="submit" class="bg-[#800000] text-white px-10 py-2 rounded-md text-sm font-black uppercase w-48 shadow-md hover:bg-red-900 transition">
                    {{ __('SAVE') }}
                </button>
            </div>
        </div>
    </form>
    
    {{-- Hidden form for email verification --}}
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>
</section>

{{-- Avatar Preview Script --}}
<script>
    document.getElementById('avatar-input').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const fileNameDisplay = document.getElementById('file-name-display');
        const preview = document.getElementById('avatar-preview');
        const placeholder = document.getElementById('avatar-placeholder');

        if (file) {
            fileNameDisplay.textContent = file.name;
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            }
            reader.readAsDataURL(file);
        }
    });
</script>