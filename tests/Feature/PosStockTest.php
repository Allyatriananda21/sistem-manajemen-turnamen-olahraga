<?php

use App\Livewire\Admin\PosCashier;
use App\Models\PosProduct;
use App\Models\PosTransaction;
use App\Models\TournamentTeam;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function makeProductWithStock(int $stock): PosProduct
{
    return PosProduct::create([
        'product_name' => 'Air Mineral Test',
        'price' => 5000,
        'stock' => $stock,
    ]);
}

// ---------------------------------------------------------------------------
// Stock validation tests
// ---------------------------------------------------------------------------

test('checkout succeeds and decrements stock when quantity is within stock', function () {
    $kasir = User::factory()->create(['role' => 'kasir']);
    $product = makeProductWithStock(10);

    $component = Livewire::actingAs($kasir)
        ->test(PosCashier::class);

    // Manually set cart with qty = 3 (within stock of 10)
    $component->set('cart', [
        $product->id => [
            'id' => $product->id,
            'name' => $product->product_name,
            'price' => 5000.0,
            'qty' => 3,
            'subtotal' => 15000.0,
        ],
    ]);

    $component->set('amountPaid', '15000');
    $component->call('checkout');

    // No checkout error
    $component->assertHasNoErrors('checkout');

    // Transaction recorded
    expect(PosTransaction::count())->toBe(1);

    // Stock decremented correctly
    $product->refresh();
    expect($product->stock)->toBe(7);
});

test('checkout fails and does not decrement stock when quantity exceeds stock', function () {
    $kasir = User::factory()->create(['role' => 'kasir']);
    $product = makeProductWithStock(2); // only 2 in stock

    $component = Livewire::actingAs($kasir)
        ->test(PosCashier::class);

    // Cart requests qty = 5, but only 2 available
    $component->set('cart', [
        $product->id => [
            'id' => $product->id,
            'name' => $product->product_name,
            'price' => 5000.0,
            'qty' => 5,
            'subtotal' => 25000.0,
        ],
    ]);

    $component->set('amountPaid', '25000');
    $component->call('checkout');

    // Error must be present
    $component->assertHasErrors('checkout');

    // No transaction should have been created
    expect(PosTransaction::count())->toBe(0);

    // Stock must remain unchanged
    $product->refresh();
    expect($product->stock)->toBe(2);
});

test('checkout is atomic — all items or none when one item has insufficient stock', function () {
    $kasir = User::factory()->create(['role' => 'kasir']);
    $product1 = makeProductWithStock(5);
    $product2 = makeProductWithStock(1); // only 1 in stock

    $component = Livewire::actingAs($kasir)
        ->test(PosCashier::class);

    $component->set('cart', [
        $product1->id => [
            'id' => $product1->id, 'name' => $product1->product_name,
            'price' => 5000.0, 'qty' => 3, 'subtotal' => 15000.0,
        ],
        $product2->id => [
            'id' => $product2->id, 'name' => $product2->product_name,
            'price' => 5000.0, 'qty' => 3, 'subtotal' => 15000.0, // exceeds stock
        ],
    ]);

    $component->set('amountPaid', '30000');
    $component->call('checkout');

    $component->assertHasErrors('checkout');

    // Neither product's stock should have changed
    $product1->refresh();
    $product2->refresh();

    expect($product1->stock)->toBe(5); // unchanged
    expect($product2->stock)->toBe(1); // unchanged
    expect(PosTransaction::count())->toBe(0);
});

test('checkout clears cart and shows receipt on success', function () {
    $kasir = User::factory()->create(['role' => 'kasir']);
    $product = makeProductWithStock(10);

    $component = Livewire::actingAs($kasir)
        ->test(PosCashier::class)
        ->set('cart', [
            $product->id => [
                'id' => $product->id, 'name' => $product->product_name,
                'price' => 5000.0, 'qty' => 1, 'subtotal' => 5000.0,
            ],
        ])
        ->set('amountPaid', '10000')
        ->call('checkout');

    $component->assertHasNoErrors('checkout');

    // Cart must be cleared
    $component->assertSet('cart', []);

    // Receipt must be populated
    expect($component->get('lastReceipt'))->not->toBeNull();
    expect($component->get('lastReceipt')['change'])->toBe(5000.0);
});

test('amountPaid with dot separators parses correctly', function () {
    $kasir = User::factory()->create(['role' => 'kasir']);
    $product = makeProductWithStock(10);

    $component = Livewire::actingAs($kasir)
        ->test(PosCashier::class)
        ->set('cart', [
            $product->id => [
                'id' => $product->id, 'name' => $product->product_name,
                'price' => 5000.0, 'qty' => 1, 'subtotal' => 5000.0,
            ],
        ])
        ->set('amountPaid', '10.000') // with dot separator
        ->call('checkout');

    $component->assertHasNoErrors('checkout');
    $component->assertSet('cart', []);
    expect($component->get('lastReceipt'))->not->toBeNull();
    expect($component->get('lastReceipt')['change'])->toBe(5000.0);
});

test('processTeamPayment converts amount to thousands if less than 1000', function () {
    $kasir = User::factory()->create(['role' => 'kasir']);
    $team = TournamentTeam::create([
        'name' => 'Tim Futsal A',
        'sport_type' => 'Futsal',
        'contact_person' => 'Budi',
        'phone' => '08123456789',
        'status' => 'approved',
        'payment_status' => 'unpaid',
    ]);

    $component = Livewire::actingAs($kasir)
        ->test(PosCashier::class)
        ->call('selectTeam', $team->id)
        ->set('teamPaymentAmount', '450') // input 450
        ->call('processTeamPayment');

    $component->assertHasNoErrors('teamPaymentAmount');

    // The team payment status should be updated to paid
    $team->refresh();
    expect($team->payment_status)->toBe('paid');

    // The transaction should be recorded with amount 450000
    $transaction = PosTransaction::where('team_id', $team->id)->first();
    expect($transaction)->not->toBeNull();
    expect((float) $transaction->total_amount)->toBe(450000.0);
});

test('processDenda records transaction type denda', function () {
    $kasir = User::factory()->create(['role' => 'kasir']);
    $team = TournamentTeam::create([
        'name' => 'Tim Futsal B',
        'sport_type' => 'Futsal',
        'contact_person' => 'Joko',
        'phone' => '08123456780',
        'status' => 'approved',
        'payment_status' => 'unpaid',
    ]);

    $component = Livewire::actingAs($kasir)
        ->test(PosCashier::class)
        ->call('selectDendaTeam', $team->id)
        ->set('dendaAmount', '50.000') // input 50.000 with dots
        ->call('processDenda');

    $component->assertHasNoErrors('dendaAmount');

    // The transaction should be recorded with amount 50,000 and type denda
    $transaction = PosTransaction::where('team_id', $team->id)
        ->where('transaction_type', 'denda')
        ->first();
    expect($transaction)->not->toBeNull();
    expect((float) $transaction->total_amount)->toBe(50000.0);
});
