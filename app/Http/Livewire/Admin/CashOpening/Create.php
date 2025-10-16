<?php

namespace App\Http\Livewire\Admin\CashOpening;

use App\Models\CashOpening;
use App\Models\Terminal;
use App\Traits\LivewireTrait;
use Livewire\Component;

class Create extends Component
{
    use LivewireTrait;

    protected $listeners = ['openCreate'];

    public $openCreate = false;

    public $terminal_id;

    // Campos del formulario
    public $initial_cash = 0;
    public $initial_coins = 0;
    public $tarjeta_credito = 0;
    public $tarjeta_debito = 0;
    public $cheques = 0;
    public $otros = 0;
    public $total_initial = 0;
    public $observations = '';

    public function mount()
    {
        $this->terminal_id = null;
    }
    
    /**
     * Obtener el objeto terminal actual
     */
    public function getTerminal()
    {
        if ($this->terminal_id) {
            return Terminal::find($this->terminal_id);
        }
        
        return getTerminal();
    }

    public function render()
    {
        return view('livewire.admin.cash-opening.create');
    }

    /**
     * Calcular total inicial cuando cambian los valores
     */
    public function updatedInitialCash()
    {
        $this->calculateTotal();
    }

    public function updatedInitialCoins()
    {
        $this->calculateTotal();
    }

    public function updatedTarjetaCredito()
    {
        $this->calculateTotal();
    }

    public function updatedTarjetaDebito()
    {
        $this->calculateTotal();
    }

    public function updatedCheques()
    {
        $this->calculateTotal();
    }

    public function updatedOtros()
    {
        $this->calculateTotal();
    }

    private function calculateTotal()
    {
        $this->total_initial = intval($this->initial_cash) + intval($this->initial_coins) + 
                              intval($this->tarjeta_credito) + intval($this->tarjeta_debito) + 
                              intval($this->cheques) + intval($this->otros);
    }

    /**
     * Abrir modal de apertura
     */
    public function openCreate(Terminal $terminal = null)
    {
        $terminalObj = ($terminal && $terminal->id) ? $terminal : getTerminal();
        
        // Verificar que haya una terminal válida
        if (!$terminalObj || !$terminalObj->id) {
            return $this->emit('alert', 'No se pudo determinar la terminal. ID usuario: ' . auth()->id());
        }
        
        // Almacenar solo el ID
        $this->terminal_id = $terminalObj->id;
        
        // Verificar si ya hay una caja abierta para esta terminal
        if (CashOpening::hasOpenCash($terminalObj->id)) {
            return $this->emit('alert', 'Ya existe una caja abierta para la terminal ' . $terminalObj->name);
        }

        $this->resetValidation();
        $this->reset(['initial_cash', 'initial_coins', 'tarjeta_credito', 'tarjeta_debito', 'cheques', 'otros', 'total_initial', 'observations']);
        $this->openCreate = true;
    }

    /**
     * Guardar apertura de caja
     */
    public function store()
    {
        $rules = [
            'initial_cash' => 'required|integer|min:0|max:99999999',
            'initial_coins' => 'nullable|integer|min:0|max:99999999',
            'tarjeta_credito' => 'nullable|integer|min:0|max:99999999',
            'tarjeta_debito' => 'nullable|integer|min:0|max:99999999',
            'cheques' => 'nullable|integer|min:0|max:99999999',
            'otros' => 'nullable|integer|min:0|max:99999999',
            'observations' => 'nullable|string|max:255'
        ];

        $attributes = [
            'initial_cash' => 'dinero inicial en efectivo',
            'initial_coins' => 'monedas iniciales',
            'tarjeta_credito' => 'tarjeta de crédito',
            'tarjeta_debito' => 'tarjeta de débito',
            'cheques' => 'cheques',
            'otros' => 'otros métodos de pago',
            'observations' => 'observaciones'
        ];

        $this->validateTerminal();

        $terminal = $this->getTerminal();
        
        // Verificar que tenemos una terminal válida después de la validación
        if (!$terminal || !$terminal->id) {
            return $this->emit('alert', 'No se pudo determinar la terminal. Verifique la configuración.');
        }

        $data = $this->validate($rules, [], $attributes);

        // Verificar nuevamente si no hay caja abierta
        if (CashOpening::hasOpenCash($terminal->id)) {
            return $this->emit('alert', 'Ya existe una caja abierta para esta terminal');
        }

        // Calcular total
        $this->calculateTotal();

        // Crear apertura
        $cashOpening = CashOpening::create([
            'initial_cash' => $this->initial_cash,
            'initial_coins' => $this->initial_coins ?: 0,
            'tarjeta_credito' => $this->tarjeta_credito ?: 0,
            'tarjeta_debito' => $this->tarjeta_debito ?: 0,
            'cheques' => $this->cheques ?: 0,
            'otros' => $this->otros ?: 0,
            'total_initial' => $this->total_initial,
            'observations' => $this->observations,
            'user_id' => auth()->id(),
            'terminal_id' => $terminal->id,
            'opened_at' => now(),
            'is_active' => true,
        ]);

        // Registrar en log (mantenemos compatibilidad)
        $this->logOpening($cashOpening);

        // Disparar evento para imprimir ticket
        $this->dispatchBrowserEvent('print-cash-opening', [
            'terminal' => $terminal->name,
            'user' => auth()->user()->name,
            'initial_cash' => $this->initial_cash,
            'initial_coins' => $this->initial_coins,
            'tarjeta_credito' => $this->tarjeta_credito,
            'tarjeta_debito' => $this->tarjeta_debito,
            'cheques' => $this->cheques,
            'otros' => $this->otros,
            'total_initial' => $this->total_initial,
            'observations' => $this->observations,
            'datetime' => now()->format('d-m-Y H:i:s'),
        ]);

        $this->emit('success', 'Caja abierta exitosamente');
        $this->reset();
        $this->openCreate = false;
        $this->terminal_id = null;
    }

    /**
     * Registrar en log para compatibilidad
     */
    private function logOpening(CashOpening $cashOpening)
    {
        $data = [
            'user_id' => $cashOpening->user_id,
            'user_name' => $cashOpening->user->name,
            'terminal_id' => $cashOpening->terminal_id,
            'terminal_name' => $cashOpening->terminal->name,
            'initial_cash' => $cashOpening->initial_cash,
            'total_initial' => $cashOpening->total_initial,
            'datetime' => $cashOpening->opened_at->format('d-m-Y H:i:s'),
        ];

        \Log::channel('cashRegister')->info('Apertura de caja registrada', $data);
    }

    /**
     * Verificar estado de caja para terminal actual
     */
    public function getCashStatusProperty()
    {
        $terminal = $this->getTerminal();
        
        if (!$terminal || !$terminal->id) {
            return null;
        }

        return CashOpening::getActiveCash($terminal->id);
    }
}