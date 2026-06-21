<?php

namespace App\Livewire\Admin;

use App\Models\PosProduct;
use Flux\Flux;
use Illuminate\Contracts\Validation\ValidationRule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Produk POS')]
class PosProductManagement extends Component
{
    use WithPagination;

    public string $search = '';

    // Shared form fields for create & edit
    public string $formProductName = '';

    public string $formPrice = '';

    public int $formStock = 0;

    // Modal state
    public bool $showFormModal = false;

    public bool $showDeleteModal = false;

    public ?int $editingProductId = null;

    public ?int $deletingProductId = null;

    public string $deletingProductName = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    // -----------------------------------------------------------------------
    // Create
    // -----------------------------------------------------------------------

    public function openCreate(): void
    {
        $this->resetForm();
        $this->editingProductId = null;
        $this->showFormModal = true;
    }

    // -----------------------------------------------------------------------
    // Edit
    // -----------------------------------------------------------------------

    public function openEdit(int $productId): void
    {
        $product = PosProduct::findOrFail($productId);

        $this->editingProductId = $productId;
        $this->formProductName = $product->product_name;
        $this->formPrice = (string) $product->price;
        $this->formStock = $product->stock;

        $this->resetValidation();
        $this->showFormModal = true;
    }

    // -----------------------------------------------------------------------
    // Save (create or update)
    // -----------------------------------------------------------------------

    public function save(): void
    {
        $validated = $this->validate(
            $this->formRules(),
            $this->formMessages(),
        );

        $data = [
            'product_name' => $validated['formProductName'],
            'price' => $validated['formPrice'],
            'stock' => $validated['formStock'],
        ];

        if ($this->editingProductId) {
            PosProduct::findOrFail($this->editingProductId)->update($data);
            $message = "Produk \"{$validated['formProductName']}\" berhasil diperbarui.";
        } else {
            PosProduct::create($data);
            $message = "Produk \"{$validated['formProductName']}\" berhasil ditambahkan.";
        }

        $this->showFormModal = false;
        $this->resetForm();

        Flux::toast(variant: 'success', text: $message);
    }

    // -----------------------------------------------------------------------
    // Delete
    // -----------------------------------------------------------------------

    public function openDelete(int $productId): void
    {
        $product = PosProduct::findOrFail($productId);

        $this->deletingProductId = $productId;
        $this->deletingProductName = $product->product_name;
        $this->showDeleteModal = true;
    }

    public function destroy(): void
    {
        $product = PosProduct::findOrFail($this->deletingProductId);
        $name = $product->product_name;

        $product->delete();

        $this->showDeleteModal = false;
        $this->deletingProductId = null;

        Flux::toast(variant: 'danger', text: "Produk \"{$name}\" berhasil dihapus.");
    }

    // -----------------------------------------------------------------------
    // Render
    // -----------------------------------------------------------------------

    public function render()
    {
        $products = PosProduct::query()
            ->when(
                $this->search,
                fn ($q) => $q->where('product_name', 'like', "%{$this->search}%")
            )
            ->orderBy('product_name')
            ->paginate(10);

        return view('livewire.admin.pos-product-management', compact('products'));
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    private function resetForm(): void
    {
        $this->formProductName = '';
        $this->formPrice = '';
        $this->formStock = 0;
        $this->editingProductId = null;
        $this->resetValidation();
    }

    /**
     * @return array<string, list<string|ValidationRule>>
     */
    private function formRules(): array
    {
        return [
            'formProductName' => ['required', 'string', 'max:100'],
            'formPrice' => ['required', 'numeric', 'gt:0'],
            'formStock' => ['required', 'integer', 'min:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function formMessages(): array
    {
        return [
            'formProductName.required' => 'Nama produk wajib diisi.',
            'formProductName.max' => 'Nama produk maksimal 100 karakter.',
            'formPrice.required' => 'Harga wajib diisi.',
            'formPrice.numeric' => 'Harga harus berupa angka.',
            'formPrice.gt' => 'Harga harus lebih dari 0.',
            'formStock.required' => 'Stok wajib diisi.',
            'formStock.integer' => 'Stok harus berupa bilangan bulat.',
            'formStock.min' => 'Stok tidak boleh negatif.',
        ];
    }
}
