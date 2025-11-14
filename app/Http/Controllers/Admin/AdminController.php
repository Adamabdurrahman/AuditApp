<?php

namespace App\Http\Controllers\Admin;
use Exception;
use App\Models\Status;
use App\Models\Kategori;
use App\Models\Priority;
use App\Models\Reminder;
use App\Models\AuditForm;
use App\Models\Subkategori;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\Fileattachment;
use App\Models\Findlossdetail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use App\Models\NotificationType;
use App\Models\AuditPlan;
use App\Models\FindlossRecovery;
use App\Mail\AuditNotificationMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Models\User;


class AdminController extends Controller
{
        // Fungsi untuk menampilkan dasbor admin
    public function dashboard(Request $request)
    {
        $planFindingStats = AuditPlan::query()
            ->withCount(['findings as findings_with_plan_count' => function ($query) {
                $query->whereNotNull('audit_plan_id');
            }])
            ->get();

        $engagedPlanIds = $planFindingStats
            ->where('findings_with_plan_count', '>', 0)
            ->pluck('id');

        $totalPertemuan = $planFindingStats->sum('findings_with_plan_count');

        $totalEngagement = $engagedPlanIds->count();

        $planIdFilter = $engagedPlanIds->isEmpty() ? [-1] : $engagedPlanIds->all();

        $auditFormsWithPlan = AuditForm::query()
            ->whereIn('audit_plan_id', $planIdFilter)
            ->whereHas('auditPlan');

        $auditeeCount = Reminder::whereNotNull('email')
            ->whereHas('auditForms', function ($query) use ($planIdFilter) {
                $query->whereIn('audit_plan_id', $planIdFilter)
                    ->whereHas('auditPlan');
            })
            ->distinct('email')
            ->count('email');

        $teamMemberCount = User::whereIn('role_id', [1, 2])
            ->whereHas('auditedForms', function ($query) use ($planIdFilter) {
                $query->whereIn('audit_plan_id', $planIdFilter)
                    ->whereHas('auditPlan');
            })
            ->distinct()
            ->count('id');

        $availableYears = AuditPlan::query()
            ->whereNotNull('year')
            ->pluck('year')
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();

        $finLossBaseQuery = AuditForm::query()
            ->whereIn('audit_plan_id', $planIdFilter)
            ->whereHas('auditPlan')
            ->whereHas('kategori', fn ($q) => $q->where('name', 'Fin Loss'))
            ->whereHas('subkategori', fn ($q) => $q->where('name', 'Recovery'));

        $finLossYears = $finLossBaseQuery->get()
            ->map(function ($form) {
                $date = $form->tanggal_temuan ?? $form->start_date ?? $form->created_at;
                if (!$date) {
                    return null;
                }

                try {
                    return Carbon::parse($date)->year;
                } catch (\Throwable $e) {
                    return null;
                }
            })
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();

        $filterYears = $availableYears->concat($finLossYears)->unique()->sortDesc()->values();
        if ($filterYears->isEmpty()) {
            $filterYears = collect([now()->year]);
        }

        $selectedYear = (int) ($request->input('year') ?: $filterYears->first());
        if (!$filterYears->contains($selectedYear)) {
            $selectedYear = (int) $filterYears->first();
        }

        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');

        $startDate = null;
        $endDate = null;

        if ($startDateInput) {
            try {
                $startDate = Carbon::parse($startDateInput)->startOfDay();
            } catch (\Throwable $e) {
                $startDate = null;
            }
        }

        if ($endDateInput) {
            try {
                $endDate = Carbon::parse($endDateInput)->endOfDay();
            } catch (\Throwable $e) {
                $endDate = null;
            }
        }

        if ($startDate && $endDate && $startDate->greaterThan($endDate)) {
            [$startDate, $endDate] = [$endDate->copy()->startOfDay(), $startDate->copy()->endOfDay()];
        }

        $applyDateRange = function ($query) use ($startDate, $endDate) {
            if ($startDate) {
                $query->where(function ($fallback) use ($startDate) {
                    $fallback->whereNotNull('tanggal_temuan')
                        ->whereDate('tanggal_temuan', '>=', $startDate)
                        ->orWhere(function ($alt) use ($startDate) {
                            $alt->whereNull('tanggal_temuan')
                                ->whereNotNull('start_date')
                                ->whereDate('start_date', '>=', $startDate);
                        });
                });
            }

            if ($endDate) {
                $query->where(function ($fallback) use ($endDate) {
                    $fallback->whereNotNull('tanggal_temuan')
                        ->whereDate('tanggal_temuan', '<=', $endDate)
                        ->orWhere(function ($alt) use ($endDate) {
                            $alt->whereNull('tanggal_temuan')
                                ->whereNotNull('start_date')
                                ->whereDate('start_date', '<=', $endDate);
                        });
                });
            }
        };

        $plans = AuditPlan::query()
            ->withCount(['findings as findings_count' => function ($query) use ($applyDateRange) {
                $applyDateRange($query);
            }])
            ->when($selectedYear, fn ($query) => $query->where('year', $selectedYear))
            ->orderBy('title')
            ->get();

        $chartData = [
            'labels' => $plans->pluck('title')->values()->all(),
            'values' => $plans->pluck('findings_count')->values()->all(),
            'years' => $filterYears,
        ];

        $statusCountsRaw = (clone $auditFormsWithPlan)
            ->select('status_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('status_id')
            ->when($selectedYear, function ($query) use ($selectedYear) {
                $query->where(function ($inner) use ($selectedYear) {
                    $inner->whereYear('tanggal_temuan', $selectedYear)
                        ->orWhere(function ($fallback) use ($selectedYear) {
                            $fallback->whereNull('tanggal_temuan')
                                ->whereYear('start_date', $selectedYear);
                        });
                });
            })
            ->when($startDate || $endDate, function ($query) use ($applyDateRange) {
                $applyDateRange($query);
            })
            ->groupBy('status_id')
            ->get();

        $statusNames = Status::whereIn('id', $statusCountsRaw->pluck('status_id')->filter())
            ->pluck('status', 'id');

        $statusOrder = ['Open', 'Closed', 'Overdue'];

        $statusCounts = $statusCountsRaw->mapWithKeys(function ($row) use ($statusNames) {
            $name = $statusNames[$row->status_id] ?? null;
            if (!$name) {
                return [];
            }

            return [$name => (int) $row->total];
        });

        $statusLabels = [];
        $statusValues = [];

        foreach ($statusOrder as $statusName) {
            if (isset($statusCounts[$statusName])) {
                $statusLabels[] = $statusName;
                $statusValues[] = $statusCounts[$statusName];
            }
        }

        foreach ($statusCounts as $name => $value) {
            if (!in_array($name, $statusOrder, true)) {
                $statusLabels[] = $name;
                $statusValues[] = $value;
            }
        }

        $totalStatusCount = array_sum($statusValues);
        $statusSummary = [];

        if ($totalStatusCount > 0) {
            foreach ($statusLabels as $index => $label) {
                $value = $statusValues[$index] ?? 0;
                $percentage = $totalStatusCount > 0 ? round(($value / $totalStatusCount) * 100, 1) : 0;

                $statusSummary[] = [
                    'label' => $label,
                    'value' => $value,
                    'percentage' => $percentage,
                ];
            }
        }

        $statusPieData = [
            'labels' => $statusLabels,
            'values' => $statusValues,
        ];

        $resolveDate = function (...$candidates) {
            foreach ($candidates as $candidate) {
                if ($candidate instanceof Carbon) {
                    return $candidate->copy();
                }

                if ($candidate) {
                    try {
                        return Carbon::parse($candidate);
                    } catch (\Throwable $e) {
                        continue;
                    }
                }
            }

            return null;
        };

        $forms = $finLossBaseQuery
            ->with(['findlossdetails.recoveries', 'priority'])
            ->get();

        $sequences = collect();

        foreach ($forms as $form) {
            foreach ($form->findlossdetails as $detail) {
                $agreedDate = $resolveDate(
                    $form->tanggal_temuan,
                    $form->start_date,
                    $form->created_at
                );

                $agreedAmountIdr = (float) ($detail->nilai ?? 0);
                $agreedAmountUsd = (float) ($detail->nilai_usd ?? 0);

                if (!$agreedDate || ($agreedAmountIdr <= 0 && $agreedAmountUsd <= 0)) {
                    continue;
                }

                $sequence = collect();

                $sequence->push([
                    'type' => 'agreed',
                    'date' => $agreedDate,
                    'amount_idr' => $agreedAmountIdr,
                    'amount_usd' => $agreedAmountUsd,
                    'label' => $agreedDate->format('d M Y') . ' • Temuan: ' . ($detail->item ?? 'Tanpa item'),
                ]);

                $detail->recoveries
                    ->sortBy(fn ($recovery) => $resolveDate($recovery->recorded_at, $recovery->created_at, $form->created_at))
                    ->each(function ($recovery) use ($sequence, $resolveDate, $form, $detail) {
                        $recoveryDate = $resolveDate(
                            $recovery->recorded_at,
                            $recovery->created_at,
                            $form->created_at
                        );

                        $amountIdr = (float) ($recovery->amount ?? 0);
                        $amountUsd = (float) ($recovery->amount_usd ?? 0);

                        if ($recoveryDate && ($amountIdr > 0 || $amountUsd > 0)) {
                            $sequence->push([
                                'type' => 'paid',
                                'date' => $recoveryDate,
                                'amount_idr' => $amountIdr,
                                'amount_usd' => $amountUsd,
                                'label' => $recoveryDate->format('d M Y') . ' • Recovery: ' . ($recovery->notes ?: ($detail->item ?? 'Tanpa item')),
                            ]);
                        }
                    });

                $sequences->push($sequence);
            }
        }

        $events = $sequences
            ->flatMap(fn ($sequence) => $sequence
                ->sortBy(fn ($event) => $event['date']->timestamp)
                ->values()
            )
            ->filter(fn ($event) => $event['date'] instanceof Carbon && ((float) $event['amount_idr'] > 0 || (float) $event['amount_usd'] > 0))
            ->values();

        $eventDates = $events->pluck('date')
            ->filter(fn ($date) => $date instanceof Carbon)
            ->sortBy(fn ($date) => $date->timestamp)
            ->values();

        $currentAgreedIdr = 0.0;
        $currentPaidIdr = 0.0;
        $currentAgreedUsd = 0.0;
        $currentPaidUsd = 0.0;

        $runningOutstandingIdr = 0.0;
        $runningOutstandingUsd = 0.0;

        $labels = [];
        $agreedSeriesIdr = [];
        $paidSeriesIdr = [];
        $remainingSeriesIdr = [];
        $agreedSeriesUsd = [];
        $paidSeriesUsd = [];
        $remainingSeriesUsd = [];

        $chartStart = $startDate;
        $chartEnd = $endDate;

        if ($events->isNotEmpty()) {
            $firstEventDate = $eventDates->first();
            $lastEventDate = $eventDates->last();

            if (!$chartStart || $chartStart->greaterThan($firstEventDate)) {
                $chartStart = $firstEventDate->copy();
            }

            if (!$chartEnd || $chartEnd->lessThan($lastEventDate)) {
                $chartEnd = $lastEventDate->copy();
            }

            foreach ($events as $event) {
                $amountIdr = (float) ($event['amount_idr'] ?? 0);
                $amountUsd = (float) ($event['amount_usd'] ?? 0);

                if ($chartStart && $event['date']->lt($chartStart)) {
                    if ($event['type'] === 'agreed') {
                        $currentAgreedIdr += $amountIdr;
                        $currentAgreedUsd += $amountUsd;
                        $runningOutstandingIdr += $amountIdr;
                        $runningOutstandingUsd += $amountUsd;
                    } else {
                        $currentPaidIdr += $amountIdr;
                        $currentPaidUsd += $amountUsd;
                        $runningOutstandingIdr = max($runningOutstandingIdr - $amountIdr, 0);
                        $runningOutstandingUsd = max($runningOutstandingUsd - $amountUsd, 0);
                    }

                    continue;
                }

                if ($chartEnd && $event['date']->greaterThan($chartEnd)) {
                    break;
                }

                $agreedValueIdr = 0.0;
                $paidValueIdr = 0.0;
                $agreedValueUsd = 0.0;
                $paidValueUsd = 0.0;
                $remainingValueIdr = 0.0;
                $remainingValueUsd = 0.0;

                if ($event['type'] === 'agreed') {
                    $currentAgreedIdr += $amountIdr;
                    $currentAgreedUsd += $amountUsd;

                    $agreedValueIdr = round($amountIdr, 2);
                    $agreedValueUsd = round($amountUsd, 2);

                    $runningOutstandingIdr += $amountIdr;
                    $runningOutstandingUsd += $amountUsd;
                } else {
                    $currentPaidIdr += $amountIdr;
                    $currentPaidUsd += $amountUsd;

                    $paidValueIdr = round($amountIdr, 2);
                    $paidValueUsd = round($amountUsd, 2);

                    $runningOutstandingIdr = max($runningOutstandingIdr - $amountIdr, 0);
                    $runningOutstandingUsd = max($runningOutstandingUsd - $amountUsd, 0);
                }

                $remainingValueIdr = round($runningOutstandingIdr, 2);
                $remainingValueUsd = round($runningOutstandingUsd, 2);

                $label = $event['label'] ?? $event['date']->format('d M Y');
                $labels[] = $label;
                $agreedSeriesIdr[] = $agreedValueIdr;
                $paidSeriesIdr[] = $paidValueIdr;
                $remainingSeriesIdr[] = $remainingValueIdr;
                $agreedSeriesUsd[] = $agreedValueUsd;
                $paidSeriesUsd[] = $paidValueUsd;
                $remainingSeriesUsd[] = $remainingValueUsd;
            }
        }

        $finLossChart = [
            'labels' => $labels,
            'idr' => [
                'agreed' => $agreedSeriesIdr,
                'paid' => $paidSeriesIdr,
                'remaining' => $remainingSeriesIdr,
            ],
            'usd' => [
                'agreed' => $agreedSeriesUsd,
                'paid' => $paidSeriesUsd,
                'remaining' => $remainingSeriesUsd,
            ],
        ];

        $currentRemainingIdr = max($currentAgreedIdr - $currentPaidIdr, 0);
        $currentRemainingUsd = max($currentAgreedUsd - $currentPaidUsd, 0);

        $finLossSummary = [
            'total_agreed_idr' => $currentAgreedIdr,
            'total_paid_idr' => $currentPaidIdr,
            'total_remaining_idr' => $currentRemainingIdr,
            'total_agreed_usd' => $currentAgreedUsd,
            'total_paid_usd' => $currentPaidUsd,
            'total_remaining_usd' => $currentRemainingUsd,
        ];

        $perItemRawEntries = collect();
        $perItemYears = collect();
        $perItemItems = collect();

        foreach ($forms as $form) {
            $formDate = $resolveDate(
                $form->tanggal_temuan,
                $form->start_date,
                $form->created_at
            );

            $formYear = $formDate ? $formDate->year : null;

            if ($formYear) {
                $perItemYears->push($formYear);
            }

            foreach ($form->findlossdetails as $detail) {
                $itemName = trim($detail->item ?? '') ?: 'Tanpa Item';
                $perItemItems->push($itemName);

                $perItemRawEntries->push([
                    'item' => $itemName,
                    'year' => $formYear,
                    'agreed_idr' => (float) ($detail->nilai ?? 0),
                    'agreed_usd' => (float) ($detail->nilai_usd ?? 0),
                    'recovery_idr' => (float) $detail->recoveries->sum('amount'),
                    'recovery_usd' => (float) $detail->recoveries->sum('amount_usd'),
                ]);
            }
        }

        $perItemYearOptions = $perItemYears->filter()->unique()->sortDesc()->values();
        $perItemItemOptions = $perItemItems->filter()->unique()->sort()->values();

        $perItemSelectedYear = $request->input('per_item_year');
        $perItemSelectedYear = $perItemSelectedYear !== null && $perItemSelectedYear !== ''
            ? (int) $perItemSelectedYear
            : null;

        if ($perItemSelectedYear !== null && $perItemYearOptions->isNotEmpty() && !$perItemYearOptions->contains($perItemSelectedYear)) {
            $perItemSelectedYear = (int) $perItemYearOptions->first();
        }

        $perItemSelectedItem = $request->input('per_item_category');
        if ($perItemSelectedItem === null || $perItemSelectedItem === '') {
            $perItemSelectedItem = null;
        } elseif ($perItemItemOptions->isNotEmpty() && !$perItemItemOptions->contains($perItemSelectedItem)) {
            $perItemSelectedItem = null;
        }

        $perItemAggregated = [];

        foreach ($perItemRawEntries as $entry) {
            if ($perItemSelectedYear !== null && $entry['year'] !== $perItemSelectedYear) {
                continue;
            }

            if ($perItemSelectedItem !== null && $entry['item'] !== $perItemSelectedItem) {
                continue;
            }

            $key = $entry['item'];

            if (!isset($perItemAggregated[$key])) {
                $perItemAggregated[$key] = [
                    'agreed_idr' => 0.0,
                    'agreed_usd' => 0.0,
                    'recovery_idr' => 0.0,
                    'recovery_usd' => 0.0,
                ];
            }

            $perItemAggregated[$key]['agreed_idr'] += $entry['agreed_idr'];
            $perItemAggregated[$key]['agreed_usd'] += $entry['agreed_usd'];
            $perItemAggregated[$key]['recovery_idr'] += $entry['recovery_idr'];
            $perItemAggregated[$key]['recovery_usd'] += $entry['recovery_usd'];
        }

        ksort($perItemAggregated);

        $perItemLabels = array_keys($perItemAggregated);
        $perItemAgreedIdr = [];
        $perItemRecoveryIdr = [];
        $perItemRemainingIdr = [];
        $perItemAgreedUsd = [];
        $perItemRecoveryUsd = [];
        $perItemRemainingUsd = [];

        foreach ($perItemAggregated as $itemName => $values) {
            $agreedIdr = round($values['agreed_idr'], 2);
            $recoveryIdr = round($values['recovery_idr'], 2);
            $remainingIdr = max($agreedIdr - $recoveryIdr, 0);

            $agreedUsd = round($values['agreed_usd'], 2);
            $recoveryUsd = round($values['recovery_usd'], 2);
            $remainingUsd = max($agreedUsd - $recoveryUsd, 0);

            $perItemAgreedIdr[] = $agreedIdr;
            $perItemRecoveryIdr[] = $recoveryIdr;
            $perItemRemainingIdr[] = $remainingIdr;

            $perItemAgreedUsd[] = $agreedUsd;
            $perItemRecoveryUsd[] = $recoveryUsd;
            $perItemRemainingUsd[] = $remainingUsd;
        }

        $perItemTotals = [
            'idr' => [
                'agreed' => array_sum($perItemAgreedIdr),
                'recovery' => array_sum($perItemRecoveryIdr),
                'remaining' => array_sum($perItemRemainingIdr),
            ],
            'usd' => [
                'agreed' => array_sum($perItemAgreedUsd),
                'recovery' => array_sum($perItemRecoveryUsd),
                'remaining' => array_sum($perItemRemainingUsd),
            ],
        ];

        $perItemChart = [
            'labels' => $perItemLabels,
            'series' => [
                'idr' => [
                    'temuan' => $perItemAgreedIdr,
                    'recovery' => $perItemRecoveryIdr,
                    'remaining' => $perItemRemainingIdr,
                ],
                'usd' => [
                    'temuan' => $perItemAgreedUsd,
                    'recovery' => $perItemRecoveryUsd,
                    'remaining' => $perItemRemainingUsd,
                ],
            ],
            'totals' => $perItemTotals,
        ];

        $monthPriorityCounts = [];
        $priorityOrder = ['High', 'Medium', 'Low'];

        foreach ($forms as $form) {
            $referenceDate = $resolveDate(
                $form->tanggal_temuan,
                $form->start_date,
                $form->created_at
            );

            if (!$referenceDate) {
                continue;
            }

            if ($startDate && $referenceDate->lt($startDate)) {
                continue;
            }

            if ($endDate && $referenceDate->gt($endDate)) {
                continue;
            }

            if ($selectedYear && !$startDate && !$endDate && (int) $referenceDate->year !== (int) $selectedYear) {
                continue;
            }

            $monthKey = $referenceDate->format('Y-m');
            $priorityName = optional($form->priority)->name;
            $normalizedPriority = match (strtolower((string) $priorityName)) {
                'high' => 'High',
                'medium' => 'Medium',
                'low' => 'Low',
                default => $priorityName ? ucfirst(strtolower($priorityName)) : 'Unspecified',
            };

            if (!isset($monthPriorityCounts[$monthKey])) {
                $monthPriorityCounts[$monthKey] = [];
            }

            if (!isset($monthPriorityCounts[$monthKey][$normalizedPriority])) {
                $monthPriorityCounts[$monthKey][$normalizedPriority] = 0;
            }

            $monthPriorityCounts[$monthKey][$normalizedPriority]++;
        }

        ksort($monthPriorityCounts);

        $detectedPriorities = collect($monthPriorityCounts)
            ->flatMap(fn ($counts) => array_keys($counts))
            ->unique()
            ->values();

        $orderedPriorities = collect($priorityOrder)
            ->filter(fn ($name) => $detectedPriorities->contains($name))
            ->merge(
                $detectedPriorities->reject(fn ($name) => in_array($name, $priorityOrder, true))
            )
            ->values();

        $compositionLabels = [];
        $compositionSeries = [];

        foreach ($orderedPriorities as $priorityName) {
            $compositionSeries[$priorityName] = [];
        }

        foreach ($monthPriorityCounts as $monthKey => $counts) {
            try {
                $label = Carbon::createFromFormat('Y-m', $monthKey)->format('M Y');
            } catch (\Throwable $e) {
                $label = $monthKey;
            }

            $compositionLabels[] = $label;

            foreach ($orderedPriorities as $priorityName) {
                $compositionSeries[$priorityName][] = (int) ($counts[$priorityName] ?? 0);
            }
        }

        $finLossComposition = [
            'labels' => $compositionLabels,
            'series' => $compositionSeries,
        ];

        return view('admin.dashboard', [
            'totalPertemuan' => $totalPertemuan,
            'totalEngagement' => $totalEngagement,
            'auditeeCount' => $auditeeCount,
            'teamMemberCount' => $teamMemberCount,
            'selectedYear' => $selectedYear,
            'chartData' => $chartData,
            'finLossChart' => $finLossChart,
            'finLossSummary' => $finLossSummary,
            'finLossComposition' => $finLossComposition,
            'startDate' => $startDateInput,
            'endDate' => $endDateInput,
            'statusPieData' => $statusPieData,
            'statusSummary' => $statusSummary,
            'perItemChart' => $perItemChart,
            'perItemTotals' => $perItemTotals,
            'perItemYearOptions' => $perItemYearOptions->values()->all(),
            'perItemItemOptions' => $perItemItemOptions->values()->all(),
            'perItemSelectedYear' => $perItemSelectedYear,
            'perItemSelectedItem' => $perItemSelectedItem,
        ]);
    }

    // Fungsi untuk menampilkan halaman Findings
    public function findings(Request $request)
    {
        $filters = $request->only(['year', 'search']);

        $years = AuditPlan::query()
            ->whereNotNull('year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        $plansQuery = AuditPlan::query()
            ->withCount('findings')
            ->withCount(['findings as filtered_findings_count' => function ($query) use ($filters) {
                $this->applyFindingFilters($query, $filters);
            }]);

        if (!empty($filters['year'])) {
            $plansQuery->where('year', $filters['year']);
        }

        if (!empty($filters['search'])) {
            $plansQuery->where('title', 'like', '%' . $filters['search'] . '%');
        }

        if ($this->hasFindingFilters($filters)) {
            $plansQuery->whereHas('findings', function ($query) use ($filters) {
                $this->applyFindingFilters($query, $filters);
            });
        }

        $plans = $plansQuery
            ->orderByDesc('year')
            ->orderBy('title')
            ->get();

        $hasFindingFilters = $this->hasFindingFilters($filters);

        // Kumpulkan data FinLoss untuk kebutuhan grafik
        $finLossFormsQuery = AuditForm::query()
            ->with(['kategori', 'findlossdetails.recoveries'])
            ->whereHas('kategori', function ($query) {
                $query->where('name', 'Fin Loss');
            });

        if (!empty($filters['year'])) {
            $finLossFormsQuery->whereHas('auditPlan', function ($query) use ($filters) {
                $query->where('year', $filters['year']);
            });
        }

        if (!empty($filters['search'])) {
            $finLossFormsQuery->where('judul_temuan', 'like', '%' . $filters['search'] . '%');
        }

        $finLossForms = $finLossFormsQuery->get();

        $recoveryItemAggregated = [];
        $finLossAggregated = [];

        foreach ($finLossForms as $form) {
            $findingTitle = trim($form->judul_temuan ?? '') ?: 'Tanpa Judul';

            if (!isset($finLossAggregated[$findingTitle])) {
                $finLossAggregated[$findingTitle] = [
                    'agreed_idr' => 0.0,
                    'agreed_usd' => 0.0,
                    'recovery_idr' => 0.0,
                    'recovery_usd' => 0.0,
                ];
            }

            foreach ($form->findlossdetails as $detail) {
                $itemName = trim($detail->item ?? '') ?: 'Tanpa Item';
                $agreedIdr = (float) ($detail->nilai ?? 0);
                $agreedUsd = (float) ($detail->nilai_usd ?? 0);
                $recoveryIdr = (float) $detail->recoveries->sum('amount');
                $recoveryUsd = (float) $detail->recoveries->sum('amount_usd');

                if (!isset($recoveryItemAggregated[$itemName])) {
                    $recoveryItemAggregated[$itemName] = 0.0;
                }

                $recoveryItemAggregated[$itemName] += $recoveryIdr;

                $finLossAggregated[$findingTitle]['agreed_idr'] += $agreedIdr;
                $finLossAggregated[$findingTitle]['agreed_usd'] += $agreedUsd;
                $finLossAggregated[$findingTitle]['recovery_idr'] += $recoveryIdr;
                $finLossAggregated[$findingTitle]['recovery_usd'] += $recoveryUsd;
            }
        }

        // Siapkan data donut recovery per item (hanya item dengan nilai > 0)
        $recoveryItemAggregated = array_filter($recoveryItemAggregated, fn ($amount) => $amount > 0);
        arsort($recoveryItemAggregated);

        $recoveryChartData = [
            'labels' => array_keys($recoveryItemAggregated),
            'values' => array_values($recoveryItemAggregated),
        ];

        // Siapkan data bar chart per judul temuan
        $finLossAggregated = array_filter($finLossAggregated, function ($data) {
            return ($data['agreed_idr'] > 0)
                || ($data['agreed_usd'] > 0)
                || ($data['recovery_idr'] > 0)
                || ($data['recovery_usd'] > 0);
        });

        uasort($finLossAggregated, function (array $a, array $b) {
            $totalA = $a['agreed_idr'] + ($a['agreed_usd'] * 15000);
            $totalB = $b['agreed_idr'] + ($b['agreed_usd'] * 15000);
            return $totalB <=> $totalA;
        });

        $finLossLabels = array_keys($finLossAggregated);
        $finLossAgreedIdr = [];
        $finLossRecoveryIdr = [];
        $finLossRemainingIdr = [];
        $finLossAgreedUsd = [];
        $finLossRecoveryUsd = [];
        $finLossRemainingUsd = [];

        foreach ($finLossAggregated as $data) {
            $agreedIdr = round($data['agreed_idr'], 2);
            $recoveryIdr = round($data['recovery_idr'], 2);
            $remainingIdr = max($agreedIdr - $recoveryIdr, 0);

            $agreedUsd = round($data['agreed_usd'], 2);
            $recoveryUsd = round($data['recovery_usd'], 2);
            $remainingUsd = max($agreedUsd - $recoveryUsd, 0);

            $finLossAgreedIdr[] = $agreedIdr;
            $finLossRecoveryIdr[] = $recoveryIdr;
            $finLossRemainingIdr[] = $remainingIdr;

            $finLossAgreedUsd[] = $agreedUsd;
            $finLossRecoveryUsd[] = $recoveryUsd;
            $finLossRemainingUsd[] = $remainingUsd;
        }

        $finLossRecoveryData = [
            'labels' => $finLossLabels,
            'series' => [
                'idr' => [
                    'agreed' => $finLossAgreedIdr,
                    'recovery' => $finLossRecoveryIdr,
                    'remaining' => $finLossRemainingIdr,
                ],
                'usd' => [
                    'agreed' => $finLossAgreedUsd,
                    'recovery' => $finLossRecoveryUsd,
                    'remaining' => $finLossRemainingUsd,
                ],
            ],
            'totals' => [
                'agreed_idr' => array_sum($finLossAgreedIdr),
                'recovery_idr' => array_sum($finLossRecoveryIdr),
                'remaining_idr' => array_sum($finLossRemainingIdr),
                'agreed_usd' => array_sum($finLossAgreedUsd),
                'recovery_usd' => array_sum($finLossRecoveryUsd),
                'remaining_usd' => array_sum($finLossRemainingUsd),
            ],
        ];

        return view('admin.findings', compact(
            'plans',
            'filters',
            'years',
            'hasFindingFilters',
            'recoveryChartData',
            'finLossRecoveryData'
        ));
    }

    private function applyFindingFilters($query, array $filters): void
    {
        if (!empty($filters['search'])) {
            $term = '%' . $filters['search'] . '%';
            $query->where('judul_temuan', 'like', $term);
        }
    }

    private function hasFindingFilters(array $filters): bool
    {
        return !empty($filters['search']);
    }



    public function deleteFinding($id)
    {
        $finding = AuditForm::findOrFail($id);
        $finding->delete();

        return redirect()->route('admin.findings')->with('success', 'Finding deleted successfully.');
    }

    // In AdminController.php

    public function showAssessment($id)
    {
        // Hentikan eksekusi dan tampilkan ID yang diterima
        // dd('Controller berhasil diakses dengan ID: ' . $id);

        $finding = AuditForm::with([
            'kategori',
            'priority',
            'status',
            'reminder',
            'findlossdetails.recoveries',
            'fileattachments',
            'assessments.recoveries',     // ✅ tambahkan ini
            'auditorUser'  
        ])->findOrFail($id);

        $categories = Kategori::all();
        $subcategories = Subkategori::all();
        $priorities = Priority::all();

        // 🔹 Tambahkan ini
        $exchangeRate = 15000; // fallback
        try {
            $response = \Illuminate\Support\Facades\Http::get('https://api.exchangerate-api.com/v4/latest/USD');
            if ($response->successful()) {
                $exchangeRate = $response->json('rates.IDR');
            }
        } catch (\Exception $e) {
            // fallback tetap
        }

        // ✅ Hitung progress
        $total = $finding->assessments->count();
        $completed = 0;

        foreach ($finding->assessments as $assessment) {
            $fields = [
                $assessment->type,
                $assessment->title,
                $assessment->description,
                $assessment->test_date,
                $assessment->testing_performed
            ];

            // jika semua terisi (tidak null & tidak kosong)
            $isComplete = collect($fields)->every(fn($f) => !is_null($f) && trim($f) !== '');
            if ($isComplete) $completed++;
        }

        $progressPercent = $total > 0 ? round(($completed / $total) * 100) : 0;

        $resolveEventDate = function (...$candidates) {
            foreach ($candidates as $candidate) {
                if ($candidate instanceof Carbon) {
                    return $candidate->copy();
                }

                if ($candidate) {
                    try {
                        return Carbon::parse($candidate);
                    } catch (\Throwable $e) {
                        continue;
                    }
                }
            }

            return null;
        };

        $detailCharts = [];

        foreach (($finding->findlossdetails ?? collect()) as $detail) {
            $agreedAmountIdr = (float) ($detail->nilai ?? 0);
            $agreedAmountUsd = (float) ($detail->nilai_usd ?? 0);

            if ($agreedAmountIdr <= 0 && $agreedAmountUsd <= 0) {
                continue;
            }

            $events = collect();

            $agreedDate = $resolveEventDate(
                $detail->recorded_at,
                $finding->tanggal_temuan,
                $finding->start_date,
                $finding->created_at
            );

            $events->push([
                'type' => 'temuan',
                'date' => $agreedDate,
                'amount_idr' => $agreedAmountIdr,
                'amount_usd' => $agreedAmountUsd,
                'label' => ($agreedDate ? $agreedDate->format('d M Y') : 'Temuan') . ' • Temuan'
            ]);

            $detail->recoveries
                ->sortBy(fn ($recovery) => optional($resolveEventDate($recovery->recorded_at, $recovery->created_at))->timestamp ?? PHP_INT_MAX)
                ->values()
                ->each(function ($recovery, $index) use ($events, $resolveEventDate) {
                    $recoveryDate = $resolveEventDate($recovery->recorded_at, $recovery->created_at);
                    $amountIdr = (float) ($recovery->amount ?? 0);
                    $amountUsd = (float) ($recovery->amount_usd ?? 0);

                    if (($amountIdr <= 0 && $amountUsd <= 0) || !$recoveryDate) {
                        return;
                    }

                    $label = $recoveryDate->format('d M Y') . ' • Recovery ' . ($index + 1);
                    if ($recovery->notes) {
                        $label .= ' • ' . $recovery->notes;
                    }

                    $events->push([
                        'type' => 'recovery',
                        'date' => $recoveryDate,
                        'amount_idr' => $amountIdr,
                        'amount_usd' => $amountUsd,
                        'label' => $label,
                    ]);
                });

            $events = $events
                ->filter(fn ($event) => ($event['amount_idr'] ?? 0) > 0 || ($event['amount_usd'] ?? 0) > 0)
                ->sortBy(fn ($event) => optional($event['date'])->timestamp ?? PHP_INT_MAX)
                ->values();

            if ($events->isEmpty()) {
                continue;
            }

            $labels = [];

            $temuanSeriesIdr = [];
            $recoverySeriesIdr = [];
            $remainingSeriesIdr = [];

            $temuanSeriesUsd = [];
            $recoverySeriesUsd = [];
            $remainingSeriesUsd = [];

            $outstandingIdr = 0.0;
            $outstandingUsd = 0.0;

            $totalAgreedIdr = 0.0;
            $totalAgreedUsd = 0.0;
            $totalRecoveryIdr = 0.0;
            $totalRecoveryUsd = 0.0;

            foreach ($events as $event) {
                $amountIdr = round((float) ($event['amount_idr'] ?? 0), 2);
                $amountUsd = round((float) ($event['amount_usd'] ?? 0), 2);

                if ($event['type'] === 'temuan') {
                    $outstandingIdr += $amountIdr;
                    $outstandingUsd += $amountUsd;
                    $totalAgreedIdr += $amountIdr;
                    $totalAgreedUsd += $amountUsd;
                } else {
                    $totalRecoveryIdr += $amountIdr;
                    $totalRecoveryUsd += $amountUsd;
                    $outstandingIdr = max($outstandingIdr - $amountIdr, 0);
                    $outstandingUsd = max($outstandingUsd - $amountUsd, 0);
                }

                $labels[] = $event['label'] ?? 'Event';

                $temuanSeriesIdr[] = $event['type'] === 'temuan' ? $amountIdr : 0.0;
                $recoverySeriesIdr[] = $event['type'] === 'recovery' ? $amountIdr : 0.0;
                $remainingSeriesIdr[] = round($outstandingIdr, 2);

                $temuanSeriesUsd[] = $event['type'] === 'temuan' ? $amountUsd : 0.0;
                $recoverySeriesUsd[] = $event['type'] === 'recovery' ? $amountUsd : 0.0;
                $remainingSeriesUsd[] = round($outstandingUsd, 2);
            }

            $detailCharts[] = [
                'detail_id' => $detail->id,
                'item' => $detail->item ?? 'Detail #' . $detail->id,
                'labels' => $labels,
                'series' => [
                    'idr' => [
                        'temuan' => $temuanSeriesIdr,
                        'recovery' => $recoverySeriesIdr,
                        'remaining' => $remainingSeriesIdr,
                    ],
                    'usd' => [
                        'temuan' => $temuanSeriesUsd,
                        'recovery' => $recoverySeriesUsd,
                        'remaining' => $remainingSeriesUsd,
                    ],
                ],
                'totals' => [
                    'idr' => [
                        'agreed' => round($totalAgreedIdr, 2),
                        'recovery' => round($totalRecoveryIdr, 2),
                        'remaining' => round($outstandingIdr, 2),
                    ],
                    'usd' => [
                        'agreed' => round($totalAgreedUsd, 2),
                        'recovery' => round($totalRecoveryUsd, 2),
                        'remaining' => round($outstandingUsd, 2),
                    ],
                ],
            ];
        }

        // ===== Jika kategori bukan Fin Loss / Recovery → return biasa =====
        if (
            !($finding->kategori && strtolower($finding->kategori->name) === 'fin loss') ||
            !($finding->subkategori && strtolower($finding->subkategori->name) === 'recovery')
        ) {
            return view('admin.assessment', compact(
                'finding', 'categories', 'subcategories', 'priorities',
                'exchangeRate', 'progressPercent', 'detailCharts'
            ));
        }

        // ===== Logika Fin Loss + Recovery =====
        $allForms = AuditForm::with(['findlossdetails.recoveries', 'assessments.recoveries'])
            ->whereHas('kategori', fn($q) => $q->where('name', 'Fin Loss'))
            ->whereHas('subkategori', fn($q) => $q->where('name', 'Recovery'))
            ->whereNotNull('id') // jaga-jaga, abaikan data aneh
            ->orderBy('id')
            ->get()
            ->values(); // reindex supaya arraynya rapat (0,1,2,...)

        $sisaOutputPrev   = 0;
        $totalKerugian    = 0;
        $totalRecovery    = 0;
        $sisaOutputNow    = 0;
        $sisaInputNow     = 0;

        foreach ($allForms as $form) {


            $kerugian = $form->findlossdetails->sum('nilai');
            $recovery = $form->findlossdetails->sum(fn($detail) => $detail->paid_amount ?? 0);

            $sisaInput  = $sisaOutputPrev;
            $sisaOutput = ($kerugian + $sisaInput) - $recovery;

            if ($form->id === $finding->id) {
                $totalKerugian = $kerugian;
                $totalRecovery = $recovery;
                $sisaOutputNow = $sisaOutput;
                $sisaInputNow  = $sisaInput;
            }

            $sisaOutputPrev = $sisaOutput;
        }

        return view('admin.assessment', compact(
            'finding', 'categories', 'subcategories', 'priorities',
            'exchangeRate', 'progressPercent',
            'totalKerugian', 'totalRecovery', 'sisaOutputNow', 'sisaInputNow',
            'detailCharts'
        ));
    }

    public function autoSaveFinding(Request $request, $id)
    {
        $field = $request->input('field');
        $value = $request->input('value');

        $finding = AuditForm::with('reminder')->findOrFail($id);

        // 🧠 Daftar field yang boleh diubah
        $allowedFields = [
            'judul_temuan', 'temuan_audit', 'kategori_id',
            'subkategori_id', 'priority_id', 'rekomendasi_author',
            'catatan_tambahan', 'pic', 'start_date',
            'reminder_pt', 'reminder_nama', 'reminder_email'
        ];

        if (!in_array($field, $allowedFields)) {
            return response()->json(['error' => 'Invalid field'], 400);
        }

        // 🔹 Jika field milik reminder
        if (in_array($field, ['reminder_pt', 'reminder_nama', 'reminder_email'])) {
            $reminder = $finding->reminder;
            if ($reminder) {
                $column = str_replace('reminder_', '', $field);
                $reminder->$column = $value;
                $reminder->save();
            } else {
                return response()->json(['error' => 'Reminder not found'], 404);
            }
        } else {
            // 🔹 Field milik AuditForm
            $finding->$field = $value;
            $finding->save();
        }

        return response()->json(['success' => true]);
    }


    // Tampilkan form untuk membuat temuan audit baru
    public function createFinding(Request $request)
    {
        // Ambil kurs real-time
        $exchangeRate = 15000; // fallback
        try {
            $response = Http::get('https://api.exchangerate-api.com/v4/latest/USD');
            if ($response->successful()) {
                $exchangeRate = $response->json('rates.IDR');
            }
        } catch (\Exception $e) {
            // Tetap gunakan fallback jika API error
        }

        $categories = Kategori::all();
        $priorities = Priority::all();
        $subcategories = Subkategori::all();

        $allPlans = AuditPlan::orderByDesc('year')
            ->orderBy('title')
            ->get();

        $auditPlans = $allPlans->groupBy('year');

        $preselectedPlanId = $request->filled('plan') ? (int) $request->input('plan') : null;
        $preselectedPlan = $preselectedPlanId ? $allPlans->firstWhere('id', $preselectedPlanId) : null;

        if (!$preselectedPlan) {
            $preselectedPlanId = null;
        }

        $extraMailRecipients = collect(config('mail.extra_recipients', []));

        return view('admin.createAuditFindings', compact(
            'categories',
            'priorities',
            'subcategories',
            'exchangeRate',
            'auditPlans',
            'preselectedPlanId',
            'preselectedPlan',
            'extraMailRecipients'
        ));
    }


    public function addFindLossDetail(Request $request, $auditFormId)
    {
        try {
            $validated = $request->validate([
                'item' => 'required|string|max:255',
                'nilai' => 'required|numeric|min:0',
                'nilai_usd' => 'nullable|numeric|min:0',
                'recorded_at' => 'nullable|date',
            ]);

            $detail = Findlossdetail::create([
                'item' => $validated['item'],
                'nilai' => (float) $validated['nilai'],
                'nilai_usd' => (float) ($validated['nilai_usd'] ?? 0),
                'paid_amount' => 0,
                'paid_amount_usd' => 0,
                'recorded_at' => $validated['recorded_at'] ?? null,
                'audit_form_id' => $auditFormId,
            ]);

            $responseData = [
                'success' => true,
                'detail' => [
                    'id' => $detail->id,
                    'item' => $detail->item,
                    'nilai' => number_format($detail->nilai, 0, ',', '.'),
                    'nilai_usd' => number_format($detail->nilai_usd ?? 0, 2, '.', ','),
                    'paid_amount' => number_format($detail->paid_amount ?? 0, 0, ',', '.'),
                    'paid_amount_usd' => number_format($detail->paid_amount_usd ?? 0, 2, '.', ','),
                    'remaining' => number_format($detail->nilai, 0, ',', '.'),
                    'remaining_usd' => number_format(max(($detail->nilai_usd ?? 0) - ($detail->paid_amount_usd ?? 0), 0), 2, '.', ',')
                ]
            ];

            Log::info('✅ Response data before return', $responseData);

            return response()->json($responseData, 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            Log::error('❌ Error addFindLossDetail', ['msg' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function updateFindLossDetail(Request $request, $id)
    {
        $detail = Findlossdetail::findOrFail($id);

        $validated = $request->validate([
            'item' => 'nullable|string|max:255',
            'nilai' => 'required|numeric|min:0',
            'nilai_usd' => 'nullable|numeric|min:0',
        ]);

        if (array_key_exists('item', $validated) && $validated['item'] !== null) {
            $detail->item = trim($validated['item']);
        }

        $detail->nilai = (float) $validated['nilai'];
        $detail->nilai_usd = isset($validated['nilai_usd']) ? (float) $validated['nilai_usd'] : 0.0;
        $detail->save();

        $remainingIdr = max($detail->nilai - ($detail->paid_amount ?? 0), 0);
        $remainingUsd = max(($detail->nilai_usd ?? 0) - ($detail->paid_amount_usd ?? 0), 0);

        return response()->json([
            'success' => true,
            'detail' => [
                'id' => $detail->id,
                'item' => $detail->item,
                'nilai' => number_format($detail->nilai, 0, ',', '.'),
                'nilai_usd' => number_format((float) ($detail->nilai_usd ?? 0), 2, '.', ','),
                'paid_amount' => number_format($detail->paid_amount ?? 0, 0, ',', '.'),
                'paid_amount_usd' => number_format((float) ($detail->paid_amount_usd ?? 0), 2, '.', ','),
                'remaining' => number_format($remainingIdr, 0, ',', '.'),
                'remaining_usd' => number_format($remainingUsd, 2, '.', ','),
            ],
        ]);
    }


    public function deleteFindLossDetail($id)
    {
        try {
            $detail = \App\Models\Findlossdetail::findOrFail($id);
            $detail->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function storeFindLossRecovery(Request $request, $detailId)
    {
        $detail = Findlossdetail::findOrFail($detailId);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'amount_usd' => 'nullable|numeric|min:0',
            'recorded_at' => 'nullable|date',
            'notes' => 'nullable|string|max:255',
        ]);

        $recovery = $detail->recoveries()->create([
            'amount' => $validated['amount'],
            'amount_usd' => (float) ($validated['amount_usd'] ?? 0),
            'recorded_at' => $validated['recorded_at'] ?? now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        $detail->syncPaidAmount();

        return response()->json([
            'success' => true,
            'recovery' => $recovery,
            'paid_amount' => $detail->paid_amount,
            'paid_amount_usd' => $detail->paid_amount_usd,
            'remaining' => max($detail->nilai - $detail->paid_amount, 0),
            'remaining_usd' => max(($detail->nilai_usd ?? 0) - ($detail->paid_amount_usd ?? 0), 0),
        ], 201);
    }

    public function updateFindLossRecovery(Request $request, $id)
    {
        $recovery = FindlossRecovery::findOrFail($id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'amount_usd' => 'nullable|numeric|min:0',
            'recorded_at' => 'nullable|date',
            'notes' => 'nullable|string|max:255',
        ]);

        $recovery->amount = (float) $validated['amount'];
        $recovery->amount_usd = isset($validated['amount_usd']) ? (float) $validated['amount_usd'] : 0.0;
        $recovery->recorded_at = $validated['recorded_at'] ?? $recovery->recorded_at;
        $recovery->notes = $validated['notes'] ?? null;
        $recovery->save();

        $detail = $recovery->detail;
        if ($detail) {
            $detail->syncPaidAmount();
        }

        return response()->json([
            'success' => true,
            'recovery' => [
                'id' => $recovery->id,
                'amount' => number_format($recovery->amount, 0, ',', '.'),
                'amount_usd' => number_format((float) ($recovery->amount_usd ?? 0), 2, '.', ','),
                'recorded_at' => optional($recovery->recorded_at)->format('d M Y'),
                'notes' => $recovery->notes,
            ],
        ]);
    }

    public function deleteFindLossRecovery($id)
    {
        try {
            $recovery = FindlossRecovery::findOrFail($id);
            $detail = $recovery->detail;

            $recovery->delete();

            if ($detail) {
                $detail->syncPaidAmount();
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function uploadAttachment(Request $request, $id)
    {
        $request->validate(['file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120']); // <= 5 MB per file

        try {
            // Hitung total ukuran file yang sudah ada
            $currentTotalSize = Fileattachment::where('auditform_id', $id)->sum('file_size');
            $newFileSize = $request->file('file')->getSize();

            if (($currentTotalSize + $newFileSize) > (5 * 1024 * 1024)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Total ukuran file untuk form ini melebihi 5 MB.'
                ], 400);
            }

            $file = $request->file('file');
            $path = $file->store('audit-attachments', 'public');

            $attachment = Fileattachment::create([
                'auditform_id' => $id,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);

            return response()->json(['success' => true, 'attachment' => $attachment]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }


    public function deleteAttachment($id)
    {
        try {
            $file = Fileattachment::findOrFail($id);
            Storage::disk('public')->delete($file->file_path);
            $file->delete();
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function extendDueDate(Request $request, $id)
    {
        $request->validate([
            'due_date' => 'required|date|after_or_equal:today',
        ]);

        $form = AuditForm::with(['status', 'auditorUser', 'reminder'])->findOrFail($id);
        $oldDueDate = $form->due_date;
        $newDueDate = $request->due_date;

        // ✅ Cegah extend jika tanggal sama
        if ($newDueDate === $oldDueDate) {
            return response()->json(['success' => false, 'error' => 'Tanggal baru sama dengan due date saat ini.'], 400);
        }

        // Jangan izinkan lebih kecil dari due_date lama
        if (Carbon::parse($newDueDate)->lt(Carbon::parse($oldDueDate))) {
            return response()->json(['success' => false, 'error' => 'Tanggal baru tidak boleh sebelum due date lama.'], 400);
        }

        // ✅ Cegah duplikasi notifikasi extend di hari yang sama
        $today = Carbon::today();
        $alreadySent = Notification::where('auditform_id', $form->id)
            ->where('notificationstype_id', 4)
            ->whereDate('created_at', $today)
            ->exists();

        if ($alreadySent) {
            return response()->json(['success' => false, 'error' => 'Notifikasi extend sudah dikirim hari ini.'], 400);
        }

        // Update due date
        $form->due_date = $newDueDate;

        // Jika status sekarang Overdue → ubah jadi Open
        $openStatus = Status::where('status', 'Open')->first();
        if ($form->status->status === 'Overdue' && $openStatus) {
            $form->status_id = $openStatus->id;
        }

        $form->save();

        // Buat notifikasi
        Notification::create([
            'user_id' => $form->auditor,
            'auditform_id' => $form->id,
            'notificationstype_id' => 4, // tambahkan "Extend" di tabel notificationstype
            'title' => 'Due Date Diperpanjang',
            'message' => "Temuan '{$form->judul_temuan}' diperpanjang sampai {$newDueDate}.",
        ]);

        // Kirim email ke auditor & auditee
        $subject = '[Audit System] Due Date Diperpanjang';
        $title = 'Due Date Audit Diperpanjang';
        $message = "Temuan '{$form->judul_temuan}' telah diperpanjang sampai {$newDueDate}.";

        if ($form->auditorUser && $form->auditorUser->email) {
            Mail::to($form->auditorUser->email)->send(
                new AuditNotificationMail($subject, $title, $message, $form)
            );
        }

        if ($form->reminder && $form->reminder->email) {
            Mail::to($form->reminder->email)->send(
                new AuditNotificationMail($subject, $title, $message, $form)
            );
        }

        return response()->json(['success' => true]);
    }


    public function storeFinding(Request $request)
    {
        Log::info('Starting storeFinding...', ['input' => $request->all()]);

        try {

            // Validasi
            $request->validate([
                'audit_plan_id' => 'required|exists:audit_plans,id',
                'title' => 'required|string|max:255',
                'department_pic' => 'nullable|string|max:255',
                'auditor' => 'required|exists:users,id',
                'description' => 'required|string',
                'category' => 'required|string',
                'priority' => 'required|string',
                'sub_category' => 'nullable|string',
                'start_date' => 'required|date',
                'due_date' => 'required|date',
                'client_email' => 'nullable|string',
                'reminder_email' => 'nullable|string',
                'internal_notes' => 'nullable|string',
                'auditee_notes' => 'nullable|string',
                'file_upload' => 'nullable|array',
                'file_upload.*' => 'file|mimes:png,jpg,jpeg,pdf|max:5120',
                'loss_description' => 'nullable|array',
                'loss_value' => 'nullable|array',
                'loss_value_usd' => 'nullable|array', // manual USD values
            ]);

            $kategori = Kategori::where('name', $request->category)->first();
            $priority = Priority::where('name', $request->priority)->first();
            $subkategori = $request->category === 'Fin Loss' 
                ? Subkategori::where('name', $request->sub_category)->first()
                : null;

            // Validasi jika data tidak ditemukan
            if (!$kategori || !$priority) {
                return back()->withErrors(['error' => 'Kategori atau Priority tidak valid']);
            }

            $auditorUser = User::findOrFail($request->auditor);

            $rawAuditorEmails = $request->category === 'Fin Loss'
                ? $request->client_email
                : $request->reminder_email;

            $parsedAuditorEmails = $this->parseEmailList($rawAuditorEmails);

            $emailFieldKey = $request->category === 'Fin Loss' ? 'client_email' : 'reminder_email';

            if ($parsedAuditorEmails->isEmpty()) {
                return back()
                    ->withErrors([$emailFieldKey => 'Masukkan minimal satu email auditor yang valid.'])
                    ->withInput();
            }

            // Simpan reminder / auditee contact (hanya email yang dibutuhkan)
            $reminder = Reminder::create([
                'pt' => null,
                'nama' => null,
                'email' => $parsedAuditorEmails->first() ?? optional($auditorUser)->email,
            ]);
            $reminderId = $reminder->id;


            // Simpan AuditForm
            $findingDate = Carbon::now()->toDateString();
            $auditForm = AuditForm::create([
                'judul_temuan' => $request->title,
                'pic' => $request->department_pic,
                'auditor' => $request->auditor, // ID user
                'temuan_audit' => $request->description,
                'kategori_id' => $kategori->id,
                'priority_id' => $priority->id,
                'subkategori_id' => $subkategori?->id,
                'tanggal_temuan' => $findingDate,
                'start_date' => $request->start_date,
                'due_date' => $request->due_date,
                'reminder_id' => $reminderId,
                'rekomendasi_author' => $request->internal_notes,
                'catatan_tambahan' => $request->auditee_notes,
                'status_id' => 1, // Default 'Open'
                'audit_plan_id' => $request->audit_plan_id,
                // 'attachment_path' => $attachmentPath
            ]);

            // Simpan Fin Loss Details (jika ada)
            if ($request->category === 'Fin Loss' && is_array($request->loss_description)) {
                foreach ($request->loss_description as $index => $description) {
                    $value = $request->loss_value[$index] ?? null;
                    $valueUsd = $request->loss_value_usd[$index] ?? null;

                    if (!empty(trim($description)) && (is_numeric($value) || is_numeric($valueUsd))) {
                        Findlossdetail::create([
                            'item' => trim($description),
                            'nilai' => is_numeric($value) ? (float) $value : 0.0,
                            'nilai_usd' => is_numeric($valueUsd) ? (float) $valueUsd : 0.0,
                            'paid_amount' => 0.0,
                            'paid_amount_usd' => 0.0,
                            'recorded_at' => $auditForm->start_date ?? $auditForm->tanggal_temuan ?? now(),
                            'audit_form_id' => $auditForm->id,
                        ]);
                    }
                }

                Log::info('Fin Loss Details to save:', [
                    'descriptions' => $request->loss_description,
                    'values' => $request->loss_value,
                    'values_usd' => $request->loss_value_usd,
                    'audit_form_id' => $auditForm->id
                ]);

            }

            // Simpan lampiran (jika ada)
            // ================= FILE ATTACHMENT SECTION ======================
            if ($request->hasFile('file_upload')) {
                $files = $request->file('file_upload');
                
                foreach ($files as $file) {
                    $path = $file->store('audit-attachments', 'public');
                    
                    Fileattachment::create([
                        'auditform_id' => $auditForm->id,
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }

                Log::info('Files saved to database', [
                    'auditform_id' => $auditForm->id,
                    'file_count' => count($files),
                ]);
            } else {
                Log::warning('No file uploaded', ['input_files' => $request->allFiles()]);
            }


            Log::info('File uploaded?', [
                'has_file' => $request->hasFile('file_upload'),
                'files' => collect($request->file('file_upload'))->map(fn($f) => $f->getClientOriginalName())->toArray()
            ]);

            // Ambil data auditor & auditee
            $primaryReminderEmail = $parsedAuditorEmails->first();

            if (!empty($primaryReminderEmail)) {
                $reminder->update(['email' => $primaryReminderEmail]);
            }

            $allRecipientEmails = $parsedAuditorEmails
                ->push(optional($auditorUser)->email)
                ->filter()
                ->unique()
                ->values();

            // Buat notifikasi ke database (untuk web)
            Notification::create([
                'user_id' => $auditForm->auditor,
                'auditform_id' => $auditForm->id,
                'notificationstype_id' => 1, // Create
                'title' => 'Temuan Audit Baru Dibuat',
                'message' => "Pada tanggal '{$auditForm->tanggal_temuan}' Temuan '{$auditForm->judul_temuan}' telah dibuat dan ditugaskan kepada Anda.",
            ]);

            // Siapkan konten email
            $subject = '[Audit System] Temuan Audit Baru Dibuat';
            $title = 'Temuan Audit Baru Dibuat';
            $message = "Temuan '{$auditForm->judul_temuan}' telah dibuat.\n\nTanggal Temuan: {$auditForm->tanggal_temuan}\nDue Date: {$auditForm->due_date}";

            $this->sendNotificationMail(
                $allRecipientEmails->all(),
                new AuditNotificationMail($subject, $title, $message, $auditForm)
            );

            return redirect()->route('admin.findings')->with('success', 'Audit finding created successfully!');
        
        } catch (Exception $e) {
        Log::error('StoreFinding Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return back()->withErrors(['error' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }

    // penambahan assessment
    public function addAssessment(Request $request, $auditFormId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $form = \App\Models\AuditForm::findOrFail($auditFormId);

        $assessment = \App\Models\Assessment::create([
            'audit_form_id' => $form->id,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'assessment' => [
                'id' => $assessment->id,
                'title' => $assessment->title,
                'description' => $assessment->description,
            ]
        ]);
    }

    public function getAssessment($id)
    {
        $assessment = \App\Models\Assessment::findOrFail($id);

        return response()->json([
            'success' => true,
            'assessment' => [
                'id' => $assessment->id,
                'title' => $assessment->title,
                'description' => $assessment->description,
                'type' => $assessment->type,
                'test_date' => $assessment->test_date,
                'testing_performed' => $assessment->testing_performed,
            ]
        ]);
    }

    public function updateAssessment(Request $request, $id)
    {
        $assessment = \App\Models\Assessment::findOrFail($id);

        $request->validate([
            'field' => 'required|string',
            'value' => 'nullable|string|max:5000',
        ]);

        $allowed = ['type', 'title', 'description', 'test_date', 'testing_performed'];
        if (!in_array($request->field, $allowed)) {
            return response()->json(['success' => false, 'error' => 'Invalid field.'], 400);
        }

        $assessment->{$request->field} = $request->value;
        $assessment->save();

        return response()->json(['success' => true]);
    }

    public function deleteAssessment($id)
    {
        try {
            // 1️⃣ Ambil assessment
            $assessment = \App\Models\Assessment::findOrFail($id);

            // 2️⃣ Hapus semua recovery terkait
            $assessment->recoveries()->delete();

            // 3️⃣ Hapus assessment itu sendiri
            $assessment->delete();

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // ===================== RECOVERY =====================

    public function addRecovery(Request $request, $assessmentId)
    {
        $request->validate([
            'item' => 'required|string|max:255',
            'nilai' => 'required|numeric|min:0',
        ]);

        $exchangeRate = 15000; // fallback
        try {
            $response = \Illuminate\Support\Facades\Http::get('https://api.exchangerate-api.com/v4/latest/USD');
            if ($response->successful()) {
                $exchangeRate = $response->json('rates.IDR');
            }
        } catch (\Exception $e) {
            // fallback
        }

        $recovery = \App\Models\Recovery::create([
            'item' => $request->item,
            'nilai' => $request->nilai,
            'assessment_id' => $assessmentId,
        ]);

        return response()->json([
            'success' => true,
            'recovery' => [
                'id' => $recovery->id,
                'item' => $recovery->item,
                'nilai' => number_format($recovery->nilai, 0, ',', '.'),
                'usd' => number_format($recovery->nilai / $exchangeRate, 2),
            ],
        ]);
    }

    public function deleteRecovery($id)
    {
        try {
            $recovery = \App\Models\Recovery::findOrFail($id);
            $recovery->delete();

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getRecovery($assessmentId)
    {
        try {
            $recoveries = \App\Models\Recovery::where('assessment_id', $assessmentId)->get();

            $exchangeRate = 15000;
            try {
                $response = \Illuminate\Support\Facades\Http::get('https://api.exchangerate-api.com/v4/latest/USD');
                if ($response->successful()) {
                    $exchangeRate = $response->json('rates.IDR');
                }
            } catch (\Exception $e) {
                // fallback
            }

            $formatted = $recoveries->map(function ($r) use ($exchangeRate) {
                return [
                    'id' => $r->id,
                    'item' => $r->item,
                    'nilai' => number_format($r->nilai, 0, ',', '.'),
                    'usd' => number_format($r->nilai / $exchangeRate, 2),
                ];
            });

            return response()->json(['success' => true, 'recoveries' => $formatted]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function confirmFinding($id)
    {
        $finding = AuditForm::with([
            'kategori',
            'priority',
            'status',
            'subkategori',
            'reminder',
            'auditorUser'
        ])->findOrFail($id);

        return view('admin.confirmation', compact('finding'));
    }

    public function closeFinding($id)
    {
        $finding = AuditForm::with(['auditorUser', 'reminder'])->findOrFail($id);

        // Ubah status ke 'Closed'
        $closedStatus = Status::where('status', 'Closed')->first();
        if ($closedStatus) {
            $finding->status_id = $closedStatus->id;
            $finding->save();
        }

        // Kirim notifikasi terakhir
        Notification::create([
            'user_id' => $finding->auditor,
            'auditform_id' => $finding->id,
            'notificationstype_id' => 5, // misal 2 = Reminder / Close
            'title' => 'Audit Form Closed',
            'message' => "Temuan '{$finding->judul_temuan}' telah selesai dan ditutup secara permanen."
        ]);

        // Kirim email ke auditor & auditee
        $subject = '[Audit System] Audit Form Closed';
        $title   = 'Audit Form Ditutup';
        $message = "Form audit '{$finding->judul_temuan}' telah resmi ditutup oleh admin. Tidak ada notifikasi lanjutan.";

        if ($finding->auditorUser && $finding->auditorUser->email) {
            Mail::to($finding->auditorUser->email)
                ->send(new \App\Mail\AuditNotificationMail($subject, $title, $message, $finding));
        }

        if ($finding->reminder && $finding->reminder->email) {
            Mail::to($finding->reminder->email)
                ->send(new \App\Mail\AuditNotificationMail($subject, $title, $message, $finding));
        }

        return redirect()->route('admin.findings.assessment', $finding->id)
                        ->with('success', 'Form telah ditutup dan notifikasi akhir telah dikirim.');
    }

    // Fungsi untuk menampilkan halaman Report
    public function report()
    {
        return view('admin.report');
    }

    private function sendNotificationMail(array $recipients, \Illuminate\Contracts\Mail\Mailable $mailable): void
    {
        $emails = collect($recipients)
            ->merge(config('mail.extra_recipients', []))
            ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values();

        if ($emails->isEmpty()) {
            return;
        }

        $emails->each(function (string $email) use ($mailable) {
            Mail::to($email)->send(clone $mailable);
        });
    }

    private function parseEmailList(?string $rawEmails): \Illuminate\Support\Collection
    {
        if (blank($rawEmails)) {
            return collect();
        }

        return collect(preg_split('/[\s,;]+/', $rawEmails ?? '', -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn ($email) => strtolower(trim($email)))
            ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values();
    }

    // Fungsi untuk menampilkan halaman Manage Users
    public function manageUsers(Request $request)
    {
        $query = User::query();

        // 2. Terapkan filter pencarian JIKA ada input 'search' dari form
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('email', 'LIKE', "%{$searchTerm}%");
            });
        }

        $query->where('id', '!=', auth()->id());
        
        // 4. Urutkan berdasarkan nama
        $query->orderBy('name', 'asc');

        // 5. Ambil hasil dengan paginasi (15 data per halaman), BUKAN ->get()
        // ->withQueryString() akan memastikan link paginasi tetap membawa parameter pencarian
        $users = $query->paginate(15)->withQueryString();

        // 6. Kembalikan view dengan data yang sudah dipaginasi
        return view('admin.users.index', ['users' => $users]);
    }

    public function updateUserRole(Request $request, User $user)
    {
        // 1. Validasi input: pastikan role_id yang dikirim itu ada dan valid
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id', // 'roles' adalah nama tabel role Anda
        ]);

        // 2. Cegah Super Admin mengubah rolenya sendiri atau super admin lain
        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.users')
                ->with('error', 'Super Admin role cannot be changed.');
        }

        // 3. Update role user
        $user->role_id = $validated['role_id'];
        $user->save();

        // 4. Redirect kembali ke halaman manage users dengan pesan sukses
        return redirect()->route('admin.users')
            ->with('success', "{$user->name}'s role has been updated successfully!");
    }

    public function getStatusChart(Request $request)
    {
        [$start, $end] = $this->resolveChartDateRange(
            $request->input('start_date'),
            $request->input('end_date'),
            Carbon::now()->subMonths(6)->startOfMonth(),
            Carbon::now()->addMonths(6)->endOfMonth()
        );

        $query = AuditForm::query()->whereNotNull('status_id')->with('status');
        $this->applyChartDateRange($query, $start, $end);

        $forms = $query->get();

        $order = ['Open', 'Closed', 'Overdue'];

        $counts = $forms->groupBy(fn ($form) => optional($form->status)->status)
            ->map(fn ($items) => $items->count())
            ->filter(fn ($total, $status) => !empty($status));

        $labels = [];
        $values = [];

        foreach ($order as $statusName) {
            if ($counts->has($statusName)) {
                $labels[] = $statusName;
                $values[] = (int) $counts->get($statusName);
            }
        }

        foreach ($counts as $statusName => $total) {
            if (!in_array($statusName, $order, true)) {
                $labels[] = $statusName;
                $values[] = (int) $total;
            }
        }

        $totalAll = array_sum($values);
        $summary = [];

        if ($totalAll > 0) {
            foreach ($labels as $index => $label) {
                $value = $values[$index] ?? 0;
                $summary[] = [
                    'label' => $label,
                    'value' => $value,
                    'percentage' => round(($value / $totalAll) * 100, 1),
                ];
            }
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values,
            'summary' => $summary,
            'total' => $totalAll,
            'range' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ],
        ]);
    }

    public function getFinLossGlobalChart(Request $request)
    {
        [$start, $end] = $this->resolveChartDateRange(
            $request->input('start_date'),
            $request->input('end_date'),
            Carbon::now()->subMonths(6)->startOfMonth(),
            Carbon::now()->addMonths(6)->endOfMonth()
        );

        $query = AuditForm::query()
            ->whereHas('kategori', fn($q) => $q->where('name', 'Fin Loss'))
            ->with(['findlossdetails.recoveries']);

        $this->applyChartDateRange($query, $start, $end);

        $forms = $query->get();

        $totalAgreedIdr = 0.0;
        $totalAgreedUsd = 0.0;
        $totalRecoveryIdr = 0.0;
        $totalRecoveryUsd = 0.0;

        foreach ($forms as $form) {
            foreach ($form->findlossdetails as $detail) {
                $totalAgreedIdr += (float) ($detail->nilai ?? 0);
                $totalAgreedUsd += (float) ($detail->nilai_usd ?? 0);

                $totalRecoveryIdr += (float) $detail->recoveries->sum('amount');
                $totalRecoveryUsd += (float) $detail->recoveries->sum('amount_usd');
            }
        }

        $totalAgreedIdr = round($totalAgreedIdr, 2);
        $totalAgreedUsd = round($totalAgreedUsd, 2);
        $totalRecoveryIdr = round($totalRecoveryIdr, 2);
        $totalRecoveryUsd = round($totalRecoveryUsd, 2);

        $totalRemainingIdr = max(round($totalAgreedIdr - $totalRecoveryIdr, 2), 0);
        $totalRemainingUsd = max(round($totalAgreedUsd - $totalRecoveryUsd, 2), 0);

        $hasValue = ($totalAgreedIdr > 0) || ($totalRecoveryIdr > 0) || ($totalRemainingIdr > 0);

        return response()->json([
            'labels' => ['Total'],
            'datasets' => [
                'recovery' => [$totalRecoveryIdr],
                'remaining' => [$totalRemainingIdr],
                'agreed' => [$totalAgreedIdr],
            ],
            'totals' => [
                'agreed_idr' => $totalAgreedIdr,
                'recovery_idr' => $totalRecoveryIdr,
                'remaining_idr' => $totalRemainingIdr,
                'agreed_usd' => $totalAgreedUsd,
                'recovery_usd' => $totalRecoveryUsd,
                'remaining_usd' => $totalRemainingUsd,
            ],
            'hasValue' => $hasValue,
            'range' => [
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ],
        ]);
    }

    public function getFinLossFindingBreakdown(Request $request)
    {
        [$start, $end] = $this->resolveChartDateRange(
            $request->input('start_date'),
            $request->input('end_date'),
            Carbon::now()->subMonths(6)->startOfMonth(),
            Carbon::now()->addMonths(6)->endOfMonth()
        );

        $formsQuery = AuditForm::query()
            ->whereHas('kategori', fn($q) => $q->where('name', 'Fin Loss'))
            ->with(['findlossdetails.recoveries']);

        $this->applyChartDateRange($formsQuery, $start, $end);

        $forms = $formsQuery->get();

        $labelsIdr = [];
        $initialIdr = [];
        $recoveryIdr = [];
        $remainingIdr = [];

        $labelsUsd = [];
        $initialUsd = [];
        $recoveryUsd = [];
        $remainingUsd = [];

        foreach ($forms as $form) {
            $title = $form->judul_temuan ?: ('Finding #' . $form->id);

            $totalInitialIdr = 0.0;
            $totalRecoveryIdr = 0.0;
            $totalInitialUsd = 0.0;
            $totalRecoveryUsd = 0.0;

            foreach ($form->findlossdetails as $detail) {
                $totalInitialIdr += (float) ($detail->nilai ?? 0);
                $totalInitialUsd += (float) ($detail->nilai_usd ?? 0);

                $totalRecoveryIdr += (float) $detail->recoveries->sum('amount');
                $totalRecoveryUsd += (float) $detail->recoveries->sum('amount_usd');
            }

            $totalRemainingIdr = max($totalInitialIdr - $totalRecoveryIdr, 0);
            $totalRemainingUsd = max($totalInitialUsd - $totalRecoveryUsd, 0);

            if ($totalInitialIdr > 0 || $totalRecoveryIdr > 0 || $totalRemainingIdr > 0) {
                $labelsIdr[] = $title;
                $initialIdr[] = round($totalInitialIdr, 2);
                $recoveryIdr[] = round($totalRecoveryIdr, 2);
                $remainingIdr[] = round($totalRemainingIdr, 2);
            }

            if ($totalInitialUsd > 0 || $totalRecoveryUsd > 0 || $totalRemainingUsd > 0) {
                $labelsUsd[] = $title;
                $initialUsd[] = round($totalInitialUsd, 2);
                $recoveryUsd[] = round($totalRecoveryUsd, 2);
                $remainingUsd[] = round($totalRemainingUsd, 2);
            }
        }

        return response()->json([
            'idr' => [
                'labels' => $labelsIdr,
                'datasets' => [
                    ['label' => 'Initial Amount', 'data' => $initialIdr],
                    ['label' => 'Recovery', 'data' => $recoveryIdr],
                    ['label' => 'Remaining', 'data' => $remainingIdr],
                ],
            ],
            'usd' => [
                'labels' => $labelsUsd,
                'datasets' => [
                    ['label' => 'Initial Amount', 'data' => $initialUsd],
                    ['label' => 'Recovery', 'data' => $recoveryUsd],
                    ['label' => 'Remaining', 'data' => $remainingUsd],
                ],
            ],
        ]);
    }

    public function getReportTitleDistribution(Request $request)
    {
        $selectedYear = $request->integer('year');

        $availableYears = AuditPlan::query()
            ->whereNotNull('year')
            ->pluck('year')
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();

        if (!$selectedYear && $availableYears->isNotEmpty()) {
            $selectedYear = (int) $availableYears->first();
        }

        $plansQuery = AuditPlan::query()
            ->withCount(['findings as findings_count' => function ($query) {
                $query->whereNotNull('id');
            }])
            ->orderBy('title');

        if ($selectedYear) {
            $plansQuery->where('year', $selectedYear);
        }

        $plans = $plansQuery->get();

        $labels = $plans->pluck('title')->toArray();
        $values = $plans->pluck('findings_count')->map(fn ($count) => (int) $count)->toArray();

        $details = $plans->map(function ($plan) {
            return [
                'title' => $plan->title,
                'count' => (int) $plan->findings_count,
            ];
        })->values();

        return response()->json([
            'labels' => $labels,
            'values' => $values,
            'details' => $details,
            'years' => $availableYears,
            'selectedYear' => $selectedYear,
        ]);
    }

    public function getFinLossDonut(Request $request)
    {
        [$start, $end] = $this->resolveChartDateRange(
            $request->input('start_date'),
            $request->input('end_date'),
            Carbon::now()->subMonths(6)->startOfMonth(),
            Carbon::now()->addMonths(6)->endOfMonth()
        );

        $query = AuditForm::query()
            ->whereHas('kategori', fn($q) => $q->where('name', 'Fin Loss'))
            ->with(['subkategori', 'priority']);

        $this->applyChartDateRange($query, $start, $end);

        $forms = $query->get();

        $priorityKeys = ['high', 'medium', 'low'];
        $buckets = [
            'recovery' => array_fill_keys($priorityKeys, 0),
            'non-recovery' => array_fill_keys($priorityKeys, 0),
        ];

        foreach ($forms as $form) {
            $subcategoryName = strtolower(trim(optional($form->subkategori)->name ?? ''));

            // Logika yang lebih fleksibel untuk mengidentifikasi recovery vs non-recovery
            if ($subcategoryName === 'recovery' || Str::contains($subcategoryName, 'recovery')) {
                $bucketKey = 'recovery';
            } elseif ($subcategoryName === 'non-recovery' || Str::contains($subcategoryName, 'non')) {
                $bucketKey = 'non-recovery';
            } else {
                // Default ke recovery jika subkategori tidak jelas
                $bucketKey = 'recovery';
            }

            $priorityKey = strtolower(trim(optional($form->priority)->name ?? ''));

            if (!array_key_exists($priorityKey, $buckets[$bucketKey])) {
                continue;
            }

            $buckets[$bucketKey][$priorityKey]++;
        }

        $data = [
            'Recovery' => array_map(fn($key) => $buckets['recovery'][$key], $priorityKeys),
            'Non-Recovery' => array_map(fn($key) => $buckets['non-recovery'][$key], $priorityKeys),
        ];

        return response()->json($data);
    }

    public function getImprovementChart(Request $request)
    {
        [$start, $end] = $this->resolveChartDateRange(
            $request->input('start_date'),
            $request->input('end_date'),
            Carbon::now()->subMonths(6)->startOfMonth(),
            Carbon::now()->addMonths(6)->endOfMonth()
        );

        // Siapkan list bulan sesuai rentang
        $months = collect();
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $months->push($cursor->format('M'));
            $cursor->addMonth();
        }

        // Ambil data kategori 'Improvement'
        $query = AuditForm::query()
            ->whereHas('kategori', fn($q) => $q->where('name', 'Improvement'))
            ->with('priority');

        $this->applyChartDateRange($query, $start, $end);

        $forms = $query->get();

        $priorityKeys = ['high', 'medium', 'low'];
        $counts = [];

        foreach ($months as $monthLabel) {
            $counts[$monthLabel] = array_fill_keys($priorityKeys, 0);
        }

        foreach ($forms as $form) {
            $referenceDate = $this->resolveReferenceDate($form);
            if (!$referenceDate) {
                continue;
            }

            $monthKey = $referenceDate->format('M');
            if (!isset($counts[$monthKey])) {
                continue;
            }

            $priorityKey = strtolower(trim(optional($form->priority)->name ?? ''));
            if (!array_key_exists($priorityKey, $counts[$monthKey])) {
                continue;
            }

            $counts[$monthKey][$priorityKey]++;
        }

        $data = [
            'labels' => $months,
        ];

        foreach ($priorityKeys as $priorityKey) {
            $data[ucfirst($priorityKey)] = $months->map(fn($month) => $counts[$month][$priorityKey] ?? 0)->toArray();
        }

        return response()->json($data);
    }

    public function getNonComplianceChart(Request $request)
    {
        [$start, $end] = $this->resolveChartDateRange(
            $request->input('start_date'),
            $request->input('end_date'),
            Carbon::now()->subMonths(6)->startOfMonth(),
            Carbon::now()->addMonths(6)->endOfMonth()
        );

        // Daftar bulan yang ingin ditampilkan berdasarkan rentang
        $months = collect();
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $months->push($cursor->format('M'));
            $cursor->addMonth();
        }

        // Ambil data dari kategori Non Compliance
        $query = AuditForm::query()
            ->whereHas('kategori', fn($q) => $q->where('name', 'Non Compliance'))
            ->with('priority');

        $this->applyChartDateRange($query, $start, $end);

        $forms = $query->get();

        $priorityKeys = ['high', 'medium', 'low'];
        $counts = [];

        foreach ($months as $monthLabel) {
            $counts[$monthLabel] = array_fill_keys($priorityKeys, 0);
        }

        foreach ($forms as $form) {
            $referenceDate = $this->resolveReferenceDate($form);
            if (!$referenceDate) {
                continue;
            }

            $monthKey = $referenceDate->format('M');
            if (!isset($counts[$monthKey])) {
                continue;
            }

            $priorityKey = strtolower(trim(optional($form->priority)->name ?? ''));
            if (!array_key_exists($priorityKey, $counts[$monthKey])) {
                continue;
            }

            $counts[$monthKey][$priorityKey]++;
        }

        $dataPoints = [];

        foreach ($months as $monthLabel) {
            foreach ($priorityKeys as $priorityKey) {
                $dataPoints[] = [
                    'x' => $monthLabel,
                    'y' => ucfirst($priorityKey),
                    'value' => $counts[$monthLabel][$priorityKey] ?? 0,
                ];
            }
        }

        return response()->json(['data' => $dataPoints]);
    }

    private function applyChartDateRange(Builder $query, Carbon $start, Carbon $end): void
    {
        $query->where(function (Builder $scope) use ($start, $end) {
            $scope->whereBetween('start_date', [$start, $end])
                ->orWhereBetween('tanggal_temuan', [$start, $end])
                ->orWhere(function (Builder $fallback) use ($start, $end) {
                    $fallback->whereNull('start_date')
                        ->whereNull('tanggal_temuan')
                        ->whereBetween('due_date', [$start, $end]);
                });
        });
    }

    private function resolveReferenceDate(?AuditForm $form): ?Carbon
    {
        if (!$form) {
            return null;
        }

        $candidates = [
            $form->start_date,
            $form->tanggal_temuan,
            $form->due_date,
        ];

        foreach ($candidates as $candidate) {
            if ($candidate instanceof Carbon) {
                return $candidate->copy();
            }

            if ($candidate) {
                try {
                    return Carbon::parse($candidate);
                } catch (\Throwable $e) {
                    continue;
                }
            }
        }

        return null;
    }

    private function resolveChartDateRange(?string $startDate, ?string $endDate, Carbon $defaultStart, Carbon $defaultEnd): array
    {
        $start = $defaultStart;
        $end = $defaultEnd;

        if ($startDate) {
            try {
                $start = Carbon::parse($startDate)->startOfDay();
            } catch (\Throwable $e) {
                $start = $defaultStart;
            }
        }

        if ($endDate) {
            try {
                $end = Carbon::parse($endDate)->endOfDay();
            } catch (\Throwable $e) {
                $end = $defaultEnd;
            }
        }

        if ($start->greaterThan($end)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        return [$start, $end];
    }

    private function applyDashboardFilters($query, array $filters, ?int $year = null): void
    {
        if ($year) {
            $query->where(function ($yearQuery) use ($year) {
                $yearQuery->whereYear('tanggal_temuan', $year)
                    ->orWhere(function ($fallback) use ($year) {
                        $fallback->whereNull('tanggal_temuan')
                            ->where(function ($dateQuery) use ($year) {
                                $dateQuery->whereYear('start_date', $year)
                                    ->orWhereYear('due_date', $year);
                            });
                    });
            });
        }

        if (!empty($filters['audit_plan_id'])) {
            $query->where('audit_plan_id', $filters['audit_plan_id']);
        }

        if (!empty($filters['status'])) {
            $query->whereHas('status', function ($statusQuery) use ($filters) {
                $statusQuery->where('status', $filters['status']);
            });
        }

        if (!empty($filters['priority'])) {
            $query->whereHas('priority', function ($priorityQuery) use ($filters) {
                $priorityQuery->where('name', $filters['priority']);
            });
        }

        if (!empty($filters['kategori'])) {
            $query->whereHas('kategori', function ($kategoriQuery) use ($filters) {
                $kategoriQuery->where('name', $filters['kategori']);
            });
        }

        if (!empty($filters['search'])) {
            $searchTerm = "%{$filters['search']}%";
            $query->where(function ($searchQuery) use ($searchTerm) {
                $searchQuery->where('judul_temuan', 'like', $searchTerm)
                    ->orWhere('temuan_audit', 'like', $searchTerm);
            });
        }

        if (!empty($filters['due_start'])) {
            $query->whereDate('due_date', '>=', $filters['due_start']);
        }

        if (!empty($filters['due_end'])) {
            $query->whereDate('due_date', '<=', $filters['due_end']);
        }
    }

    public function getAuditRecapChart(Request $request)
    {
        // Ambil parameter tanggal
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Ambil semua audit plans dengan findings yang memiliki Fin Loss Recovery
        $plans = AuditPlan::query()
            ->with(['findings' => function ($query) use ($startDate, $endDate) {
                $query->whereHas('kategori', fn($q) => $q->where('name', 'Fin Loss'))
                      ->whereHas('subkategori', fn($q) => $q->where('name', 'Recovery'))
                      ->with(['findlossdetails.recoveries']);
                
                // Filter berdasarkan tanggal jika ada
                if ($startDate) {
                    $query->where(function($q) use ($startDate) {
                        $q->whereDate('tanggal_temuan', '>=', $startDate)
                          ->orWhereDate('start_date', '>=', $startDate)
                          ->orWhereDate('due_date', '>=', $startDate);
                    });
                }
                if ($endDate) {
                    $query->where(function($q) use ($endDate) {
                        $q->whereDate('tanggal_temuan', '<=', $endDate)
                          ->orWhereDate('start_date', '<=', $endDate)
                          ->orWhereDate('due_date', '<=', $endDate);
                    });
                }
            }])
            ->get();

        // Pisahkan data IDR dan USD
        $labelsIDR = [];
        $dataAwalIDR = [];
        $dataRecoveryIDR = [];
        $dataSisaIDR = [];

        $labelsUSD = [];
        $dataAwalUSD = [];
        $dataRecoveryUSD = [];
        $dataSisaUSD = [];

        foreach ($plans as $plan) {
            $totalAwalIDR = 0;
            $totalRecoveryIDR = 0;
            $totalAwalUSD = 0;
            $totalRecoveryUSD = 0;

            foreach ($plan->findings as $finding) {
                foreach ($finding->findlossdetails as $detail) {
                    // Pisahkan berdasarkan currency
                    $totalAwalIDR += $detail->nilai ?? 0;
                    $totalRecoveryIDR += $detail->paid_amount ?? 0;
                    $totalAwalUSD += $detail->nilai_usd ?? 0;
                    $totalRecoveryUSD += $detail->paid_amount_usd ?? 0;
                }
            }

            // Tambahkan ke array IDR jika ada data
            if ($totalAwalIDR > 0) {
                $labelsIDR[] = $plan->title;
                $dataAwalIDR[] = $totalAwalIDR;
                $dataRecoveryIDR[] = $totalRecoveryIDR;
                $dataSisaIDR[] = max($totalAwalIDR - $totalRecoveryIDR, 0);
            }

            // Tambahkan ke array USD jika ada data
            if ($totalAwalUSD > 0) {
                $labelsUSD[] = $plan->title;
                $dataAwalUSD[] = $totalAwalUSD;
                $dataRecoveryUSD[] = $totalRecoveryUSD;
                $dataSisaUSD[] = max($totalAwalUSD - $totalRecoveryUSD, 0);
            }
        }

        return response()->json([
            'idr' => [
                'labels' => $labelsIDR,
                'datasets' => [
                    [
                        'label' => 'Initial Amount',
                        'data' => $dataAwalIDR,
                        'backgroundColor' => '#22c55e',
                        'borderColor' => '#16a34a',
                        'borderWidth' => 1
                    ],
                    [
                        'label' => 'Recovery',
                        'data' => $dataRecoveryIDR,
                        'backgroundColor' => '#ef4444',
                        'borderColor' => '#dc2626',
                        'borderWidth' => 1
                    ],
                    [
                        'label' => 'Remaining',
                        'data' => $dataSisaIDR,
                        'backgroundColor' => '#3b82f6',
                        'borderColor' => '#2563eb',
                        'borderWidth' => 1
                    ]
                ]
            ],
            'usd' => [
                'labels' => $labelsUSD,
                'datasets' => [
                    [
                        'label' => 'Initial Amount',
                        'data' => $dataAwalUSD,
                        'backgroundColor' => '#22c55e',
                        'borderColor' => '#16a34a',
                        'borderWidth' => 1
                    ],
                    [
                        'label' => 'Recovery',
                        'data' => $dataRecoveryUSD,
                        'backgroundColor' => '#ef4444',
                        'borderColor' => '#dc2626',
                        'borderWidth' => 1
                    ],
                    [
                        'label' => 'Remaining',
                        'data' => $dataSisaUSD,
                        'backgroundColor' => '#3b82f6',
                        'borderColor' => '#2563eb',
                        'borderWidth' => 1
                    ]
                ]
            ]
        ]);
    }
}
