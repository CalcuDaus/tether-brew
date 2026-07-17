<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Color;

class RevenueReportExport implements FromArray, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    protected array $data;
    protected int $dataStartRow;
    protected int $dataEndRow;
    protected int $totalRows;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Laporan Omset';
    }

    public function array(): array
    {
        $rows = [];

        // === HEADER SECTION ===
        // Row 1: Company name
        $rows[] = ['TETHER BREW', '', '', '', '', '', '', '', ''];
        // Row 2: Management System
        $rows[] = ['Management System', '', '', '', '', '', '', '', ''];
        // Row 3: Empty
        $rows[] = ['', '', '', '', '', '', '', '', ''];
        // Row 4: Report title
        $rows[] = ['LAPORAN OMSET', '', '', '', '', '', '', '', ''];
        // Row 5: Period
        $rows[] = ['Periode: ' . ($this->data['periodLabel'] ?? '-'), '', '', '', '', '', '', '', ''];
        // Row 6: Status filter
        $statusLabel = match ($this->data['confirmationStatus'] ?? null) {
            'confirmed' => 'Sudah Dikonfirmasi',
            'unconfirmed' => 'Belum Dikonfirmasi',
            default => 'Semua Data',
        };
        $rows[] = ['Status: ' . $statusLabel, '', '', '', '', '', '', '', ''];
        // Row 7: Empty
        $rows[] = ['', '', '', '', '', '', '', '', ''];

        // === SUMMARY SECTION ===
        $summary = $this->data['summary'] ?? null;
        if ($summary) {
            // Row 8: Summary title
            $rows[] = ['RINGKASAN', '', '', '', '', '', '', '', ''];
            // Row 9-14: Summary data
            $rows[] = ['', 'Total Omset', '', $summary['total_omset'] ?? 0, '', 'Jumlah Rider', '', $summary['rider_count'] ?? 0, ''];
            $rows[] = ['', 'Setoran Cash (Fisik)', '', $summary['total_actual_setor'] ?? 0, '', 'Total Cup', '', ($summary['total_cups'] ?? 0) . ' Pcs', ''];
            $rows[] = ['', 'Total QRIS', '', $summary['total_qris'] ?? 0, '', '', '', '', ''];
            $rows[] = ['', 'Total Minus', '', $summary['total_minus'] ?? 0, '', '', '', '', ''];
            // Row 13: Empty
            $rows[] = ['', '', '', '', '', '', '', '', ''];
        }

        // === DATA TABLE SECTION ===
        $this->dataStartRow = count($rows) + 1;

        // Table header
        $rows[] = ['No', 'Tanggal', 'Rider', 'Cash (Target)', 'Cash (Setor Fisik)', 'QRIS', 'Total Setoran', 'Total (Omset)', 'Total Cups', 'Admin Pemeriksa', 'Status'];

        $sales = $this->data['sales'] ?? collect();
        $confirmedDatesMap = $this->data['confirmedDatesMap'] ?? [];
        $no = 1;

        $grandCash = 0;
        $grandActualSetor = 0;
        $grandQris = 0;
        $grandSetoran = 0;
        $grandTotal = 0;
        $grandCups = 0;

        foreach ($sales as $sale) {
            $dateStr = $sale->date->format('Y-m-d');
            $isConfirmed = isset($confirmedDatesMap[$dateStr]);

            $rows[] = [
                $no++,
                $sale->date->format('d/m/Y'),
                $sale->rider->name ?? '-',
                $sale->cash_amount,
                $sale->actual_setor,
                $sale->qris_amount,
                $sale->total_setoran,
                $sale->total_gross_income,
                ($sale->total_cups ?? 0) . ' Pcs',
                $sale->admin_pemeriksa ?? ($sale->admin->name ?? '-'),
                $isConfirmed ? '✓ Dikonfirmasi' : '○ Belum',
            ];

            $grandCash += $sale->cash_amount;
            $grandActualSetor += $sale->actual_setor;
            $grandQris += $sale->qris_amount;
            $grandSetoran += $sale->total_setoran;
            $grandTotal += $sale->total_gross_income;
            $grandCups += $sale->total_cups ?? 0;
        }

        // Grand total row
        $rows[] = [
            '', '', 'GRAND TOTAL',
            $grandCash,
            $grandActualSetor,
            $grandQris,
            $grandSetoran,
            $grandTotal,
            $grandCups . ' Pcs',
            '', '',
        ];

        $this->dataEndRow = count($rows);
        $this->totalRows = count($rows);

        // Footer
        $rows[] = ['', '', '', '', '', '', '', '', ''];
        $rows[] = ['Dicetak pada: ' . now()->translatedFormat('d F Y, H:i') . ' WIB', '', '', '', '', '', '', '', ''];

        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 15,
            'C' => 22,
            'D' => 18,
            'E' => 20,
            'F' => 18,
            'G' => 18,
            'H' => 18,
            'I' => 13,
            'J' => 20,
            'K' => 18,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastCol = 'K';

                // === BRANDING HEADER ===
                // Row 1: Company name
                $sheet->mergeCells("A1:{$lastCol}1");
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 20,
                        'color' => ['rgb' => '22c55e'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Row 2: Subtitle
                $sheet->mergeCells("A2:{$lastCol}2");
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => [
                        'size' => 11,
                        'color' => ['rgb' => '64748b'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                // Row 4: Report title
                $sheet->mergeCells("A4:{$lastCol}4");
                $sheet->getStyle('A4')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                        'color' => ['rgb' => '1e293b'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                // Row 5: Period
                $sheet->mergeCells("A5:{$lastCol}5");
                $sheet->getStyle('A5')->applyFromArray([
                    'font' => [
                        'size' => 11,
                        'color' => ['rgb' => '475569'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                // Row 6: Status
                $sheet->mergeCells("A6:{$lastCol}6");
                $sheet->getStyle('A6')->applyFromArray([
                    'font' => [
                        'size' => 10,
                        'italic' => true,
                        'color' => ['rgb' => '64748b'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                // === SUMMARY SECTION ===
                $summary = $this->data['summary'] ?? null;
                if ($summary) {
                    // Row 8: Summary title
                    $sheet->mergeCells("A8:{$lastCol}8");
                    $sheet->getStyle('A8')->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 12,
                            'color' => ['rgb' => '1e293b'],
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'f0fdf4'],
                        ],
                        'borders' => [
                            'bottom' => [
                                'borderStyle' => Border::BORDER_MEDIUM,
                                'color' => ['rgb' => '22c55e'],
                            ],
                        ],
                    ]);

                    // Summary data styling (rows 9-12)
                    for ($row = 9; $row <= 12; $row++) {
                        $sheet->getStyle("B{$row}")->applyFromArray([
                            'font' => ['color' => ['rgb' => '475569'], 'size' => 10],
                        ]);
                        $sheet->getStyle("D{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1e293b']],
                            'numberFormat' => ['formatCode' => '#,##0'],
                        ]);
                        $sheet->getStyle("F{$row}")->applyFromArray([
                            'font' => ['color' => ['rgb' => '475569'], 'size' => 10],
                        ]);
                        $sheet->getStyle("H{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1e293b']],
                        ]);
                    }

                    // Color-code specific summary values
                    // Total Omset - green
                    $sheet->getStyle('D9')->getFont()->setColor(new Color('22c55e'));
                    // Total QRIS - blue
                    $sheet->getStyle('D11')->getFont()->setColor(new Color('3b82f6'));
                    // Total Minus - red
                    $sheet->getStyle('D12')->getFont()->setColor(new Color('ef4444'));

                    // Summary border box
                    $sheet->getStyle("A8:{$lastCol}12")->applyFromArray([
                        'borders' => [
                            'outline' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'e2e8f0'],
                            ],
                        ],
                    ]);
                }

                // === DATA TABLE STYLING ===
                $headerRow = $this->dataStartRow;
                $headerRange = "A{$headerRow}:{$lastCol}{$headerRow}";

                // Table header style
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 10,
                        'color' => ['rgb' => 'ffffff'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '22c55e'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '16a34a'],
                        ],
                    ],
                ]);

                // Data rows styling
                $dataFirstRow = $headerRow + 1;
                $dataLastRow = $this->dataEndRow - 1; // Exclude grand total row
                $grandTotalRow = $this->dataEndRow;

                if ($dataFirstRow <= $dataLastRow) {
                    $dataRange = "A{$dataFirstRow}:{$lastCol}{$dataLastRow}";

                    // All data borders
                    $sheet->getStyle($dataRange)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'e2e8f0'],
                            ],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                        'font' => [
                            'size' => 10,
                        ],
                    ]);

                    // Alternating row colors
                    for ($row = $dataFirstRow; $row <= $dataLastRow; $row++) {
                        if (($row - $dataFirstRow) % 2 === 1) {
                            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray([
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => 'f8fafc'],
                                ],
                            ]);
                        }
                    }

                    // Number columns alignment (right) & Rupiah format
                    $numberCols = ['D', 'E', 'F', 'G', 'H'];
                    foreach ($numberCols as $col) {
                        $sheet->getStyle("{$col}{$dataFirstRow}:{$col}{$dataLastRow}")->applyFromArray([
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                            'numberFormat' => ['formatCode' => '#,##0'],
                        ]);
                    }

                    // Center columns
                    $centerCols = ['A', 'I'];
                    foreach ($centerCols as $col) {
                        $sheet->getStyle("{$col}{$dataFirstRow}:{$col}{$dataLastRow}")->applyFromArray([
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        ]);
                    }

                    // Status column styling
                    for ($row = $dataFirstRow; $row <= $dataLastRow; $row++) {
                        $statusVal = $sheet->getCell("K{$row}")->getValue();
                        if (str_contains($statusVal ?? '', '✓')) {
                            $sheet->getStyle("K{$row}")->applyFromArray([
                                'font' => ['color' => ['rgb' => '16a34a'], 'bold' => true, 'size' => 9],
                            ]);
                        } else {
                            $sheet->getStyle("K{$row}")->applyFromArray([
                                'font' => ['color' => ['rgb' => 'f59e0b'], 'size' => 9],
                            ]);
                        }
                    }
                }

                // === GRAND TOTAL ROW ===
                $sheet->getStyle("A{$grandTotalRow}:{$lastCol}{$grandTotalRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                        'color' => ['rgb' => '1e293b'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'dcfce7'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '22c55e'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Grand total number format
                foreach (['D', 'E', 'F', 'G', 'H'] as $col) {
                    $sheet->getStyle("{$col}{$grandTotalRow}")->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                        'numberFormat' => ['formatCode' => '#,##0'],
                    ]);
                }
                $sheet->getStyle("I{$grandTotalRow}")->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // === FOOTER ===
                $footerRow = $this->totalRows + 2;
                $sheet->mergeCells("A{$footerRow}:{$lastCol}{$footerRow}");
                $sheet->getStyle("A{$footerRow}")->applyFromArray([
                    'font' => [
                        'italic' => true,
                        'size' => 9,
                        'color' => ['rgb' => '94a3b8'],
                    ],
                ]);

                // === PRINT SETTINGS ===
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
                $sheet->getPageMargins()->setTop(0.5);
                $sheet->getPageMargins()->setRight(0.5);
                $sheet->getPageMargins()->setBottom(0.5);
                $sheet->getPageMargins()->setLeft(0.5);

                // Freeze pane below table header
                $sheet->freezePane("A" . ($headerRow + 1));
            },
        ];
    }
}
