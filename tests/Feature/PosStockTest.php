<?php

use App\Models\PosProduct;
use App\Models\PosTransaction;
use App\Models\User;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function makeProductWithStock(int $stock): PosProduct
{
    return PosProduct::create([
        'product_name' => 'Air Mineral Test',
        'price'        => 5000,
        'stock'        => $stock,
    ]);
}

// ---------------------------------------------------------------------------
// Stock validation tests
// ---------------------------------------------------------------------------

test('checkout succeeds and decrements stock when quantity is within stock', function () {
    $kasir   = User::factory()->create(['role' => 'kasir']);
    $product = makeProductWithStock(10);

    $component = Livewire::actingAs($kasir)
        ->test(\App\Livewire\Admin\PosCashier::class);

    // Manually set cart with qty = 3 (within stock of 10)
    $component->set('cart', [
        $product->id => [
            'id'       => $product->id,
            'name'     => $product->product_name,
            'price'    => 5000.0,
            'qty'      => 3,
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
    $kasir   = User::factory()->create(['role' => 'kasir']);
    $product = makeProductWithStock(2); // only 2 in stock

    $component = Livewire::actingAs($kasir)
        ->test(\App\Livewire\Admin\PosCashier::class);

    // Cart requests qty = 5, but only 2 available
    $component->set('cart', [
        $product->id => [
            'id'       => $product->id,
            'name'     => $product->product_name,
            'price'    => 5000.0,
            'qty'      => 5,
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
    $kasir    = User::factory()->create(['role' => 'kasir']);
    $product1 = makeProductWithStock(5);
    $product2 = makeProductWithStock(1); // only 1 in stock

    $component = Livewire::actingAs($kasir)
        ->test(\App\Livewire\Admin\PosCashier::class);

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
    $kasir   = User::factory()->create(['role' => 'kasir']);
    $product = makeProductWithStock(10);

    $component = Livewire::actingAs($kasir)
        ->test(\App\Livewire\Admin\PosCashier::class)
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
