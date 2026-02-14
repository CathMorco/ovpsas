{{-- Success Message for Password --}}
@if (session('status') === 'password-updated')
    <div x-data="{ show: true }"
         x-show="show"
         x-transition
         x-init="setTimeout(() => show = false, 3000)"
         class="text-xs font-bold text-green-600 uppercase tracking-widest mb-2">
        {{ __('Password changed successfully.') }}
    </div>
@endif