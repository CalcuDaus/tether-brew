<?php

namespace App\Imports;

use App\Models\Journal;
use App\Models\JournalCategory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class JournalsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $categories = JournalCategory::all()->pluck('id', 'name')->mapWithKeys(function ($item, $key) {
            return [strtolower(trim($key)) => $item];
        });

        // "Lain-lain" category as fallback
        $lainLainCategoryId = null;
        $lainLainCat = JournalCategory::where('name', 'like', '%lain%')->first();
        if ($lainLainCat) {
            $lainLainCategoryId = $lainLainCat->id;
        } else {
            // Create if it doesn't exist
            $newCat = JournalCategory::create(['name' => 'Lain-lain']);
            $lainLainCategoryId = $newCat->id;
        }

        foreach ($rows as $row) {
            // Skip empty rows
            if (!isset($row['tanggal']) && !isset($row['keterangan'])) {
                continue;
            }

            // Parse Date
            $date = null;
            if (isset($row['tanggal'])) {
                try {
                    // Handle Excel serial date or string date
                    if (is_numeric($row['tanggal'])) {
                        $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal'])->format('Y-m-d');
                    } else {
                        $date = Carbon::parse($row['tanggal'])->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    $date = now()->format('Y-m-d');
                }
            } else {
                $date = now()->format('Y-m-d');
            }

            // Parse Debit/Kredit
            $debit = isset($row['debit']) ? floatval(preg_replace('/[^0-9]/', '', $row['debit'])) : 0;
            $kredit = isset($row['kredit']) ? floatval(preg_replace('/[^0-9]/', '', $row['kredit'])) : 0;

            $type = 'debit';
            $amount = 0;

            if ($debit > 0) {
                $type = 'debit';
                $amount = $debit;
            } elseif ($kredit > 0) {
                $type = 'credit';
                $amount = $kredit;
            } else {
                continue; // Skip if both 0 or empty
            }

            // Category Matching (Referensi)
            $categoryId = $lainLainCategoryId;
            if (isset($row['referensi']) && trim($row['referensi']) !== '') {
                $refName = strtolower(trim($row['referensi']));
                if (isset($categories[$refName])) {
                    $categoryId = $categories[$refName];
                }
            }

            $description = $row['keterangan'] ?? '-';

            Journal::create([
                'date' => $date,
                'description' => $description,
                'type' => $type,
                'amount' => $amount,
                'journal_category_id' => $categoryId,
                'branch_id' => activeBranchId(),
                'created_by' => auth()->id() ?? 1, // Fallback to 1 if testing via console etc
            ]);
        }
    }
}
