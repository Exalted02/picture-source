<x-guest-layout>
    <!-- Session Status -->
    {{--<x-auth-session-status class="mb-4" :status="session('status')" />--}}
	@if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
	
    <form method="POST" action="{{ route('account-remove') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" 
                          type="email" name="email" :value="old('email')" 
                          required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
		
		<div class="mt-4">
			 <x-input-label for="region" :value="__('Region')" />
            <textarea id="region" class="block mt-1 w-full" name="region" required>{{ old('region') }}</textarea>
            <x-input-error :messages="$errors->get('region')" class="mt-2" />
		</div>


		<div class="flex items-center justify-end mt-4">
           

            <x-primary-button class="ms-3">
                {{ __('Save') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
