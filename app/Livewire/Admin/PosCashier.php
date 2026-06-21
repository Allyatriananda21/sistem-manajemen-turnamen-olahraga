<?php

namespace App\Livewire\Admin;

use App\Models\PosProduct;
use App\Models\PosTransaction;
use App\Models\PosTransactionDetail;
use App\Models\TournamentTeam;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Kasir POS')]
class PosCashier extends Component
{
    public string $search = '';

    public string $amountPaid = '';

    /**
     * Last completed transaction for receipt display.
     *
     * @var array{id: int, cashier: string, total: float, paid: float, change: float, items: list<array{name: string, qty: int, price: float, subtotal: float}>}|null
     */
    public ?array $lastReceipt = null;

    /**
     * Cart items keyed by product ID.
     *
     * Shape: array<int, array{id: int, name: string, price: float, qty: int, subtotal: float}>
     *
     * @var array<int, array{id: int, name: string, price: float, qty: int, subtotal: float}>
     */
    public array $cart = [];

    // -----------------------------------------------------------------------
    // Tagihan Tim properties
    // -----------------------------------------------------------------------

    public string $teamSearch = '';

    public ?int $selectedTeamId = null;

    public string $teamPaymentAmount = '';

    // -----------------------------------------------------------------------
    // Computed properties
    // -----------------------------------------------------------------------

    /**
     * Products with stock > 0, filtered by search term.
     *
     * @return Collection<int, PosProduct>
     */
    #[Computed]
    public function products()
    {
        return PosProduct::where('stock', '>', 0)
            ->when(
                $this->search,
                fn ($q) => $q->where('product_name', 'like', "%{$this->search}%")
            )
            ->orderBy('product_name')
            ->get();
    }

    /**
     * Total price of all items in the cart.
     */
    #[Computed]
    public function total(): float
    {
        return array_sum(array_column($this->cart, 'subtotal'));
    }

    /**
     * Change to return to customer. Negative when underpaid.
     */
    #[Computed]
    public function change(): float
    {
        $paid = (float) ($this->amountPaid ?: 0);

        return $paid - $this->total;
    }

    /**
     * Unpaid teams for tagihan tab, filtered by search.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, TournamentTeam>
     */
    #[Computed]
    public function unpaidTeams()
    {
        return TournamentTeam::where('payment_status', 'unpaid')
            ->where('status', 'approved')
            ->when(
                $this->teamSearch,
                fn ($q) => $q->where('name', 'like', "%{$this->teamSearch}%")
            )
            ->orderBy('name')
            ->get();
    }

    /**
     * The currently selected team for payment.
     */
    #[Computed]
    public function selectedTeam(): ?TournamentTeam
    {
        if (! $this->selectedTeamId) {
            return null;
        }

        return TournamentTeam::find($this->selectedTeamId);
    }

    // -----------------------------------------------------------------------
    // Cart actions
    // -----------------------------------------------------------------------

    /**
     * Add one unit of the given product to the cart.
     * Respects available stock — cannot add more than stock allows.
     */
    public function addToCart(int $productId): void
    {
        $product = PosProduct::find($productId);

        if (! $product || $product->stock <= 0) {
            return;
        }

        if (isset($this->cart[$productId])) {
            // Check against live stock before incrementing
            if ($this->cart[$productId]['qty'] >= $product->stock) {
                return;
            }

            $this->cart[$productId]['qty']++;
            $this->cart[$productId]['subtotal'] = $this->cart[$productId]['qty'] * $this->cart[$productId]['price'];
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->product_name,
                'price' => (float) $product->price,
                'qty' => 1,
                'subtotal' => (float) $product->price,
            ];
        }

        // Invalidate cached computed totals
        unset($this->total, $this->change);
    }

    /**
     * Increment quantity of an existing cart item.
     */
    public function incrementQty(int $productId): void
    {
        if (! isset($this->cart[$productId])) {
            return;
        }

        $product = PosProduct::find($productId);

        if (! $product || $this->cart[$productId]['qty'] >= $product->stock) {
            return;
        }

        $this->cart[$productId]['qty']++;
        $this->cart[$productId]['subtotal'] = $this->cart[$productId]['qty'] * $this->cart[$productId]['price'];

        unset($this->total, $this->change);
    }

    /**
     * Decrement quantity. Removes item if qty reaches zero.
     */
    public function decrementQty(int $productId): void
    {
        if (! isset($this->cart[$productId])) {
            return;
        }

        if ($this->cart[$productId]['qty'] <= 1) {
            $this->removeFromCart($productId);

            return;
        }

        $this->cart[$productId]['qty']--;
        $this->cart[$productId]['subtotal'] = $this->cart[$productId]['qty'] * $this->cart[$productId]['price'];

        unset($this->total, $this->change);
    }

    /**
     * Remove a product entirely from the cart.
     */
    public function removeFromCart(int $productId): void
    {
        unset($this->cart[$productId]);
        unset($this->total, $this->change);
    }

    /**
     * Clear all items from the cart and reset payment input.
     */
    public function clearCart(): void
    {
        $this->cart = [];
        $this->amountPaid = '';

        unset($this->total, $this->change);
    }

    // -----------------------------------------------------------------------
    // Checkout
    // -----------------------------------------------------------------------

    /**
     * Process the retail checkout.
     *
     * Steps inside a single DB transaction:
     *   1. Re-validate every cart item's stock from the database (server-side).
     *   2. Insert one pos_transactions row.
     *   3. Insert pos_transaction_details rows for each cart item.
     *   4. Decrement stock on each pos_products row.
     *
     * On any stock violation the transaction is rolled back and an error is shown.
     * On success the cart is cleared and a receipt summary is stored for display.
     */
    public function checkout(): void
    {
        if (empty($this->cart)) {
            return;
        }

        $paid = (float) ($this->amountPaid ?: 0);

        if ($paid < $this->total) {
            $this->addError('checkout', 'Uang yang dibayarkan kurang dari total belanja.');

            return;
        }

        $cartSnapshot = $this->cart; // capture before clearing
        $totalAmount = $this->total;

        try {
            $transaction = DB::transaction(function () use ($cartSnapshot, $totalAmount): PosTransaction {

                // Step 1 — re-validate stock for every item
                $stockErrors = [];

                foreach ($cartSnapshot as $productId => $item) {
                    $product = PosProduct::lockForUpdate()->find($productId);

                    if (! $product || $product->stock < $item['qty']) {
                        $available = $product ? $product->stock : 0;
                        $stockErrors[] = "Stok \"{$item['name']}\" tidak cukup (tersisa {$available}, diminta {$item['qty']}).";
                    }
                }

                if (! empty($stockErrors)) {
                    throw new \RuntimeException(implode(' | ', $stockErrors));
                }

                // Step 2 — insert transaction header
                $transaction = PosTransaction::create([
                    'transaction_type' => 'retail',
                    'team_id' => null,
                    'total_amount' => $totalAmount,
                    'payment_method' => 'cash',
                    'cashier_name' => Auth::user()->name,
                ]);

                // Step 3 & 4 — insert details and decrement stock
                foreach ($cartSnapshot as $productId => $item) {
                    PosTransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $productId,
                        'quantity' => $item['qty'],
                        'subtotal' => $item['subtotal'],
                    ]);

                    PosProduct::where('id', $productId)
                        ->decrement('stock', $item['qty']);
                }

                return $transaction;
            });

            // Build receipt data before clearing state
            $receiptItems = array_values(array_map(fn ($item) => [
                'name' => $item['name'],
                'qty' => $item['qty'],
                'price' => $item['price'],
                'subtotal' => $item['subtotal'],
            ], $cartSnapshot));

            $this->lastReceipt = [
                'id' => $transaction->id,
                'cashier' => Auth::user()->name,
                'total' => $totalAmount,
                'paid' => $paid,
                'change' => $paid - $totalAmount,
                'items' => $receiptItems,
            ];

            // Clear cart and payment
            $this->cart = [];
            $this->amountPaid = '';

            unset($this->total, $this->change, $this->products);

            Flux::toast(variant: 'success', text: 'Transaksi berhasil disimpan.');

        } catch (\RuntimeException $e) {
            $this->addError('checkout', $e->getMessage());
        }
    }

    /**
     * Dismiss the receipt panel and start a new transaction.
     */
    public function dismissReceipt(): void
    {
        $this->lastReceipt = null;
    }

    // -----------------------------------------------------------------------
    // Tagihan Tim actions
    // -----------------------------------------------------------------------

    /**
     * Select a team to process their registration payment.
     */
    public function selectTeam(int $teamId): void
    {
        $this->selectedTeamId = $teamId;
        $this->teamPaymentAmount = '';
        $this->resetValidation('teamPaymentAmount');
        unset($this->selectedTeam);
    }

    /**
     * Deselect the current team without processing payment.
     */
    public function deselectTeam(): void
    {
        $this->selectedTeamId = null;
        $this->teamPaymentAmount = '';
        $this->resetValidation('teamPaymentAmount');
        unset($this->selectedTeam);
    }

    /**
     * Process registration payment for the selected team.
     *
     * In a single DB transaction:
     *   1. Re-fetch team and verify still unpaid (guard against double payment).
     *   2. Insert pos_transactions row (transaction_type='registrasi').
     *   3. Update team payment_status to 'paid'.
     */
    public function processTeamPayment(): void
    {
        $validated = $this->validate([
            'teamPaymentAmount' => ['required', 'numeric', 'gt:0'],
        ], [
            'teamPaymentAmount.required' => 'Nominal pembayaran wajib diisi.',
            'teamPaymentAmount.numeric' => 'Nominal harus berupa angka.',
            'teamPaymentAmount.gt' => 'Nominal harus lebih dari 0.',
        ]);

        $amount = (float) $validated['teamPaymentAmount'];
        $teamIdNow = $this->selectedTeamId;

        try {
            DB::transaction(function () use ($teamIdNow, $amount): void {
                $team = TournamentTeam::lockForUpdate()->findOrFail($teamIdNow);

                if ($team->payment_status === 'paid') {
                    throw new \RuntimeException("Tim \"{$team->name}\" sudah tercatat lunas.");
                }

                PosTransaction::create([
                    'transaction_type' => 'registrasi',
                    'team_id' => $team->id,
                    'total_amount' => $amount,
                    'payment_method' => 'cash',
                    'cashier_name' => Auth::user()->name,
                ]);

                $team->update(['payment_status' => 'paid']);
            });

            // Refresh computed caches after mutation
            unset($this->unpaidTeams, $this->selectedTeam);

            $this->selectedTeamId = null;
            $this->teamPaymentAmount = '';

            Flux::toast(variant: 'success', text: 'Pembayaran registrasi tim berhasil dicatat.');

        } catch (\RuntimeException $e) {
            $this->addError('teamPaymentAmount', $e->getMessage());
        }
    }

    // -----------------------------------------------------------------------
    // Render
    // -----------------------------------------------------------------------

    public function render()
    {
        return view('livewire.admin.pos-cashier');
    }
}
