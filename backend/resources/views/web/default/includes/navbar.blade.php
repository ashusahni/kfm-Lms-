@php
    if (empty($authUser) and auth()->check()) {
        $authUser = auth()->user();
    }

    $navBtnUrl = null;
    $navBtnText = null;

    $navbarButton = getNavbarButton(!empty($authUser) ? $authUser->role_id : null, empty($authUser));
    if (!empty($navbarButton)) {
        $navBtnUrl = $navbarButton->url;
        $navBtnText = $navbarButton->title;
    }
@endphp

<div id="navbarVacuum"></div>
<nav id="navbar" class="navbar navbar-modern navbar-expand-lg">
    <div class="{{ (!empty($isPanel) and $isPanel) ? 'container-fluid' : 'container'}}">
        <div class="navbar-modern-inner">

            <a class="navbar-brand-modern navbar-order {{ (empty($navBtnUrl) and empty($navBtnText)) ? 'ml-auto' : '' }}" href="{{ frontend_url('/') }}">
                @if(!empty($generalSettings['logo']))
                    <img src="{{ $generalSettings['logo'] }}" class="img-cover" alt="site logo">
                @else
                    <span class="font-weight-600 text-dark">{{ !empty($generalSettings['site_name']) ? $generalSettings['site_name'] : 'Logo' }}</span>
                @endif
            </a>

            <button class="navbar-toggler-modern navbar-order d-lg-none" type="button" id="navbarToggle" aria-label="Toggle menu">
                <i data-feather="menu" width="22" height="22"></i>
            </button>

            <div class="nav-main navbar-toggle-content d-lg-flex" id="navbarContent">
                <div class="navbar-toggle-header text-right d-lg-none">
                    <button type="button" class="btn btn-transparent p-0" id="navbarClose" aria-label="Close">
                        <i data-feather="x" width="28" height="28"></i>
                    </button>
                </div>

                <ul class="navbar-nav mr-auto d-flex align-items-center list-unstyled mb-0 pl-0">
                    @if(!empty($categories) and count($categories))
                        <li class="mr-lg-25">
                            <div class="menu-category">
                                <ul class="list-unstyled mb-0">
                                    <li class="cursor-pointer user-select-none d-flex xs-categories-toggle nav-link-modern">
                                        <i data-feather="grid" width="18" height="18"></i>
                                        {{ trans('categories.categories') }}

                                        <ul class="cat-dropdown-menu">
                                            @foreach($categories as $category)
                                                <li>
                                                    <a href="{{ frontend_url($category->getUrl()) }}" class="{{ (!empty($category->subCategories) and count($category->subCategories)) ? 'js-has-subcategory' : '' }}">
                                                        <div class="d-flex align-items-center">
                                                            <img src="{{ $category->icon }}" class="cat-dropdown-menu-icon mr-10" alt="{{ $category->title }} icon">
                                                            {{ $category->title }}
                                                        </div>

                                                        @if(!empty($category->subCategories) and count($category->subCategories))
                                                            <i data-feather="chevron-right" width="16" height="16" class="d-none d-lg-inline-block ml-10"></i>
                                                            <i data-feather="chevron-down" width="16" height="16" class="d-inline-block d-lg-none"></i>
                                                        @endif
                                                    </a>

                                                    @if(!empty($category->subCategories) and count($category->subCategories))
                                                        <ul class="sub-menu" data-simplebar @if((!empty($isRtl) and $isRtl)) data-simplebar-direction="rtl" @endif>
                                                            @foreach($category->subCategories as $subCategory)
                                                                <li>
                                                                    <a href="{{ frontend_url($subCategory->getUrl()) }}">
                                                                        @if(!empty($subCategory->icon))
                                                                            <img src="{{ $subCategory->icon }}" class="cat-dropdown-menu-icon mr-10" alt="{{ $subCategory->title }} icon">
                                                                        @endif

                                                                        {{ $subCategory->title }}
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endif

                    @if(!empty($navbarPages) and count($navbarPages))
                        @foreach($navbarPages as $navbarPage)
                            @php
                                $pageLink = $navbarPage['link'];
                                if (strpos($pageLink, '/') === 0 && !str_starts_with($pageLink, '/panel') && !str_starts_with($pageLink, getAdminPanelUrlPrefix())) {
                                    $pageLink = frontend_url($pageLink);
                                }
                            @endphp
                            <li class="nav-item">
                                <a class="nav-link-modern" href="{{ $pageLink }}">{{ $navbarPage['title'] }}</a>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>

            <div class="nav-actions navbar-order d-flex align-items-center">
                @if(!empty($navBtnUrl))
                    @php
                        $mainSiteUrl = (strpos($navBtnUrl, '/') === 0 && !str_starts_with($navBtnUrl, '/panel') && !str_starts_with($navBtnUrl, getAdminPanelUrlPrefix()))
                            ? frontend_url($navBtnUrl)
                            : $navBtnUrl;
                    @endphp
                    <a href="{{ $mainSiteUrl }}" class="btn-nav-cta d-none d-lg-inline-flex">
                        {{ $navBtnText }}
                    </a>
                    <a href="{{ $mainSiteUrl }}" class="nav-link-modern d-flex d-lg-none">
                        {{ $navBtnText }}
                    </a>
                @endif

                @if(!empty($isPanel) && !empty($authUser) && $authUser->checkAccessToAIContentFeature())
                    <div class="js-show-ai-content-drawer show-ai-content-drawer-btn d-flex align-items-center mr-3">
                        <div class="d-flex align-items-center justify-content-center size-32 rounded-circle bg-white border">
                            <img src="/assets/default/img/ai/ai-chip.svg" alt="ai" width="16" height="16">
                        </div>
                        <span class="ml-2 font-weight-500 text-secondary font-14 d-none d-lg-block">{{ trans('update.ai_content') }}</span>
                    </div>
                @endif

                <div class="d-none nav-notify-cart-dropdown top-navbar">
                    @include('web.default.includes.shopping-cart-dropdwon')
                    <span class="border-sep mx-2"></span>
                    @include('web.default.includes.notification-dropdown')
                </div>
            </div>
        </div>
    </div>
</nav>

@push('scripts_bottom')
    <script src="/assets/default/js/parts/navbar.min.js"></script>
@endpush
