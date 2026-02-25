@php
    $socials = getSocials();
    if (!empty($socials) and count($socials)) {
        $socials = collect($socials)->sortBy('order')->toArray();
    }

    $footerColumns = getFooterColumns();
    $isHomePage = request()->is('/');
@endphp

<footer class="footer footer-modern footer-modern position-relative user-select-none {{ $isHomePage ? 'fk-footer' : 'bg-secondary' }}" @if($isHomePage) style="background-color: #1e2d2a !important; color: rgba(255,255,255,0.9);" @endif>
    {{-- Newsletter strip --}}
    <div class="footer-newsletter">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 col-md-5 mb-3 mb-md-0">
                    <strong class="footer-title d-block">{{ trans('footer.join_us_today') }}</strong>
                    <span class="footer-about-desc d-block">{{ trans('footer.subscribe_content') }}</span>
                </div>
                <div class="col-12 col-md-7">
                    <form action="/newsletters" method="post" class="form-inline-modern">
                        {{ csrf_field() }}
                        <input type="text" name="newsletter_email" class="form-control @error('newsletter_email') is-invalid @enderror" placeholder="{{ trans('footer.enter_email_here') }}" aria-label="{{ trans('footer.enter_email_here') }}"/>
                        @error('newsletter_email')
                            <div class="invalid-feedback d-block w-100">{{ $message }}</div>
                        @enderror
                        <button type="submit" class="btn-subscribe">{{ trans('footer.join') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Main footer columns --}}
    <div class="footer-section">
        <div class="container">
            <div class="row">
                <div class="col-12 col-sm-6 col-lg-3 footer-col">
                    @if(!empty($footerColumns['first_column']))
                        @if(!empty($footerColumns['first_column']['title']))
                            <h3 class="footer-title">{{ $footerColumns['first_column']['title'] }}</h3>
                        @endif
                        @if(!empty($footerColumns['first_column']['value']))
                            <div class="footer-about-desc footer-links">
                                {!! $footerColumns['first_column']['value'] !!}
                            </div>
                        @endif
                    @endif
                    @if(!empty($socials) and count($socials))
                        <nav class="footer-social" aria-label="{{ trans('footer.social_links') ?? 'Social links' }}">
                            @foreach($socials as $social)
                                <a href="{{ $social['link'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social['title'] ?? '' }}">
                                    <img src="{{ $social['image'] }}" alt="{{ $social['title'] ?? '' }}" width="20" height="20">
                                </a>
                            @endforeach
                        </nav>
                    @endif
                </div>

                <div class="col-12 col-sm-6 col-lg-3 footer-col">
                    @if(!empty($footerColumns['second_column']))
                        @if(!empty($footerColumns['second_column']['title']))
                            <h3 class="footer-title">{{ $footerColumns['second_column']['title'] }}</h3>
                        @endif
                        @if(!empty($footerColumns['second_column']['value']))
                            <div class="footer-links">
                                {!! $footerColumns['second_column']['value'] !!}
                            </div>
                        @endif
                    @endif
                </div>

                <div class="col-12 col-sm-6 col-lg-3 footer-col">
                    @if(!empty($footerColumns['third_column']))
                        @if(!empty($footerColumns['third_column']['title']))
                            <h3 class="footer-title">{{ $footerColumns['third_column']['title'] }}</h3>
                        @endif
                        @if(!empty($footerColumns['third_column']['value']))
                            <div class="footer-links">
                                {!! $footerColumns['third_column']['value'] !!}
                            </div>
                        @endif
                    @endif
                </div>

                <div class="col-12 col-sm-6 col-lg-3 footer-col">
                    @if(!empty($footerColumns['forth_column']))
                        @if(!empty($footerColumns['forth_column']['title']))
                            <h3 class="footer-title">{{ $footerColumns['forth_column']['title'] }}</h3>
                        @endif
                        @if(!empty($footerColumns['forth_column']['value']))
                            <div class="footer-links">
                                {!! $footerColumns['forth_column']['value'] !!}
                            </div>
                        @endif
                    @endif
                    @if(getOthersPersonalizationSettings('platform_phone_and_email_position') == 'footer')
                        <div class="footer-links">
                            @if(!empty($generalSettings['site_phone']))
                                <div class="footer-contact-item">
                                    <i data-feather="phone" width="18" height="18" aria-hidden="true"></i>
                                    <a href="tel:{{ preg_replace('/\s+/', '', $generalSettings['site_phone']) }}">{{ $generalSettings['site_phone'] }}</a>
                                </div>
                            @endif
                            @if(!empty($generalSettings['site_email']))
                                <div class="footer-contact-item">
                                    <i data-feather="mail" width="18" height="18" aria-hidden="true"></i>
                                    <a href="mailto:{{ $generalSettings['site_email'] }}">{{ $generalSettings['site_email'] }}</a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <hr class="footer-divider my-0">
    <div class="footer-bottom">
        <div class="container">
            <div class="row align-items-center w-100">
                <div class="col-12 col-md-6 footer-bottom-copyright">
                    &copy; {{ date('Y') }} {{ trans('update.platform_copyright_hint') }}
                </div>
                <div class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end mt-2 mt-md-0">
                    <nav class="footer-bottom-links" aria-label="Legal">
                        <a href="/privacy">{{ trans('public.privacy_policy') ?? 'Privacy Policy' }}</a>
                        <a href="/terms">{{ trans('auth.terms_and_rules') ?? 'Terms' }}</a>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</footer>
