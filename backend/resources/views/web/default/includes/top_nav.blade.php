@php
    $userLanguages = !empty($generalSettings['site_language']) ? [$generalSettings['site_language'] => getLanguages($generalSettings['site_language'])] : [];

    if (!empty($generalSettings['user_languages']) and is_array($generalSettings['user_languages'])) {
        $userLanguages = getLanguages($generalSettings['user_languages']);
    }

    $localLanguage = [];

    foreach($userLanguages as $key => $userLanguage) {
        $localLanguage[localeToCountryCode($key)] = $userLanguage;
    }

@endphp

<div class="top-navbar top-navbar-modern d-flex border-bottom">
    <div class="container d-flex justify-content-between align-items-center flex-column flex-lg-row py-0">
        <div class="top-nav-left d-flex flex-column flex-md-row align-items-center justify-content-center flex-grow-1">

            @if(getOthersPersonalizationSettings('platform_phone_and_email_position') == 'header')
                <div class="d-flex align-items-center flex-wrap gap-3">
                    @if(!empty($generalSettings['site_phone']))
                        <a href="tel:{{ preg_replace('/\s+/', '', $generalSettings['site_phone']) }}" class="top-nav-link text-decoration-none">
                            <i data-feather="phone" width="16" height="16"></i>
                            {{ $generalSettings['site_phone'] }}
                        </a>
                    @endif

                    @if(!empty($generalSettings['site_email']))
                        <a href="mailto:{{ $generalSettings['site_email'] }}" class="top-nav-link text-decoration-none">
                            <i data-feather="mail" width="16" height="16"></i>
                            {{ $generalSettings['site_email'] }}
                        </a>
                    @endif
                </div>
                @if(!empty($generalSettings['site_phone']) || !empty($generalSettings['site_email']))
                    <span class="border-sep d-none d-md-block"></span>
                @endif
            @endif

            <div class="d-flex align-items-center flex-wrap">
                @include('web.default.includes.top_nav.currency')

                @if(!empty($localLanguage) and count($localLanguage) > 1)
                    <span class="border-sep"></span>
                    <form action="/locale" method="post" class="d-inline">
                        {{ csrf_field() }}
                        <input type="hidden" name="locale">
                        @if(!empty($previousUrl))
                            <input type="hidden" name="previous_url" value="{{ $previousUrl }}">
                        @endif
                        <div class="language-select">
                            <div id="localItems"
                                 data-selected-country="{{ localeToCountryCode(mb_strtoupper(app()->getLocale())) }}"
                                 data-countries='{{ json_encode($localLanguage) }}'
                            ></div>
                        </div>
                    </form>
                @endif

                <span class="border-sep"></span>
                <form action="/search" method="get" class="navbar-search-modern form-inline my-0">
                    <button type="submit" class="search-icon-btn" aria-label="Search">
                        <i data-feather="search" width="18" height="18"></i>
                    </button>
                    <input class="form-control border-0" type="text" name="search" placeholder="{{ trans('navbar.search_anything') }}" aria-label="Search">
                </form>
            </div>
        </div>

        <div class="top-nav-right xs-w-100 d-flex align-items-center justify-content-between justify-content-lg-end">
            @include(getTemplate().'.includes.shopping-cart-dropdwon')
            <span class="border-sep"></span>
            @include(getTemplate().'.includes.notification-dropdown')
            @include('web.default.includes.top_nav.user_menu')
        </div>
    </div>
</div>

@push('scripts_bottom')
    <link href="/assets/default/vendors/flagstrap/css/flags.css" rel="stylesheet">
    <script src="/assets/default/vendors/flagstrap/js/jquery.flagstrap.min.js"></script>
    <script src="/assets/default/js/parts/top_nav_flags.min.js"></script>
@endpush
