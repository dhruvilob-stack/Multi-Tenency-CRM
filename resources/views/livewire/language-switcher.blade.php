<div class="shrink-0">
    <x-filament::dropdown placement="bottom-end">
        <x-slot name="trigger">
            <button type="button" class="fi-topbar-item-btn">
                <span class="text-base leading-none">
                    {{ $languages[$locale]['flag'] ?? '🌐' }}
                </span>
                <x-filament::icon icon="heroicon-m-language" class="h-4 w-4 opacity-70" />
            </button>
        </x-slot>

        <x-filament::dropdown.list>
            <x-filament::dropdown.list.item :disabled="true">
                <div class="flex items-center justify-between gap-4">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                        {{ __('ui.language') }}
                    </span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        {{ strtoupper($locale) }}
                    </span>
                </div>
            </x-filament::dropdown.list.item>

            <x-filament::dropdown.list.item :disabled="true">
                <div class="grid grid-cols-4 gap-2">
                    @foreach ($languages as $code => $language)
                        <button
                            type="button"
                            wire:click.prevent="setLocale('{{ $code }}')"
                            class="@class([
                                'flex items-center justify-center rounded-lg border px-2 py-2 text-base transition',
                                'border-primary-500/70 bg-primary-50 dark:bg-white/5' => $locale === $code,
                                'border-gray-200 bg-white hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:hover:bg-white/5' => $locale !== $code,
                            ])"
                            title="{{ $language['label'] }}"
                        >
                            {{ $language['flag'] }}
                        </button>
                    @endforeach
                </div>
            </x-filament::dropdown.list.item>
        </x-filament::dropdown.list>
    </x-filament::dropdown>
</div>
