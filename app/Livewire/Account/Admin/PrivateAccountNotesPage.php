<?php

namespace App\Livewire\Account\Admin;

use App\Services\ImpersonationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class PrivateAccountNotesPage extends Component
{
    public string $admin_notes = '';
    public string $notes = '';
    public bool $admin_notes_locked = false;

    public function mount(): void
    {
        if (! app(ImpersonationService::class)->isImpersonating()) {
            abort(403);
        }

        $customer = Auth::guard('customer')->user();

        $this->admin_notes = $customer->admin_notes ?? '';
        $this->notes = $customer->notes ?? '';
        $this->admin_notes_locked = filled($customer->admin_notes);
    }

    public function save(): void
    {
        if (! app(ImpersonationService::class)->isImpersonating()) {
            abort(403);
        }

        $this->validate([
            'admin_notes' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $customer = Auth::guard('customer')->user();

        $data = [
            'notes' => $this->notes ?: null,
        ];

        // Only allow updating admin_notes if it was previously empty
        if (! $this->admin_notes_locked) {
            $data['admin_notes'] = $this->admin_notes ?: null;
            // Lock the field after saving if it now has content
            if (filled($this->admin_notes)) {
                $this->admin_notes_locked = true;
            }
        }

        $customer->update($data);

        session()->flash('success', 'Account notes have been updated.');
    }

    public function render(): View
    {
        return view('livewire.account.admin.private-account-notes-page')
            ->layout('layouts.storefront');
    }
}
