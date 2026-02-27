<style>
    /* Mobile-first: keep Filament defaults and ensure topbar can wrap */
    .fi-topbar {
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .fi-topbar .fi-global-search-ctn {
        order: 50;
        width: 100%;
        flex-basis: 100%;
    }

    .fi-topbar .fi-global-search,
    .fi-topbar .fi-global-search-field,
    .fi-topbar .fi-global-search-field > * {
        width: 100%;
        max-width: 100%;
    }

    .fi-topbar .fi-global-search input[type='search'] {
        width: 100%;
        max-width: 100%;
    }

    /* Desktop: center search in the middle, without pushing topbar icons */
    @media (min-width: 1024px) {
        .fi-topbar {
            position: relative;
            flex-wrap: nowrap;
            gap: 0;
        }

        .fi-topbar .fi-global-search-ctn {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: min(44rem, calc(100vw - 8rem));
            display: flex;
            justify-content: center;
            pointer-events: none;
            order: initial;
            flex-basis: auto;
        }

        .fi-topbar .fi-global-search {
            width: 100%;
            pointer-events: auto;
        }

        /* Center the dropdown results under the search field */
        .fi-topbar .fi-global-search-results-ctn {
            inset-inline: auto !important;
            left: 50% !important;
            right: auto !important;
            width: min(44rem, calc(100vw - 8rem)) !important;
            max-width: none !important;
            transform: translateX(-50%) translateZ(0) !important;
        }
    }
</style>
