@extends('layouts.app')

@section('content')
<div class="send-wrap">

  <div class="page-header fade-in d1">
    <a href="{{ url()->previous() }}" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    </a>
    <h1>{{ __('cards.title') }}</h1>
    <a href="{{ route('dashboard') }}" class="icon-btn">
      <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/></svg>
    </a>
  </div>

  <!-- ============ card carousel ============ -->
  <div class="card-scroll fade-in d2">

    @forelse($cards as $index => $card)
      <div
        class="card-visual {{ $index === 0 ? 'selected' : '' }} {{ $card->isVirtual() ? 'virtual' : '' }} {{ $card->isFrozen() ? 'frozen' : '' }}"
        id="card-{{ $card->id }}"
        onclick="selectCard({{ $card->id }})"
        style="position:relative;"
      >
        <span class="card-badge {{ $card->isFrozen() ? 'frozen' : '' }}">{{ $card->badgeLabel() }}</span>
        <div class="cv-top left-align">
          <svg width="34" height="24" viewBox="0 0 34 24" fill="none">
            <defs>
              <linearGradient id="chipGrad{{ $card->id }}" x1="0" y1="0" x2="34" y2="24" gradientUnits="userSpaceOnUse">
                <stop offset="0" stop-color="#F3D287"/>
                <stop offset="1" stop-color="#C9A24B"/>
              </linearGradient>
            </defs>
            <rect x="0.5" y="0.5" width="33" height="23" rx="4" fill="url(#chipGrad{{ $card->id }})" stroke="#8C6A22" stroke-width="0.6"/>
            <line x1="12" y1="0.5" x2="12" y2="23.5" stroke="#8C6A22" stroke-width="0.6"/>
            <line x1="22" y1="0.5" x2="22" y2="23.5" stroke="#8C6A22" stroke-width="0.6"/>
            <line x1="0.5" y1="8" x2="33.5" y2="8" stroke="#8C6A22" stroke-width="0.6"/>
            <line x1="0.5" y1="16" x2="33.5" y2="16" stroke="#8C6A22" stroke-width="0.6"/>
            <rect x="12" y="8" width="10" height="8" fill="none" stroke="#8C6A22" stroke-width="0.6" rx="1.5"/>
          </svg>
          <span style="font-family:'Newsreader', serif; font-weight:600; font-size:15px;">Ledger</span>
        </div>
        <div>
          <div class="cv-number">{{ $card->maskedNumber() }}</div>
          <div class="cv-bottom">
            <span class="cv-name">{{ $card->cardholder_name }}</span>
            <span>{{ $card->expiryLabel() }}</span>
          </div>
        </div>
      </div>
    @empty
      <p style="padding:20px 4px; opacity:0.65;">{{ __('cards.no_cards_yet') }}</p>
    @endforelse

    <a href="javascript:void(0)" class="add-card-ghost" onclick="requestNewCard()">
      <div class="ac-circle">
        <svg viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14"/></svg>
      </div>
      {{ __('cards.add_a_card') }}
    </a>
  </div>

  {{-- Clicking "Add a card" above only ever creates a CardRequest, never a
       real Card — an admin has to approve it first. See
       CardController::requestCard()/Card::issueFor() and
       AdminCardController::approve(), which is what actually generates
       the real card number, CVV, and PIN. Once approved, the new card
       shows up in the carousel above like any other, and its eye icon
       reveals that generated number exactly like it does for every other
       card. --}}
  @if($latestRequest && $latestRequest->isPending())
    <div class="card-detail-card fade-in d2" style="margin-bottom:18px;">
      <p style="margin:0; font-size:13.5px;">{{ __('cards.request_pending_prefix') }} {{ strtolower($latestRequest->typeLabel()) }} {{ __('cards.request_pending_suffix') }}</p>
    </div>
  @elseif($latestRequest && $latestRequest->isDeclined())
    <div class="card-detail-card fade-in d2" style="margin-bottom:18px;">
      <p style="margin:0 0 4px; font-size:13.5px; font-weight:600;">{{ __('cards.request_declined') }}</p>
      @if($latestRequest->decline_reason)
        <p style="margin:0; font-size:12.5px; opacity:0.7;">{{ $latestRequest->decline_reason }}</p>
      @endif
      <p style="margin:6px 0 0; font-size:12.5px; opacity:0.7;">{{ __('cards.request_declined_tap_again') }}</p>
    </div>
  @endif

  <form id="cardRequestForm" method="POST" action="{{ route('card.request') }}" style="display:none;">
    @csrf
    <input type="hidden" name="type" id="cardRequestTypeField" value="physical">
  </form>

  @if($cards->isNotEmpty())
    <!-- ============ quick actions for the selected card ============ -->
    <div class="actions fade-in d3" style="padding-top:12px;">
      <button type="button" class="action" onclick="toggleFreeze()">
        <div class="circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 2v20M2 12h20M4.9 4.9l14.2 14.2M19.1 4.9L4.9 19.1"/></svg></div>
        <span id="freezeActionLabel">{{ __('cards.action_freeze') }}</span>
      </button>
      <button type="button" class="action" onclick="revealPin()">
        <div class="circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg></div>
        <span>{{ __('cards.action_view_pin') }}</span>
      </button>
      <button type="button" class="action" onclick="editLimit()">
        <div class="circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 2v4M12 18v4M4.9 4.9l2.8 2.8M16.3 16.3l2.8 2.8M2 12h4M18 12h4M4.9 19.1l2.8-2.8M16.3 7.7l2.8-2.8"/></svg></div>
        <span>{{ __('cards.action_limit') }}</span>
      </button>
      <button type="button" class="action" onclick="replaceCard()">
        <div class="circle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M21 12a9 9 0 1 1-3-6.7"/><path d="M21 3v6h-6"/></svg></div>
        <span>{{ __('cards.action_replace') }}</span>
      </button>
    </div>

    <!-- ============ card details ============ -->
    <p class="section-label">{{ __('cards.card_details_heading') }}</p>
    <div class="card-detail-card fade-in d3">
      <div class="card-detail-row">
        <div>
          <p class="cd-label">{{ __('cards.card_number') }}</p>
          <p class="cd-value" id="cardNumberValue">•••• •••• •••• ••••</p>
        </div>
        <div class="cd-actions">
          <button type="button" class="icon-mini-btn" onclick="toggleReveal()" id="revealBtn">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7Z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
          <button type="button" class="icon-mini-btn" onclick="copyCardNumber()">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><rect x="9" y="9" width="11" height="11" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/></svg>
          </button>
        </div>
      </div>
      <div class="card-detail-row">
        <div>
          <p class="cd-label">{{ __('cards.cardholder') }}</p>
          <p class="cd-value" id="cardholderValue">-</p>
        </div>
      </div>
      <div class="card-detail-row">
        <div>
          <p class="cd-label">{{ __('cards.expiry') }}</p>
          <p class="cd-value" id="cardExpiryValue">-</p>
        </div>
        <div>
          <p class="cd-label">{{ __('cards.cvv') }}</p>
          <p class="cd-value" id="cardCvvValue">•••</p>
        </div>
      </div>
      <div class="card-detail-row">
        <div>
          <p class="cd-label">{{ __('cards.pin') }}</p>
          <p class="cd-value" id="cardPinValue">••••</p>
        </div>
        <div class="cd-actions">
          <button type="button" class="icon-mini-btn" onclick="changePin()" title="{{ __('cards.change_pin_title') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
          </button>
        </div>
      </div>
      <div class="card-detail-row">
        <div>
          <p class="cd-label">{{ __('cards.spending_limit') }}</p>
          <p class="cd-value" id="cardLimitValue">-</p>
        </div>
        <div class="cd-actions">
          <button type="button" class="icon-mini-btn" onclick="editLimit()" title="{{ __('cards.change_limit_title') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
          </button>
        </div>
      </div>
    </div>

    <!-- ============ freeze toggle ============ -->
    <div class="card-detail-card fade-in d3">
      <div class="freeze-row">
        <div class="fr-text">
          <p class="t">{{ __('cards.freeze_this_card') }}</p>
          <p class="d">{{ __('cards.freeze_this_card_desc') }}</p>
        </div>
        <label class="toggle-switch">
          <input type="checkbox" id="freezeToggle" onchange="onFreezeToggle()">
          <span class="track"></span>
          <span class="thumb"></span>
        </label>
      </div>
    </div>

    <!-- ============ card settings ============ -->
    <p class="section-label">{{ __('cards.card_settings_heading') }}</p>
    <div class="setting-list fade-in d3" style="margin-bottom:18px;">
      <a href="javascript:void(0)" class="setting-item" onclick="editLimit()">
        <div class="s-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M12 2v4M12 18v4M4.9 4.9l2.8 2.8M16.3 16.3l2.8 2.8M2 12h4M18 12h4M4.9 19.1l2.8-2.8M16.3 7.7l2.8-2.8"/></svg>
        </div>
        <span class="label">{{ __('cards.setting_spending_limit') }}</span>
        <span id="limitStateInline" style="font-size:12px; opacity:0.6; margin-right:6px;">-</span>
        <svg class="s-chev" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6"/></svg>
      </a>
      <a href="javascript:void(0)" class="setting-item" onclick="toggleContactless()">
        <div class="s-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M4 15a4 4 0 0 1 4-4h8a4 4 0 0 1 0 8H8a4 4 0 0 1-4-4Z"/><circle cx="8" cy="15" r="1.4"/></svg>
        </div>
        <span class="label">{{ __('cards.setting_contactless_payments') }}</span>
        <span id="contactlessState" style="font-size:12px; opacity:0.6; margin-right:6px;">-</span>
        <svg class="s-chev" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6"/></svg>
      </a>
      <a href="javascript:void(0)" class="setting-item" onclick="toggleOnlinePayments()">
        <div class="s-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><circle cx="12" cy="12" r="9"/><path d="M8 12h8M12 8v8"/></svg>
        </div>
        <span class="label">{{ __('cards.setting_online_payments') }}</span>
        <span id="onlineState" style="font-size:12px; opacity:0.6; margin-right:6px;">-</span>
        <svg class="s-chev" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6"/></svg>
      </a>
      <a href="javascript:void(0)" class="setting-item" onclick="revealPin()">
        <div class="s-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
        </div>
        <span class="label">{{ __('cards.setting_view_pin') }}</span>
        <svg class="s-chev" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6"/></svg>
      </a>
    </div>

    <p class="section-label">{{ __('cards.danger_zone_heading') }}</p>
    <div class="setting-list fade-in d3">
      <a href="javascript:void(0)" class="setting-item danger" onclick="reportLostOrStolen()">
        <div class="s-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/><path d="M12 9v4M12 17h.01"/></svg>
        </div>
        <span class="label">{{ __('cards.report_lost_or_stolen') }}</span>
        <svg class="s-chev" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6"/></svg>
      </a>
      <a href="javascript:void(0)" class="setting-item danger" onclick="closeCard()">
        <div class="s-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.7"><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
        </div>
        <span class="label">{{ __('cards.close_this_card') }}</span>
        <svg class="s-chev" viewBox="0 0 24 24" fill="none"><path d="M9 18l6-6-6-6"/></svg>
      </a>
    </div>

    <!-- ============ recent card activity ============ -->
    <p class="section-label" style="margin-top:22px;">{{ __('cards.recent_activity_heading') }}</p>
    <div class="tx-card fade-in d3" id="cardActivityList"></div>
    <p id="cardActivityEmpty" style="display:none; padding:16px 4px; opacity:0.65;">{{ __('cards.no_activity_yet') }}</p>
  @endif

</div>

<div class="ledger-toast" id="ledgerToast">
  <svg viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5"/></svg>
  <span id="toastText">{{ __('cards.toast_copied') }}</span>
</div>

{{-- Hidden forms used for the three actions that redirect with a flash
     message instead of updating in place (report lost/stolen, close,
     replace) — their action URL is filled in for the currently selected
     card right before each submit, see urlFor() below. --}}
<form id="reportLostForm" method="POST" style="display:none;">@csrf</form>
<form id="closeCardForm" method="POST" style="display:none;">@csrf</form>
<form id="replaceCardForm" method="POST" style="display:none;">@csrf</form>

<?php
    $cardsForJs = $cards->mapWithKeys(function ($c) {
        return [$c->id => [
            'number' => $c->formattedNumber(),
            'masked' => $c->maskedNumber(),
            'cardholderName' => $c->cardholder_name,
            'cvv' => $c->cvv,
            'pin' => $c->pin,
            'hasPin' => ! empty($c->pin),
            'expiry' => $c->expiryLabel(),
            'frozen' => $c->isFrozen(),
            'virtual' => $c->isVirtual(),
            'contactless' => $c->contactless_enabled,
            'onlinePayments' => $c->online_payments_enabled,
            'limitLabel' => $c->limitLabel(),
            'transactions' => $c->transactions->map(function ($t) {
                return [
                    'merchant' => $t->merchant,
                    'category' => $t->category,
                    'amountLabel' => '–$'.number_format((float) $t->amount, 2),
                    'dateLabel' => $t->occurred_at->isToday()
                        ? __('cards.date_today')
                        : ($t->occurred_at->isYesterday() ? __('cards.date_yesterday') : $t->occurred_at->format('M j')),
                ];
            })->values(),
        ]];
    });
?>
<script>
  window.LedgerCardsConfig = {
    csrfToken: '{{ csrf_token() }}',
    hasPendingCardRequest: {{ ($latestRequest && $latestRequest->isPending()) ? 'true' : 'false' }},
    cardUrls: {
      freeze: "{{ route('card.freeze', ['card' => 'CARD_ID']) }}",
      pin: "{{ route('card.pin.update', ['card' => 'CARD_ID']) }}",
      limit: "{{ route('card.limit.update', ['card' => 'CARD_ID']) }}",
      contactless: "{{ route('card.contactless.toggle', ['card' => 'CARD_ID']) }}",
      online: "{{ route('card.online-payments.toggle', ['card' => 'CARD_ID']) }}",
      reportLost: "{{ route('card.report-lost', ['card' => 'CARD_ID']) }}",
      close: "{{ route('card.close', ['card' => 'CARD_ID']) }}",
      replace: "{{ route('card.replace', ['card' => 'CARD_ID']) }}",
    },
    cards: @json($cardsForJs),
    firstCardId: {{ $cards->first()->id ?? 'null' }},
    statusMessage: @if(session('status')) @json(session('status')) @else null @endif,
    i18n: {
      stateOn: @json(__('cards.state_on')),
      stateOff: @json(__('cards.state_off')),
      actionFreeze: @json(__('cards.action_freeze')),
      actionUnfreeze: @json(__('cards.action_unfreeze')),
      toastCardFrozen: @json(__('cards.toast_card_frozen')),
      toastCardUnfrozen: @json(__('cards.toast_card_unfrozen')),
      toastCouldNotUpdateCard: @json(__('cards.toast_could_not_update_card')),
      toastCardNumberCopied: @json(__('cards.toast_card_number_copied')),
      toastUnableToCopy: @json(__('cards.toast_unable_to_copy')),
      toastPinRevealedBelow: @json(__('cards.toast_pin_revealed_below')),
      promptCurrentPin: @json(__('cards.prompt_current_pin')),
      toastPinMustBe4Digits: @json(__('cards.toast_pin_must_be_4_digits')),
      promptNewPin: @json(__('cards.prompt_new_pin')),
      promptConfirmPin: @json(__('cards.prompt_confirm_pin')),
      toastPinsDidNotMatch: @json(__('cards.toast_pins_did_not_match')),
      toastCouldNotUpdatePin: @json(__('cards.toast_could_not_update_pin')),
      toastPinUpdated: @json(__('cards.toast_pin_updated')),
      toastCouldNotUpdatePinRetry: @json(__('cards.toast_could_not_update_pin_retry')),
      noLimitSet: @json(__('cards.no_limit_set')),
      promptSetSpendingLimit: @json(__('cards.prompt_set_spending_limit')),
      toastEnterValidAmount: @json(__('cards.toast_enter_valid_amount')),
      toastSpendingLimitUpdated: @json(__('cards.toast_spending_limit_updated')),
      toastCouldNotUpdateLimitRetry: @json(__('cards.toast_could_not_update_limit_retry')),
      toastContactlessTurnedOn: @json(__('cards.toast_contactless_turned_on')),
      toastContactlessTurnedOff: @json(__('cards.toast_contactless_turned_off')),
      toastCouldNotUpdateRetry: @json(__('cards.toast_could_not_update_retry')),
      toastOnlinePaymentsTurnedOn: @json(__('cards.toast_online_payments_turned_on')),
      toastOnlinePaymentsTurnedOff: @json(__('cards.toast_online_payments_turned_off')),
      confirmReportLostMessage: @json(__('cards.confirm_report_lost_message')),
      confirmReportLostButton: @json(__('cards.confirm_report_lost_button')),
      confirmCloseCardMessage: @json(__('cards.confirm_close_card_message')),
      confirmCloseCardButton: @json(__('cards.confirm_close_card_button')),
      confirmReplaceCardMessage: @json(__('cards.confirm_replace_card_message')),
      confirmReplaceCardButton: @json(__('cards.confirm_replace_card_button')),
      toastRequestAlreadyPending: @json(__('cards.toast_request_already_pending')),
      promptRequestCardType: @json(__('cards.prompt_request_card_type')),
      toastTypePhysicalOrVirtual: @json(__('cards.toast_type_physical_or_virtual')),
      optionPhysicalCard: @json(__('cards.option_physical_card')),
      optionVirtualCard: @json(__('cards.option_virtual_card')),
      confirmRequestCardPrefix: @json(__('cards.confirm_request_card_prefix')),
      confirmRequestCardSuffix: @json(__('cards.confirm_request_card_suffix')),
      confirmRequestCardButton: @json(__('cards.confirm_request_card_button')),
      badgeFrozen: @json(__('cards.badge_frozen')),
      badgeVirtual: @json(__('cards.badge_virtual')),
      badgeActive: @json(__('cards.badge_active')),
    },
  };
</script>
@endsection
