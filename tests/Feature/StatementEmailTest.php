<?php

use App\Mail\StatementMail;
use App\Models\AdminBalanceAdjustment;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    $this->travelTo('2026-11-01 06:00:00');
    $this->customer = User::factory()->create([
        'first_name' => 'Ada', 'last_name' => 'Okafor', 'email' => 'ada@example.test',
        'checking_account_number' => '200012344471', 'created_at' => '2026-09-01',
    ])->refresh();
    $this->admin = User::factory()->create();

    foreach ([['credit', '500.00', 'Salary October', '2026-10-05 09:00'], ['debit', '120.50', '', '2026-10-12 15:30'], ['credit', '75.00', 'Refund September', '2026-09-18 11:00']] as [$direction, $amount, $sender, $when]) {
        $row = AdminBalanceAdjustment::create([
            'user_id' => $this->customer->id, 'admin_id' => $this->admin->id, 'direction' => $direction,
            'balance_pot' => 'available', 'amount' => $amount, 'sender_name' => $sender,
        ]);
        $row->forceFill(['created_at' => $when])->save();
    }
});

test('on the 1st every customer is emailed last month\'s statement', function () {
    Mail::fake();
    $unverified = User::factory()->unverified()->create(['checking_account_number' => '200099990000']);
    $noAccount = User::factory()->create();

    $this->artisan('statements:send')
        ->expectsOutputToContain('for October 2026')
        ->assertSuccessful();

    Mail::assertQueued(StatementMail::class, fn (StatementMail $mail) => $mail->hasTo('ada@example.test') && $mail->startDate === '2026-10-01' && $mail->endDate === '2026-10-31');
    Mail::assertNotQueued(StatementMail::class, fn (StatementMail $mail) => $mail->hasTo($unverified->email) || $mail->hasTo($noAccount->email) || $mail->hasTo($this->admin->email));
});

test('the statement email is a short note with the statement attached as a PDF', function () {
    $mail = new StatementMail($this->customer, '2026-10-01', '2026-10-31');

    $mail->assertHasSubject('Your Ledger statement for October 2026');
    $mail->assertSeeInHtml('Hello Ada Okafor,');
    $mail->assertSeeInHtml('Statement of Account');
    $mail->assertSeeInHtml('Your statement of account for October 2026 is attached to this email as a PDF.');
    $mail->assertSeeInHtml(__('statement.email_monthly_note'));
    $mail->assertSeeInHtml('Team Ledger');
    // The transactions are in the PDF, not the email body.
    $mail->assertDontSeeInHtml('Salary October');
    $attachments = $mail->attachments();
    expect($attachments)->toHaveCount(1)
        ->and($attachments[0]->as)->toBe('Ledger-Statement-2026-10.pdf')
        ->and($attachments[0]->mime)->toBe('application/pdf');

    $pdf = $mail->statement()->pdf();
    expect(substr($pdf, 0, 5))->toBe('%PDF-');
});

test('the PDF lists only that period, with totals and full dates', function () {
    $statement = (new StatementMail($this->customer, '2026-10-01', '2026-10-31'))->statement();
    $html = view('pdf.statement', ['statement' => $statement])->render();

    expect($html)
        ->toContain('Salary October')
        ->not->toContain('Refund September')
        ->toContain('$500.00')
        ->toContain('$120.50')
        ->toContain('05/10/26')
        ->toContain('01/10/2026 - 31/10/2026')
        ->toContain('200012344471')
        ->toContain('ada@example.test');
});

test('the PDF shows opening, closing and running balances with the Ledger stamp and signature', function () {
    // $954.50 today, nothing moved since October: October opened on
    // $954.50 - ($500.00 in - $120.50 out) = $575.00.
    $this->customer->forceFill(['balance' => '954.50'])->save();

    $statement = (new StatementMail($this->customer, '2026-10-01', '2026-10-31'))->statement();

    expect($statement->openingBalance)->toBe(575.0)
        ->and($statement->closingBalance)->toBe(954.5)
        ->and(array_column($statement->items, 'balance'))->toBe([1075.0, 954.5]);

    $html = view('pdf.statement', ['statement' => $statement])->render();

    expect($html)
        ->toContain('Summary')
        ->toContain('Spend account')
        ->toContain('Opening balance')
        ->toContain('$575.00')
        ->toContain('$1,075.00')
        ->toContain('$954.50')
        ->toContain('Date/Time')
        ->toContain('To / From')
        ->toContain('Authorised signature')
        ->toContain('data:image/svg+xml;base64,');
});

test('a statement for an earlier month subtracts what moved after it', function () {
    $this->customer->forceFill(['balance' => '954.50'])->save();

    $september = (new StatementMail($this->customer, '2026-09-01', '2026-09-30'))->statement();

    // October's +$500.00 and -$120.50 happened after September closed.
    expect($september->closingBalance)->toBe(575.0)
        ->and($september->openingBalance)->toBe(500.0);
});

test('a requested statement says to contact support if it was not you', function () {
    $mail = new StatementMail($this->customer, '2026-10-03', '2026-10-10', requested: true);

    $mail->assertSeeInHtml(__('statement.email_requested_note'));
    expect($mail->statement()->filename())->toBe('Ledger-Statement-2026-10-03-to-2026-10-10.pdf');
});

test('a whole month can be sent by hand and a bad month is refused', function () {
    Mail::fake();

    $this->artisan('statements:send', ['--month' => '2026-09'])->assertSuccessful();
    Mail::assertQueued(StatementMail::class, fn (StatementMail $mail) => $mail->startDate === '2026-09-01' && $mail->endDate === '2026-09-30');

    $this->artisan('statements:send', ['--month' => 'September'])->assertFailed();
});

test('statements are scheduled for the 1st of each month', function () {
    $event = collect(app(Schedule::class)->events())
        ->first(fn ($event) => str_contains($event->command ?? '', 'statements:send'));

    expect($event)->not->toBeNull()
        ->and($event->expression)->toBe('0 6 1 * *');
});

test('customers cannot view or download statements in the app', function () {
    expect(Route::has('history.statement'))->toBeFalse()
        ->and(Route::has('history.statement.csv'))->toBeFalse();

    $this->actingAs($this->customer)->get('/history/statement')->assertMethodNotAllowed();
});

test('a customer can email themselves exact dates, only to their own email', function () {
    Mail::fake();

    $this->actingAs($this->customer)
        ->from(route('history'))
        ->post(route('history.statement.email'), ['from' => '2026-10-03', 'to' => '2026-10-10', 'email' => 'attacker@example.test'])
        ->assertRedirect(route('history'))
        ->assertSessionHas('statementSent', 'Your statement for 3 Oct 2026 – 10 Oct 2026 was sent to a•a@example.test.');

    Mail::assertSent(StatementMail::class, fn (StatementMail $mail) => $mail->hasTo('ada@example.test') && $mail->startDate === '2026-10-03' && $mail->endDate === '2026-10-10');
    Mail::assertNotSent(StatementMail::class, fn (StatementMail $mail) => $mail->hasTo('attacker@example.test'));
});

test('a statement for exact dates lists only those days', function () {
    $mail = new StatementMail($this->customer, '2026-10-01', '2026-10-10');
    $html = view('pdf.statement', ['statement' => $mail->statement()])->render();

    $mail->assertHasSubject('Your Ledger statement for 1 Oct 2026 – 10 Oct 2026');
    expect($html)->toContain('Salary October')->not->toContain('12/10/26')
        ->and($mail->statement()->items)->toHaveCount(1);
});

test('a statement cannot cover future days, days before the account, or a backwards range', function () {
    Mail::fake();

    $this->actingAs($this->customer)->post(route('history.statement.email'), ['from' => '2026-10-01', 'to' => '2026-11-05'])
        ->assertSessionHasErrors(['to' => __('history.statement_future')]);

    $this->actingAs($this->customer)->post(route('history.statement.email'), ['from' => '2026-08-20', 'to' => '2026-09-10'])
        ->assertSessionHasErrors(['from' => __('history.statement_too_early')]);

    $this->actingAs($this->customer)->post(route('history.statement.email'), ['from' => '2026-10-10', 'to' => '2026-10-01'])
        ->assertSessionHasErrors(['to' => __('history.statement_to_before_from')]);

    $this->actingAs($this->customer)->post(route('history.statement.email'), ['from' => 'October', 'to' => '2026-10-01'])
        ->assertSessionHasErrors('from');

    Mail::assertNothingSent();
});

test('a statement can cover at most a year', function () {
    Mail::fake();
    $this->customer->forceFill(['created_at' => '2024-01-01'])->save();

    $this->actingAs($this->customer)->post(route('history.statement.email'), ['from' => '2025-01-01', 'to' => '2026-10-31'])
        ->assertSessionHasErrors(['to' => __('history.statement_too_long')]);

    Mail::assertNothingSent();
});

test('statement requests are limited to five every ten minutes', function () {
    Mail::fake();

    foreach (range(1, 5) as $ignored) {
        $this->actingAs($this->customer)->post(route('history.statement.email'), ['from' => '2026-10-01', 'to' => '2026-10-31'])->assertRedirect();
    }

    $this->actingAs($this->customer)->post(route('history.statement.email'), ['from' => '2026-10-01', 'to' => '2026-10-31'])->assertTooManyRequests();
});

test('history lists only the 10 newest transactions', function () {
    foreach (range(1, 12) as $day) {
        $row = AdminBalanceAdjustment::create([
            'user_id' => $this->customer->id, 'admin_id' => $this->admin->id, 'direction' => 'credit',
            'balance_pot' => 'available', 'amount' => '10.00', 'sender_name' => 'Payment '.str_pad((string) $day, 2, '0', STR_PAD_LEFT),
        ]);
        $row->forceFill(['created_at' => '2026-10-'.str_pad((string) (13 + $day), 2, '0', STR_PAD_LEFT).' 12:00'])->save();
    }

    $response = $this->actingAs($this->customer)->get(route('history'))->assertOk();

    // Payments 03-12 are the 10 newest; 01, 02 and the older seeded rows are left out.
    $response->assertSee('Payment 12')->assertSee('Payment 03')
        ->assertDontSee('Payment 02')->assertDontSee('Payment 01')->assertDontSee('Salary October')
        ->assertSee(__('history.showing_latest', ['count' => 10]))
        ->assertDontSee('?page=2', false);

    expect(substr_count($response->getContent(), 'class="tx-row'))->toBe(10);
});

test('history has the statement button and no money in or out totals', function () {
    $this->actingAs($this->customer)->get(route('history'))
        ->assertOk()
        ->assertSee(__('history.statement_button'))
        ->assertSee(route('history.statement.email'), false)
        ->assertSee('value="ada@example.test" disabled readonly', false)
        ->assertDontSee(__('history.money_in'))
        ->assertDontSee(__('history.money_out'));
});

test('the dashboard More menu no longer repeats Pay / Scan', function () {
    Profile::create(['user_id' => $this->customer->id, 'onboarding_completed' => true]);

    $this->actingAs($this->customer)->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee(__('dashboard.more_scan'));
});
