<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.onboarding')] class extends Component
{
    public int $step = 1;

    public int $totalSteps = 5;

    // ---------- Step 1: Personal ----------
    public string $first_name = '';
    public string $last_name = '';
    public string $middle_name = '';
    public string $email = '';
    public string $phone = '';

    // ---------- Step 2: Next of Kin ----------
    public string $kin_full_name = '';
    public string $kin_phone = '';
    public string $kin_email = '';
    public string $kin_address = '';

    // ---------- Step 3: Profile ----------
    public string $gender = '';
    public string $date_of_birth = '';
    public string $country = '';
    public string $state = '';
    public string $city = '';
    public string $zip_code = '';
    public string $home_address = '';

    // ---------- Step 4: Employment ----------
    public string $employment_status = '';
    public string $occupation = '';
    public string $industry = '';
    public string $annual_income = '';
    public string $expected_annual_income = '';
    public string $main_source_of_income = '';
    public string $net_worth = '';

    // ---------- Step 5: Account ----------
    public string $account_type = '';
    public string $currency = 'USD';
    public bool $agree_terms = false;

    public function mount(): void
    {
        $user = Auth::user();

        if ($user) {
            $this->first_name = $user->first_name ?? '';
            $this->last_name = $user->last_name ?? '';
            $this->middle_name = $user->middle_name ?? '';
            $this->email = $user->email ?? '';
            $this->phone = $user->phone ?? '';
        }

        // Resume wherever they left off, and refill every field already
        // saved — this is what stops a page refresh mid-wizard from wiping
        // out steps that were already completed. Each step's data is saved
        // to the database the moment "Continue" is clicked (see nextStep()
        // and persistStep() below), not only at the very end.
        $profile = $user?->profile;

        if ($profile) {
            $this->kin_full_name = $profile->next_of_kin_name ?? '';
            $this->kin_phone = $profile->next_of_kin_phone ?? '';
            $this->kin_email = $profile->next_of_kin_email ?? '';
            $this->kin_address = $profile->next_of_kin_address ?? '';

            $this->gender = $profile->gender ?? '';
            $this->date_of_birth = $profile->date_of_birth?->format('Y-m-d') ?? '';
            $this->country = $profile->country ?? '';
            $this->state = $profile->state ?? '';
            $this->city = $profile->city ?? '';
            $this->zip_code = $profile->zip_code ?? '';
            $this->home_address = $profile->home_address ?? '';

            $this->employment_status = $profile->employment_status ?? '';
            $this->occupation = $profile->occupation ?? '';
            $this->industry = $profile->industry ?? '';
            $this->annual_income = $profile->annual_income !== null ? (string) $profile->annual_income : '';
            $this->expected_annual_income = $profile->expected_annual_income !== null ? (string) $profile->expected_annual_income : '';
            $this->main_source_of_income = $profile->main_source_of_income ?? '';
            $this->net_worth = $profile->net_worth !== null ? (string) $profile->net_worth : '';

            $this->account_type = $profile->account_type ?? '';
            $this->currency = $profile->currency ?? 'USD';

            $this->step = $profile->onboarding_completed
                ? $this->totalSteps
                : min(max($profile->onboarding_step ?? 1, 1), $this->totalSteps);
        }
    }

    /**
     * Validation rules scoped to the step currently on screen, so moving
     * from step 1 to step 2 doesn't demand step 4's fields already be filled.
     */
    protected function stepRules(): array
    {
        return match ($this->step) {
            1 => [
                'first_name' => ['required', 'string', 'max:100'],
                'last_name' => ['required', 'string', 'max:100'],
                'middle_name' => ['nullable', 'string', 'max:100'],
                'email' => ['required', 'email', 'max:255'],
                'phone' => ['nullable', 'string', 'max:30'],
            ],
            2 => [
                'kin_full_name' => ['required', 'string', 'max:150'],
                'kin_phone' => ['required', 'string', 'max:30'],
                'kin_email' => ['nullable', 'email', 'max:255'],
                'kin_address' => ['required', 'string', 'max:255'],
            ],
            3 => [
                'gender' => ['required', 'string'],
                'date_of_birth' => ['required', 'date', 'before:-18 years'],
                'country' => ['required', 'string', 'max:100'],
                'state' => ['required', 'string', 'max:100'],
                'city' => ['required', 'string', 'max:100'],
                'zip_code' => ['nullable', 'string', 'max:20'],
                'home_address' => ['required', 'string', 'max:255'],
            ],
            4 => [
                'employment_status' => ['required', 'string'],
                'occupation' => ['required', 'string', 'max:150'],
                'industry' => ['required', 'string', 'max:150'],
                'annual_income' => ['required', 'numeric', 'min:0'],
                'expected_annual_income' => ['required', 'numeric', 'min:0'],
                'main_source_of_income' => ['required', 'string'],
                'net_worth' => ['required', 'numeric', 'min:0'],
            ],
            5 => [
                'account_type' => ['required', 'string'],
                'currency' => ['required', 'string', 'max:3'],
                'agree_terms' => ['accepted'],
            ],
            default => [],
        };
    }

    public function nextStep(): void
    {
        $this->validate($this->stepRules());

        if ($this->step < $this->totalSteps) {
            $this->persistStep();
            $this->step++;

            return;
        }

        $this->submit();
    }

    public function previousStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    /**
     * Saves whatever was just validated on the current step, immediately —
     * so if the page gets refreshed one step later, this step's data is
     * already sitting in the database and mount() picks it straight back up.
     */
    protected function persistStep(): void
    {
        $user = Auth::user();

        $data = match ($this->step) {
            2 => [
                'next_of_kin_name' => $this->kin_full_name,
                'next_of_kin_phone' => $this->kin_phone,
                'next_of_kin_email' => $this->kin_email,
                'next_of_kin_address' => $this->kin_address,
            ],
            3 => [
                'gender' => $this->gender,
                'date_of_birth' => $this->date_of_birth,
                'country' => $this->country,
                'state' => $this->state,
                'city' => $this->city,
                'zip_code' => $this->zip_code,
                'home_address' => $this->home_address,
            ],
            4 => [
                'employment_status' => $this->employment_status,
                'occupation' => $this->occupation,
                'industry' => $this->industry,
                'annual_income' => $this->annual_income,
                'expected_annual_income' => $this->expected_annual_income,
                'main_source_of_income' => $this->main_source_of_income,
                'net_worth' => $this->net_worth,
            ],
            default => [],
        };

        if ($this->step === 1) {
            $user->update([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'middle_name' => $this->middle_name,
                'phone' => $this->phone,
            ]);
        }

        $user->profile()->updateOrCreate([], [
            ...$data,
            'onboarding_step' => min($this->step + 1, $this->totalSteps),
        ]);
    }

    /**
     * Shared bracket list for every dollar-figure dropdown on step 4 — the
     * value stored is each bracket's lower bound, so the "annual_income" /
     * "expected_annual_income" / "net_worth" columns stay plain decimals
     * and the existing "numeric" validation rules keep working unchanged.
     *
     * This lives here instead of an @php block in the template on purpose:
     * Blade's compiler pairs up "@php" and "@endphp" by just scanning for
     * that literal text anywhere in the file, so an @endphp here would get
     * wrongly paired with the unrelated inline @php($n = $i + 1) earlier in
     * this same file's stepper loop, corrupting everything in between.
     */
    protected function moneyRanges(): array
    {
        return [
            500 => '$500 - $1,000',
            1000 => '$1,000 - $5,000',
            5000 => '$5,000 - $10,000',
            10000 => '$10,000 - $25,000',
            25000 => '$25,000 - $50,000',
            50000 => '$50,000 - $100,000',
            100000 => '$100,000 - $250,000',
            250000 => '$250,000 - $500,000',
            500000 => '$500,000 - $1,000,000',
            1000000 => '$1,000,000+',
        ];
    }

    /**
     * Lets someone click back onto a step they've already completed, to fix
     * something — but never forward, so nothing downstream stays unvalidated.
     */
    public function goToStep(int $target): void
    {
        if ($target < $this->step && $target >= 1) {
            $this->step = $target;
        }
    }

    public function submit(): void
    {
        $this->validate($this->stepRules());

        $user = Auth::user();

        // Steps 1-4 were already saved to the database as each one was
        // completed (see persistStep()) — this only needs to save step 5's
        // own fields and flip the "done" flag that unlocks the dashboard.
        $user->profile()->updateOrCreate([], [
            'account_type' => $this->account_type,
            'currency' => $this->currency,
            'onboarding_step' => $this->totalSteps,
            'onboarding_completed' => true,
        ]);

        // Onboarding is fully done now — next comes identity verification
        // (upload a government ID + a selfie for admin review), not the
        // dashboard or email verification directly. See KycController::
        // store(), which is what finally sends them on to whichever of
        // those two used to come right here.
        $this->redirect(route('kyc.create'), navigate: true);
    }
}; ?>

<div class="ledger-wizard-card">
    <aside class="ledger-wizard-side">
        <a href="{{ route('home') }}" class="ledger-wizard-brand" wire:navigate>
            <span class="ledger-wizard-mark">L</span>
            <span class="ledger-wizard-word">Ledger</span>
        </a>

        <div class="ledger-wizard-side-body">
            <p class="ledger-wizard-eyebrow">Step {{ $step }} of {{ $totalSteps }}</p>
            <h1 class="ledger-wizard-title">Create your account</h1>
            <p class="ledger-wizard-sub">Sign up and verify your identity to unlock your account.</p>

            <ol class="ledger-wizard-vsteps">
                @foreach(['Personal', 'Next of Kin', 'Profile', 'Employment', 'Account'] as $i => $label)
                    @php($n = $i + 1)
                    <li class="ledger-wizard-vstep @if($n < $step) is-done @elseif($n === $step) is-current @endif">
                        <button
                            type="button"
                            class="ledger-wizard-vstep-dot"
                            @if($n < $step) wire:click="goToStep({{ $n }})" @endif
                            @if($n >= $step) disabled @endif
                            aria-label="{{ $label }}"
                        >
                            @if($n < $step)
                                <svg viewBox="0 0 24 24" fill="none"><path d="M5 12l4 4 10-10" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            @else
                                {{ $n }}
                            @endif
                        </button>
                        <span class="ledger-wizard-vstep-label">{{ $label }}</span>
                    </li>
                @endforeach
            </ol>
        </div>

        <p class="ledger-wizard-side-note">Encrypted, and never shared without your consent.</p>
    </aside>

    <div class="ledger-wizard-main">
    <form wire:submit="nextStep">

        {{-- ================= Step 1: Personal ================= --}}
        {{-- Read-only on purpose: this is exactly what was submitted on the
             register page (first_name/last_name/middle_name/email/phone are
             still set from mount(), so validation on this step still passes
             without the person needing to touch anything). No input fields
             here means nothing can be changed by accident during onboarding. --}}
        @if($step === 1)
            <div class="ledger-wizard-step" wire:key="step-1">
                <div class="ledger-wizard-step-head">
                    <h2>Confirm your details</h2>
                    <p>This is what you registered with.</p>
                </div>

                <div class="ledger-wizard-profile-card">
                    <div class="ledger-wizard-profile-avatar">{{ strtoupper(mb_substr($first_name, 0, 1).mb_substr($last_name, 0, 1)) }}</div>
                    <div class="ledger-wizard-profile-info">
                        <h3>{{ trim("$first_name $middle_name $last_name") }}</h3>
                        <p>{{ $email }}@if($phone !== '')<span class="dot">&middot;</span>{{ $phone }}@endif</p>
                    </div>
                </div>

                <p class="ledger-wizard-readonly-note">
                    To change any of it, update it from your account settings once your account is set up.
                </p>
            </div>
        @endif

        {{-- ================= Step 2: Next of Kin ================= --}}
        @if($step === 2)
            <div class="ledger-wizard-step" wire:key="step-2">
                <div class="ledger-wizard-step-head">
                    <h2>Next of kin</h2>
                    <p>Who should we reach if we can't reach you?</p>
                </div>
                <div class="ledger-wizard-grid">
                    <flux:input wire:model="kin_full_name" label="Full Name" required autofocus />
                    <flux:input wire:model="kin_phone" label="Phone" required />
                    <flux:input wire:model="kin_email" label="Email (optional)" type="email" />
                    <flux:input wire:model="kin_address" label="Home Address" required class="span-2" />
                </div>
            </div>
        @endif

        {{-- ================= Step 3: Profile ================= --}}
        @if($step === 3)
            <div class="ledger-wizard-step" wire:key="step-3">
                <div class="ledger-wizard-step-head">
                    <h2>Your profile</h2>
                    <p>Helps us verify who you are.</p>
                </div>
                <div class="ledger-wizard-grid">
                    <flux:select wire:model="gender" label="Gender" placeholder="Select gender" required>
                        <flux:select.option value="female">Female</flux:select.option>
                        <flux:select.option value="male">Male</flux:select.option>
                        <flux:select.option value="other">Other</flux:select.option>
                        <flux:select.option value="prefer_not_to_say">Prefer not to say</flux:select.option>
                    </flux:select>
                    <flux:input wire:model="date_of_birth" label="Date of Birth" type="date" required />

                    {{-- Country → State/Province → City, each fetched live from a free
                         public geo API (countriesnow.space, no key needed) so the state
                         and city lists always match whatever country was picked. --}}
                    <div
                        class="ledger-location-grid"
                        x-data="{
                            selectedCountry: @entangle('country'),
                            selectedState: @entangle('state'),
                            selectedCity: @entangle('city'),
                            countries: [],
                            states: [],
                            cities: [],
                            loadingCountries: false,
                            loadingStates: false,
                            loadingCities: false,
                            async init() {
                                this.loadingCountries = true;
                                try {
                                    const res = await fetch('https://countriesnow.space/api/v0.1/countries/positions');
                                    const json = await res.json();
                                    this.countries = (json.data || []).map(c => c.name).sort();
                                } catch (e) {
                                    console.error('Could not load country list', e);
                                } finally {
                                    this.loadingCountries = false;
                                }

                                if (this.selectedCountry) {
                                    await this.loadStates(this.selectedCountry, true);
                                }
                                if (this.selectedState) {
                                    await this.loadCities(this.selectedCountry, this.selectedState, true);
                                }
                            },
                            async loadStates(country, keep = false) {
                                if (! keep) { this.selectedState = ''; this.selectedCity = ''; }
                                this.states = [];
                                this.cities = [];
                                if (! country) return;
                                this.loadingStates = true;
                                try {
                                    const res = await fetch('https://countriesnow.space/api/v0.1/countries/states', {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/json' },
                                        body: JSON.stringify({ country }),
                                    });
                                    const json = await res.json();
                                    this.states = (json.data?.states || []).map(s => s.name);
                                } catch (e) {
                                    console.error('Could not load states', e);
                                } finally {
                                    this.loadingStates = false;
                                }
                            },
                            async loadCities(country, state, keep = false) {
                                if (! keep) { this.selectedCity = ''; }
                                this.cities = [];
                                if (! country || ! state) return;
                                this.loadingCities = true;
                                try {
                                    const res = await fetch('https://countriesnow.space/api/v0.1/countries/state/cities', {
                                        method: 'POST',
                                        headers: { 'Content-Type': 'application/json' },
                                        body: JSON.stringify({ country, state }),
                                    });
                                    const json = await res.json();
                                    this.cities = json.data || [];
                                } catch (e) {
                                    console.error('Could not load cities', e);
                                } finally {
                                    this.loadingCities = false;
                                }
                            },
                        }"
                        x-init="init()"
                    >
                        <div class="ledger-field">
                            <label for="wizard-country">Country</label>
                            <select id="wizard-country" class="ledger-native-select" x-model="selectedCountry" @change="loadStates(selectedCountry)" :disabled="loadingCountries">
                                <option value="">Select country</option>
                                <template x-for="c in countries" :key="c">
                                    <option :value="c" x-text="c"></option>
                                </template>
                            </select>
                        </div>
                        <div class="ledger-field">
                            <label for="wizard-state">State / Province</label>
                            <select id="wizard-state" class="ledger-native-select" x-model="selectedState" @change="loadCities(selectedCountry, selectedState)" :disabled="!selectedCountry || loadingStates">
                                <option value="">Select state / province</option>
                                <template x-for="s in states" :key="s">
                                    <option :value="s" x-text="s"></option>
                                </template>
                            </select>
                        </div>
                        <div class="ledger-field">
                            <label for="wizard-city">City</label>
                            <select id="wizard-city" class="ledger-native-select" x-model="selectedCity" :disabled="!selectedState || loadingCities">
                                <option value="">Select city</option>
                                <template x-for="ct in cities" :key="ct">
                                    <option :value="ct" x-text="ct"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <flux:input wire:model="zip_code" label="Zip / Postal Code (optional)" />
                    <flux:input wire:model="home_address" label="Home Address" required />
                </div>
            </div>
        @endif

        {{-- ================= Step 4: Employment ================= --}}
        @if($step === 4)
            <div class="ledger-wizard-step" wire:key="step-4">
                <div class="ledger-wizard-step-head">
                    <h2>Employment &amp; income</h2>
                    <p>So we can tailor your account and limits.</p>
                </div>
                <div class="ledger-wizard-grid">
                    <flux:select wire:model="employment_status" label="Employment Status" placeholder="Select employment status" required>
                        <flux:select.option value="employed">Employed</flux:select.option>
                        <flux:select.option value="self_employed">Self-employed</flux:select.option>
                        <flux:select.option value="retired">Retired</flux:select.option>
                        <flux:select.option value="unemployed">Unemployed</flux:select.option>
                        <flux:select.option value="student">Student</flux:select.option>
                    </flux:select>
                    <flux:input wire:model="occupation" label="Occupation" required />
                    <flux:input wire:model="industry" label="Industry" required class="span-2" />
                    <flux:select wire:model="annual_income" label="Annual Income" placeholder="Select a range" required>
                        @foreach($this->moneyRanges() as $value => $label)
                            <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:select wire:model="expected_annual_income" label="Expected Annual Income" placeholder="Select a range" required>
                        @foreach($this->moneyRanges() as $value => $label)
                            <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:select wire:model="main_source_of_income" label="Main Source of Income" placeholder="Select source" required>
                        <flux:select.option value="salary">Salary / Employment</flux:select.option>
                        <flux:select.option value="business">Business Income</flux:select.option>
                        <flux:select.option value="investments">Investments</flux:select.option>
                        <flux:select.option value="inheritance">Inheritance</flux:select.option>
                        <flux:select.option value="other">Other</flux:select.option>
                    </flux:select>
                    <flux:select wire:model="net_worth" label="Net Worth" placeholder="Select a range" required>
                        @foreach($this->moneyRanges() as $value => $label)
                            <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            </div>
        @endif

        {{-- ================= Step 5: Account ================= --}}
        @if($step === 5)
            <div class="ledger-wizard-step" wire:key="step-5">
                <div class="ledger-wizard-step-head">
                    <h2>Set up your account</h2>
                    <p>Choose how you'll bank with us.</p>
                </div>
                <div class="ledger-wizard-grid">
                    <flux:select wire:model="account_type" label="Account Type" placeholder="Select account type" required>
                        <flux:select.option value="savings">Savings</flux:select.option>
                        <flux:select.option value="checking">Checking</flux:select.option>
                        <flux:select.option value="both">Both</flux:select.option>
                    </flux:select>
                    <flux:select wire:model="currency" label="Preferred Currency" required>
                        <flux:select.option value="USD">USD</flux:select.option>
                        <flux:select.option value="EUR">EUR</flux:select.option>
                        <flux:select.option value="GBP">GBP</flux:select.option>
                    </flux:select>
                </div>

                <div class="ledger-wizard-review">
                    <dl>
                        <dt>Name</dt><dd>{{ trim("$first_name $middle_name $last_name") }}</dd>
                        <dt>Email</dt><dd>{{ $email }}</dd>
                        <dt>Next of kin</dt><dd>{{ $kin_full_name }}</dd>
                        <dt>Location</dt><dd>{{ trim("$city, $state, $country", ', ') }}</dd>
                        <dt>Employment</dt><dd>{{ ucfirst(str_replace('_', ' ', $employment_status)) }}</dd>
                    </dl>
                </div>

                <div style="margin-top:18px;">
                    <flux:checkbox wire:model="agree_terms" label="I agree to the terms of service and privacy policy" />
                </div>
            </div>
        @endif

        <div class="ledger-wizard-actions">
            @if($step > 1)
                <flux:button type="button" variant="outline" wire:click="previousStep">Back</flux:button>
            @else
                <span></span>
            @endif

            <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="nextStep">
                {{ $step < $totalSteps ? 'Continue' : 'Open my account' }}
            </flux:button>
        </div>
    </form>
    </div>
</div>
