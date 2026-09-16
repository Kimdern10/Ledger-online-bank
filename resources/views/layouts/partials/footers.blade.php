<footer class="site-footer" id="contact">
  <div class="container">

    <div class="footer-top">
      <div class="footer-brand">
        <a href="{!! route('home') !!}" class="brand">
          <span class="mark">L</span>
          <span class="word" style="color:var(--paper);">Ledger</span>
        </a>
        <p>{{ __('welcome.footer_tagline') }}</p>
        <div class="footer-social" style="margin-top:22px;">
          <a href="#" aria-label="X (Twitter)">
            <svg viewBox="0 0 24 24"><path d="M5 5l14 14M19 5L5 19" stroke-linecap="round"/></svg>
          </a>
          <a href="#" aria-label="Instagram">
            <svg viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="5"/><circle cx="12" cy="12" r="3.5"/><circle cx="16.6" cy="7.4" r="0.6" fill="currentColor" stroke="none"/></svg>
          </a>
          <a href="#" aria-label="LinkedIn">
            <svg viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="3"/><path d="M8.5 10.5v6M8.5 8v.01M12 16.5v-3.5c0-1.2.9-2 2-2s2 .8 2 2v3.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </a>
          <a href="#" aria-label="Facebook">
            <svg viewBox="0 0 24 24"><path d="M14 8.5h2V5.5h-2.2c-2 0-3.3 1.3-3.3 3.4V11H8.5v3H10.5v5.5h3V14h2.1l.4-3h-2.5V9.2c0-.5.2-.7.9-.7Z" stroke-linejoin="round"/></svg>
          </a>
        </div>
      </div>

      <div class="footer-cols">
        <div class="footer-col">
          <h4>{{ __('welcome.footer_col_product') }}</h4>
          {{-- "Send money" used to point at the same #services anchor as
               every other item in this column — not a real route anywhere.
               Signed-in visitors now go straight to the real Send Money
               page; guests go to registration (same pattern as the hero
               "Open an account" CTA and welcome.blade.php's own header
               link) instead of a dashboard route they can't reach yet. --}}
          <a href="{!! auth()->check() ? route('send') : route('register') !!}">{{ __('welcome.footer_product_send_money') }}</a>
          <a href="{!! route('home') !!}#services">{{ __('welcome.footer_product_receive_money') }}</a>
          <a href="{!! route('home') !!}#services">{{ __('welcome.footer_product_pay_bills') }}</a>
          <a href="{!! route('home') !!}#services">{{ __('welcome.footer_product_budgeting') }}</a>
          <a href="{!! route('home') !!}#services">{{ __('welcome.footer_product_cards') }}</a>
        </div>
        <div class="footer-col">
          <h4>{{ __('welcome.footer_col_company') }}</h4>
          <a href="{!! route('home') !!}#about">{{ __('welcome.footer_company_about') }}</a>
          <a href="#">{{ __('welcome.footer_company_careers') }}</a>
          <a href="{!! route('home') !!}#blog">{{ __('welcome.footer_company_blog') }}</a>
          <a href="#">{{ __('welcome.footer_company_press') }}</a>
        </div>
        <div class="footer-col">
          <h4>{{ __('welcome.footer_col_support') }}</h4>
          <a href="#">{{ __('welcome.footer_support_help_center') }}</a>
          <a href="#">{{ __('welcome.footer_support_security') }}</a>
          <a href="#">{{ __('welcome.footer_support_status') }}</a>
          <a href="{!! route('home') !!}#contact">{{ __('welcome.footer_support_contact_us') }}</a>
        </div>
        <div class="footer-col">
          <h4>{{ __('welcome.footer_col_legal') }}</h4>
          <a href="#">{{ __('welcome.footer_legal_privacy_policy') }}</a>
          <a href="#">{{ __('welcome.footer_legal_terms_of_service') }}</a>
          <a href="#">{{ __('welcome.footer_legal_accessibility') }}</a>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <span>{!! __('welcome.footer_rights', ['year' => date('Y')]) !!}</span>
      <span class="mono" style="font-size:12px;">{{ __('welcome.footer_kept_in_order') }}</span>
    </div>

  </div>
</footer>
