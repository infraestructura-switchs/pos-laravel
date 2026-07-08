<?php

namespace Tests\Feature\Admin;

use App\Http\Controllers\Admin\BillController;
use App\Models\Bill;
use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Tests\TestCase;

class BillControllerTypeBillTemplateTest extends TestCase
{
    public function test_type_bill_zero_uses_standard_bill_template(): void
    {
        $normalPdf = $this->mockMpdf();
        $controller = new FakeBillController($normalPdf, $this->mockMpdf());
        $bill = $this->makeBillWithDetails(['Producto A']);
        $company = (object) ['type_bill' => '0'];
        $range = (object) ['resolution_number' => null];

        $normalPdf->expects($this->once())
            ->method('setFooter')
            ->with('{PAGENO}');

        $normalPdf->expects($this->once())
            ->method('SetHTMLFooter')
            ->with($this->callback(fn ($view) => $this->viewName($view) === 'pdf.bill.footer' && $this->viewHasDataKey($view, 'company')));

        $normalPdf->expects($this->once())
            ->method('WriteHTML')
            ->with(
                $this->callback(fn ($view) => $this->viewName($view) === 'pdf.bill.template'),
                HTMLParserMode::HTML_BODY
            );

        $pdf = $controller->buildPdfByTypePublic($company, $bill, $range);

        $this->assertSame($normalPdf, $pdf);
    }

    public function test_type_bill_one_uses_ticket_template(): void
    {
        $ticketPdf = $this->mockMpdf();
        $controller = new FakeBillController($this->mockMpdf(), $ticketPdf);
        $bill = $this->makeBillWithDetails(['Producto A', 'Producto B']);
        $company = (object) ['type_bill' => '1'];
        $range = (object) ['resolution_number' => null];

        $ticketPdf->expects($this->once())
            ->method('SetHTMLFooter')
            ->with($this->callback(fn ($view) => $this->viewName($view) === 'pdf.ticket.footer' && $this->viewHasDataKey($view, 'company')));

        $ticketPdf->expects($this->once())
            ->method('WriteHTML')
            ->with(
                $this->callback(fn ($view) => $this->viewName($view) === 'pdf.ticket.template'),
                HTMLParserMode::HTML_BODY
            );

        $pdf = $controller->buildPdfByTypePublic($company, $bill, $range);

        $this->assertSame($ticketPdf, $pdf);
        $this->assertNotNull($controller->receivedTicketHeight);
        $this->assertGreaterThan(0, $controller->receivedTicketHeight);
    }

    public function test_type_bill_other_value_uses_bill_v2_template(): void
    {
        $normalPdf = $this->mockMpdf();
        $controller = new FakeBillController($normalPdf, $this->mockMpdf());
        $bill = $this->makeBillWithDetails(['Producto A']);
        $company = (object) ['type_bill' => '2'];
        $range = (object) ['resolution_number' => null];

        $normalPdf->expects($this->once())
            ->method('setFooter')
            ->with('{PAGENO}');

        $normalPdf->expects($this->once())
            ->method('SetHTMLFooter')
            ->with($this->isType('string'));

        $normalPdf->expects($this->once())
            ->method('WriteHTML')
            ->with(
                $this->isType('string'),
                HTMLParserMode::HTML_BODY
            );

        $pdf = $controller->buildPdfByTypePublic($company, $bill, $range);

        $this->assertSame($normalPdf, $pdf);
    }

    public function test_missing_type_bill_falls_back_to_ticket_template(): void
    {
        $ticketPdf = $this->mockMpdf();
        $controller = new FakeBillController($this->mockMpdf(), $ticketPdf);
        $bill = $this->makeBillWithDetails(['Producto A']);
        $company = (object) [];
        $range = (object) ['resolution_number' => null];

        $ticketPdf->expects($this->once())
            ->method('SetHTMLFooter')
            ->with($this->callback(fn ($view) => $this->viewName($view) === 'pdf.ticket.footer'));

        $ticketPdf->expects($this->once())
            ->method('WriteHTML')
            ->with(
                $this->callback(fn ($view) => $this->viewName($view) === 'pdf.ticket.template'),
                HTMLParserMode::HTML_BODY
            );

        $pdf = $controller->buildPdfByTypePublic($company, $bill, $range);

        $this->assertSame($ticketPdf, $pdf);
    }

    private function mockMpdf(): Mpdf
    {
        return $this->getMockBuilder(Mpdf::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setFooter', 'SetHTMLFooter', 'WriteHTML'])
            ->getMock();
    }

    private function makeBillWithDetails(array $names): Bill
    {
        $bill = new Bill();
        $details = collect($names)->map(static fn (string $name) => (object) ['name' => $name]);
        $bill->setRelation('details', $details);

        return $bill;
    }

    private function viewName($view): ?string
    {
        if (!is_object($view)) {
            return null;
        }

        if (method_exists($view, 'name')) {
            return $view->name();
        }

        if (method_exists($view, 'getName')) {
            return $view->getName();
        }

        return null;
    }

    private function viewHasDataKey($view, string $key): bool
    {
        if (!is_object($view) || !method_exists($view, 'getData')) {
            return false;
        }

        $data = $view->getData();

        return array_key_exists($key, $data);
    }
}

class FakeBillController extends BillController
{
    public function __construct(
        private Mpdf $normalPdf,
        private Mpdf $ticketPdf
    ) {
    }

    public ?int $receivedTicketHeight = null;

    public function buildPdfByTypePublic($company, Bill $bill, $range): Mpdf
    {
        return $this->buildPdfByType($company, $bill, $range);
    }

    public function initMPdf(): Mpdf
    {
        return $this->normalPdf;
    }

    protected function initMPdfTicket($height): Mpdf
    {
        $this->receivedTicketHeight = (int) $height;

        return $this->ticketPdf;
    }
}
