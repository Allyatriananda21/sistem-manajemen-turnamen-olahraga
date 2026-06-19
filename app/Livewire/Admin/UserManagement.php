<?php

namespace App\Livewire\Admin;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Manajemen User')]
class UserManagement extends Component
{
    use WithPagination;

    // Form fields
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $role = 'admin';

    public bool $showCreateModal = false;

    public string $search = '';

    /**
     * Reset pagination when search changes.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Open the create user modal.
     */
    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    /**
     * Store a new user.
     */
    public function store(): void
    {
        $request = new StoreUserRequest;
        $request->merge([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->role,
        ]);

        $validated = $this->validate($this->storeRules(), $this->storeMessages());

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => true,
        ]);

        $this->showCreateModal = false;
        $this->resetForm();

        Flux::toast(variant: 'success', text: 'User berhasil ditambahkan.');
    }

    /**
     * Toggle the is_active status of a user.
     */
    public function toggleActive(int $userId): void
    {
        $user = User::findOrFail($userId);

        // Prevent admin from deactivating their own account
        if ($user->id === auth()->id()) {
            Flux::toast(variant: 'warning', text: 'Anda tidak dapat menonaktifkan akun sendiri.');

            return;
        }

        $user->update(['is_active' => ! $user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        Flux::toast(variant: 'success', text: "User berhasil {$status}.");
    }

    /**
     * Update the role of a user.
     */
    public function updateRole(int $userId, string $newRole): void
    {
        if (! in_array($newRole, ['admin', 'wasit', 'kasir'], strict: true)) {
            Flux::toast(variant: 'danger', text: 'Role tidak valid.');

            return;
        }

        // Prevent admin from changing their own role
        if ($userId === auth()->id()) {
            Flux::toast(variant: 'warning', text: 'Anda tidak dapat mengubah role akun sendiri.');

            return;
        }

        $user = User::findOrFail($userId);
        $user->update(['role' => $newRole]);

        Flux::toast(variant: 'success', text: 'Role user berhasil diubah.');
    }

    /**
     * Render the component.
     */
    public function render()
    {
        $users = User::query()
            ->when(
                $this->search,
                fn ($query) => $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                })
            )
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.user-management', compact('users'));
    }

    /**
     * Reset the form fields.
     */
    private function resetForm(): void
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = 'admin';
        $this->resetValidation();
    }

    /**
     * Validation rules for storing a new user.
     *
     * @return array<string, list<string>>
     */
    private function storeRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:admin,wasit,kasir'],
        ];
    }

    /**
     * Custom validation messages for the store action.
     *
     * @return array<string, string>
     */
    private function storeMessages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
        ];
    }
}
