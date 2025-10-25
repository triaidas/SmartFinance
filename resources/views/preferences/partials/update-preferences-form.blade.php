<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Preferences') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Update your currency and budget goal preferences.') }}
        </p>
    </header>

    <form method="post" action="{{ route('preferences.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="currency" :value="__('Currency')" />
            <select
                id="currency"
                name="currency"
                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                required
            >
                <option value="USD" {{ old('currency', $user->currency) == 'USD' ? 'selected' : '' }}>USD ($)</option>
                <option value="EUR" {{ old('currency', $user->currency) == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                <option value="GBP" {{ old('currency', $user->currency) == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                <option value="JPY" {{ old('currency', $user->currency) == 'JPY' ? 'selected' : '' }}>JPY (¥)</option>
                <option value="HUF" {{ old('currency', $user->currency) == 'HUF' ? 'selected' : '' }}>HUF (Ft)</option>
                <option value="CAD" {{ old('currency', $user->currency) == 'CAD' ? 'selected' : '' }}>CAD ($)</option>
                <option value="AUD" {{ old('currency', $user->currency) == 'AUD' ? 'selected' : '' }}>AUD ($)</option>
            </select>
            <x-input-error :messages="$errors->get('currency')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="budget_goal" :value="__('Budget Goal')" />
            <x-text-input
                id="buget_goal"
                name="buget_goal"
                type="number"
                step="0.01"
                min="0"
                class="mt-1 block w-full"
                :value="old('buget_goal', $user->buget_goal)"
                placeholder="Enter your monthly budget goal"
            />
            <x-input-error :messages="$errors->get('buget_goal')" class="mt-2" />
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Set your monthly budget goal to track your spending.') }}
            </p>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'preferences-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
