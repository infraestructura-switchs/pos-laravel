<?php

namespace App\Http\Livewire\Admin\Company;

use App\Models\Company;
use App\Models\InvoiceProvider;
use Livewire\Component;

class InvoiceProviderSettings extends Component
{
    public Company $company;

    /** @var \Illuminate\Support\Collection */
    public $providers;

    public function mount(): void
    {
        $this->company = Company::first();
        $this->loadProviders();
    }

    public function render()
    {
        return view('livewire.admin.company.invoice-provider-settings');
    }

    public function toggleStatus(int $providerId): void
    {
        $provider = InvoiceProvider::findOrFail($providerId);
        $provider->status = $provider->status === 'ACTIVE' ? 'INACTIVE' : 'ACTIVE';
        $provider->save();

        $this->loadProviders();

        $label = $provider->status === 'ACTIVE' ? 'activado' : 'desactivado';
        $this->emit('success', "Proveedor \"{$provider->name}\" {$label} correctamente.");
    }

    public function selectProvider(int $providerId): void
    {
        $provider = InvoiceProvider::findOrFail($providerId);

        $this->company->invoice_provider_id = $providerId;
        $this->company->save();

        session()->put('config', $this->company->fresh(['invoiceProvider']));

        $this->loadProviders();

        $this->emit('success', "Proveedor de facturación cambiado a \"{$provider->name}\".");
    }

    private function loadProviders(): void
    {
        $this->providers = InvoiceProvider::orderBy('name')->get();
    }
}
