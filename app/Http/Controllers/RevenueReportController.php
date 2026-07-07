<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RiderDailySale;
use App\Models\RiderDailySaleItem;
use App\Models\RiderSalesJournalConfirmation;
use App\Models\Product;
use Carbon\Carbon;

class RevenueReportController extends Controller
{
    /**
     * Main index page: summary cards + data table
     */
    public function index(Request $request)
    {
        $branchId = activeBranchId();

        // Filter type: daily (default), range, monthly
        $filterType = $request->get('filter_type', 'daily');
        $filterDate = $request->get('date', today()->format('Y-m-d'));
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $confirmationStatus = $request->get('status'); // 'confirmed', 'unconfirmed', or null (all)

        // Build date range
        $startDate = null;
        $endDate = null;
        $periodLabel = '';

        switch ($filterType) {
            case 'range':
                if ($dateFrom && $dateTo) {
                    $startDate = Carbon::parse($dateFrom)->startOfDay();
                    $endDate = Carbon::parse($dateTo)->endOfDay();
                    $periodLabel = Carbon::parse($dateFrom)->translatedFormat('d F Y') . ' - ' . Carbon::parse($dateTo)->translatedFormat('d F Y');
                }
                break;
            case 'monthly':
                $startDate = Carbon::create($year, $month, 1)->startOfMonth()->startOfDay();
                $endDate = $startDate->copy()->endOfMonth()->endOfDay();
                $periodLabel = $startDate->translatedFormat('F Y');
                break;
            case 'daily':
            default:
                if ($filterDate) {
                    $startDate = Carbon::parse($filterDate)->startOfDay();
                    $endDate = Carbon::parse($filterDate)->endOfDay();
                    $periodLabel = Carbon::parse($filterDate)->translatedFormat('d F Y');
                }
                break;
        }

        $sales = collect();
        $summary = null;

        if ($startDate && $endDate) {
            $query = RiderDailySale::forBranch($branchId)
                ->with(['rider', 'admin'])
                ->withSum('items as total_cups', 'stock_sold')
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->orderBy('date', 'desc');

            // Filter by confirmation status
            if ($confirmationStatus === 'confirmed') {
                $confirmedDates = RiderSalesJournalConfirmation::forBranch($branchId)
                    ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->pluck('date')
                    ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
                    ->toArray();
                $query->whereIn(\Illuminate\Support\Facades\DB::raw('DATE(date)'), $confirmedDates);
            } elseif ($confirmationStatus === 'unconfirmed') {
                $confirmedDates = RiderSalesJournalConfirmation::forBranch($branchId)
                    ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->pluck('date')
                    ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
                    ->toArray();
                if (!empty($confirmedDates)) {
                    $query->whereNotIn(\Illuminate\Support\Facades\DB::raw('DATE(date)'), $confirmedDates);
                }
            }

            $sales = $query->paginate(20)->withQueryString();

            // Build summary from the same filters (without pagination)
            $summaryQuery = RiderDailySale::forBranch($branchId)
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

            if ($confirmationStatus === 'confirmed' && !empty($confirmedDates)) {
                $summaryQuery->whereIn(\Illuminate\Support\Facades\DB::raw('DATE(date)'), $confirmedDates);
            } elseif ($confirmationStatus === 'unconfirmed') {
                $confirmedDatesForSummary = RiderSalesJournalConfirmation::forBranch($branchId)
                    ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->pluck('date')
                    ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
                    ->toArray();
                if (!empty($confirmedDatesForSummary)) {
                    $summaryQuery->whereNotIn(\Illuminate\Support\Facades\DB::raw('DATE(date)'), $confirmedDatesForSummary);
                }
            }

            $totalCups = RiderDailySaleItem::whereHas('sale', function($q) use ($branchId, $startDate, $endDate, $confirmationStatus) {
                $q->forBranch($branchId)->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
                if ($confirmationStatus === 'confirmed') {
                    $confirmedDates = RiderSalesJournalConfirmation::forBranch($branchId)
                        ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                        ->pluck('date')
                        ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
                        ->toArray();
                    $q->whereIn(\Illuminate\Support\Facades\DB::raw('DATE(date)'), $confirmedDates);
                } elseif ($confirmationStatus === 'unconfirmed') {
                    $confirmedDates = RiderSalesJournalConfirmation::forBranch($branchId)
                        ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                        ->pluck('date')
                        ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
                        ->toArray();
                    if (!empty($confirmedDates)) {
                        $q->whereNotIn(\Illuminate\Support\Facades\DB::raw('DATE(date)'), $confirmedDates);
                    }
                }
            })->sum('stock_sold');

            $summary = [
                'total_omset'       => $summaryQuery->sum('total_gross_income'),
                'total_cash'        => $summaryQuery->sum('cash_amount'),
                'total_actual_setor'=> $summaryQuery->sum('actual_setor'),
                'total_qris'        => $summaryQuery->sum('qris_amount'),
                'total_minus'       => $summaryQuery->sum('minus_amount'),
                'total_cups'        => $totalCups,
                'rider_count'       => $summaryQuery->distinct('rider_id')->count('rider_id'),
            ];
        }

        // Get confirmed dates in the period (for badge display per row)
        $confirmedDatesMap = [];
        if ($startDate && $endDate) {
            $confirmedDatesMap = RiderSalesJournalConfirmation::forBranch($branchId)
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->pluck('date')
                ->mapWithKeys(fn($d) => [Carbon::parse($d)->format('Y-m-d') => true])
                ->toArray();
        }

        return view('admin.revenue_report.index', compact(
            'sales', 'summary', 'filterType', 'filterDate', 'dateFrom', 'dateTo',
            'month', 'year', 'periodLabel', 'confirmationStatus', 'confirmedDatesMap'
        ));
    }

    /**
     * Export to PDF via DomPDF
     */
    public function exportPdf(Request $request)
    {
        $data = $this->getExportData($request);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.revenue_report.pdf', $data);
        $pdf->setPaper('A4', 'landscape');

        $filename = 'Laporan_Omset_' . $data['periodLabel'] . '.pdf';
        $filename = str_replace(['/', ' ', ','], ['_', '_', ''], $filename);

        return $pdf->download($filename);
    }

    /**
     * Export to Excel via Maatwebsite
     */
    public function exportExcel(Request $request)
    {
        $data = $this->getExportData($request);

        $filename = 'Laporan_Omset_' . $data['periodLabel'] . '.xlsx';
        $filename = str_replace(['/', ' ', ','], ['_', '_', ''], $filename);

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\RevenueReportExport($data),
            $filename
        );
    }

    /**
     * Helper: get export data (shared between PDF and Excel)
     */
    private function getExportData(Request $request): array
    {
        $branchId = activeBranchId();
        $filterType = $request->get('filter_type', 'daily');
        $filterDate = $request->get('date', today()->format('Y-m-d'));
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $confirmationStatus = $request->get('status');

        $startDate = null;
        $endDate = null;
        $periodLabel = '';

        switch ($filterType) {
            case 'range':
                if ($dateFrom && $dateTo) {
                    $startDate = Carbon::parse($dateFrom)->startOfDay();
                    $endDate = Carbon::parse($dateTo)->endOfDay();
                    $periodLabel = Carbon::parse($dateFrom)->translatedFormat('d F Y') . ' - ' . Carbon::parse($dateTo)->translatedFormat('d F Y');
                }
                break;
            case 'monthly':
                $startDate = Carbon::create($year, $month, 1)->startOfMonth()->startOfDay();
                $endDate = $startDate->copy()->endOfMonth()->endOfDay();
                $periodLabel = $startDate->translatedFormat('F Y');
                break;
            case 'daily':
            default:
                if ($filterDate) {
                    $startDate = Carbon::parse($filterDate)->startOfDay();
                    $endDate = Carbon::parse($filterDate)->endOfDay();
                    $periodLabel = Carbon::parse($filterDate)->translatedFormat('d F Y');
                }
                break;
        }

        $sales = collect();
        $summary = null;

        if ($startDate && $endDate) {
            $query = RiderDailySale::forBranch($branchId)
                ->with(['rider', 'admin'])
                ->withSum('items as total_cups', 'stock_sold')
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->orderBy('date', 'asc');

            // Filter by confirmation status
            if ($confirmationStatus === 'confirmed' || $confirmationStatus === 'unconfirmed') {
                $confirmedDates = RiderSalesJournalConfirmation::forBranch($branchId)
                    ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                    ->pluck('date')
                    ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
                    ->toArray();

                if ($confirmationStatus === 'confirmed') {
                    $query->whereIn(\Illuminate\Support\Facades\DB::raw('DATE(date)'), $confirmedDates);
                } else {
                    if (!empty($confirmedDates)) {
                        $query->whereNotIn(\Illuminate\Support\Facades\DB::raw('DATE(date)'), $confirmedDates);
                    }
                }
            }

            $sales = $query->get();

            $totalCups = $sales->sum('total_cups');

            $summary = [
                'total_omset'        => $sales->sum('total_gross_income'),
                'total_cash'         => $sales->sum('cash_amount'),
                'total_actual_setor' => $sales->sum('actual_setor'),
                'total_qris'         => $sales->sum('qris_amount'),
                'total_minus'        => $sales->sum('minus_amount'),
                'total_cups'         => $totalCups,
                'rider_count'        => $sales->unique('rider_id')->count(),
            ];
        }

        // Confirmed dates map
        $confirmedDatesMap = [];
        if ($startDate && $endDate) {
            $confirmedDatesMap = RiderSalesJournalConfirmation::forBranch($branchId)
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->pluck('date')
                ->mapWithKeys(fn($d) => [Carbon::parse($d)->format('Y-m-d') => true])
                ->toArray();
        }

        return compact('sales', 'summary', 'periodLabel', 'confirmedDatesMap', 'confirmationStatus');
    }
}
