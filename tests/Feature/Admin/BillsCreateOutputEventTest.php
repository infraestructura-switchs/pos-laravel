<?php

namespace Tests\Feature\Admin;

use App\Http\Livewire\Admin\Bills\Create;
use App\Models\Bill;
use Tests\TestCase;

class BillsCreateOutputEventTest extends TestCase
{
    public function test_event_name_resolution_by_type_bill(): void
    {
        $component = new FakeBillsCreateComponent();

        $this->assertSame('print-ticket', $component->getOutputEventNamePublic('1'));
        $this->assertSame('direct-sale-download-ticket', $component->getOutputEventNamePublic('0'));
        $this->assertSame('direct-sale-download-ticket', $component->getOutputEventNamePublic('2'));
    }

    public function test_event_payload_is_structured_with_bill_id(): void
    {
        $component = new FakeBillsCreateComponent();

        $payloadFromInt = $component->makeOutputEventPayloadPublic(24);
        $payloadFromString = $component->makeOutputEventPayloadPublic('24');

        $this->assertSame(24, $payloadFromInt['bill_id']);
        $this->assertStringContainsString('/administrador/facturas-download/24', $payloadFromInt['download_url']);

        $this->assertSame(24, $payloadFromString['bill_id']);
        $this->assertStringContainsString('/administrador/facturas-download/24', $payloadFromString['download_url']);
    }

    public function test_dispatch_output_event_sends_expected_name_and_payload(): void
    {
        $component = new FakeBillsCreateComponent();
        $bill = new Bill();
        $bill->id = 31;

        $component->dispatchOutputEventPublic($bill, '2');

        $this->assertSame('direct-sale-download-ticket', $component->dispatchedEventName);
        $this->assertSame(31, $component->dispatchedEventPayload['bill_id']);
        $this->assertStringContainsString('/administrador/facturas-download/31', $component->dispatchedEventPayload['download_url']);
    }
}

class FakeBillsCreateComponent extends Create
{
    public ?string $dispatchedEventName = null;

    public $dispatchedEventPayload = null;

    public function getOutputEventNamePublic(string $typeBill): string
    {
        return $this->getOutputEventName($typeBill);
    }

    public function makeOutputEventPayloadPublic($billId): array
    {
        return $this->makeOutputEventPayload($billId);
    }

    public function dispatchOutputEventPublic(Bill $bill, string $typeBill): void
    {
        $this->dispatchOutputEvent($bill, $typeBill);
    }

    public function dispatchBrowserEvent($event, $data = null)
    {
        $this->dispatchedEventName = $event;
        $this->dispatchedEventPayload = $data;
    }
}
