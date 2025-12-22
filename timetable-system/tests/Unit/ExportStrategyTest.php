<?php

use PHPUnit\Framework\TestCase;

class ExportStrategyTest extends TestCase
{
    public function testCSVExportInstantiation()
    {
        $strategy = new CSVExportStrategy();
        $this->assertInstanceOf(CSVExportStrategy::class, $strategy);
    }

    public function testPDFExportInstantiation()
    {
        // Mock the controller as it's required by PDFExportStrategy
        $controller = $this->getMockBuilder(BaseController::class)
                           ->disableOriginalConstructor()
                           ->getMock();
                           
        $strategy = new PDFExportStrategy($controller);
        $this->assertInstanceOf(PDFExportStrategy::class, $strategy);
    }
}
