<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditPlan;
use App\Models\Kategori;
use App\Models\Priority;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuditPlanController extends Controller
{
    protected array $statusOptions = [
        'draft' => 'Draft',
        'planned' => 'Planned',
        'published' => 'Published',
    ];

    public function index()
    {
        $plans = AuditPlan::withCount(['findings as open_findings_count' => function ($query) {
                $query->whereHas('status', fn ($statusQuery) => $statusQuery->where('status', 'Open'));
            }])
            ->withCount('findings')
            ->orderByDesc('year')
            ->orderBy('title')
            ->get()
            ->groupBy('year');

        return view('admin.plans.index', [
            'plans' => $plans,
            'statuses' => $this->statusOptions,
        ]);
    }

    public function show(Request $request, AuditPlan $plan)
    {
        $availableYears = $plan->findings()
            ->selectRaw('YEAR(COALESCE(NULLIF(tanggal_temuan, ""), start_date, due_date)) as year')
            ->where(function ($query) {
                $query->whereNotNull('tanggal_temuan')
                    ->orWhereNotNull('start_date');
            })
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->filter();

        if ($availableYears->isEmpty()) {
            $availableYears = collect([(int) $plan->year]);
        }

        $selectedYear = (int) ($request->input('year') ?: $availableYears->first());

        $filters = $request->only(['status', 'priority', 'kategori', 'search', 'due_start', 'due_end']);

        $findingsQuery = $plan->findings()
            ->with(['status', 'priority', 'kategori', 'findlossdetails.recoveries']);

        $this->applyFilters($findingsQuery, $filters, $selectedYear);

        $findings = $findingsQuery
            ->orderBy(DB::raw('COALESCE(tanggal_temuan, start_date, due_date)'), 'asc')
            ->orderBy('id', 'asc')
            ->paginate(15)
            ->appends(array_filter($request->query()));

        // Statistik ringkas
        $totalsQuery = $plan->findings();
        $totalFindings = (clone $totalsQuery)->count();
        $openCount = (clone $totalsQuery)->whereHas('status', fn ($q) => $q->where('status', 'Open'))->count();
        $closedCount = (clone $totalsQuery)->whereHas('status', fn ($q) => $q->where('status', 'Closed'))->count();
        $overdueCount = (clone $totalsQuery)->whereHas('status', fn ($q) => $q->where('status', 'Overdue'))->count();

        $chartQuery = $plan->findings();
        $this->applyFilters($chartQuery, $filters, $selectedYear);

        $categoryCounts = (clone $chartQuery)
            ->select('kategori_id', DB::raw('COUNT(*) as total'))
            ->groupBy('kategori_id')
            ->get();

        $categoryNames = Kategori::whereIn('id', $categoryCounts->pluck('kategori_id')->filter())
            ->pluck('name', 'id');

        $chartData = [
            'labels' => $categoryCounts->map(function ($row) use ($categoryNames) {
                return $categoryNames[$row->kategori_id] ?? 'Tanpa Kategori';
            })->values()->all(),
            'values' => $categoryCounts->pluck('total')->values()->all(),
        ];

        $finLossFindings = (clone $chartQuery)
            ->with(['findlossdetails.recoveries', 'kategori', 'subkategori'])
            ->get();

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

        $timelineEvents = collect();
        $timelineFullLabels = [];
        $perFindingSummaries = [];
        $sequenceIndex = 0;

        $normalizeAmount = static function ($value): float {
            if ($value === null) {
                return 0.0;
            }

            if (is_numeric($value)) {
                return (float) $value;
            }

            if (!is_string($value)) {
                return (float) $value;
            }

            $clean = preg_replace('/[^0-9,.-]/', '', $value) ?? '';

            if ($clean === '' || $clean === '-' || $clean === '+') {
                return 0.0;
            }

            $lastComma = strrpos($clean, ',');
            $lastDot = strrpos($clean, '.');
            $decimalSeparator = null;

            if ($lastComma !== false || $lastDot !== false) {
                if ($lastComma === false) {
                    $decimalSeparator = '.';
                } elseif ($lastDot === false) {
                    $decimalSeparator = ',';
                } else {
                    $decimalSeparator = $lastComma > $lastDot ? ',' : '.';
                }

                $digitsAfter = 0;
                if ($decimalSeparator === ',') {
                    $digitsAfter = strlen($clean) - ($lastComma + 1);
                } elseif ($decimalSeparator === '.') {
                    $digitsAfter = strlen($clean) - ($lastDot + 1);
                }

                if ($digitsAfter > 2) {
                    $decimalSeparator = null;
                }
            }

            $separatorCandidates = [',', '.'];
            foreach ($separatorCandidates as $separator) {
                if ($separator !== $decimalSeparator) {
                    $clean = str_replace($separator, '', $clean);
                }
            }

            if ($decimalSeparator && $decimalSeparator !== '.') {
                $clean = str_replace($decimalSeparator, '.', $clean);
            }

            return (float) $clean;
        };

        foreach ($finLossFindings as $findingRecord) {
            $isFinLoss = strtolower($findingRecord->kategori->name ?? '') === 'fin loss';
            $subName = strtolower(trim($findingRecord->subkategori->name ?? ''));
            $isRecoverySubcategory = $subName === '' || str_contains($subName, 'recovery');

            if (!$isFinLoss || !$isRecoverySubcategory) {
                continue;
            }

            $fullTitle = trim($findingRecord->judul_temuan ?? '') ?: 'Tanpa Judul';
            $summary = [
                'finding_id' => $findingRecord->id,
                'title' => $fullTitle,
                'title_short' => Str::limit($fullTitle, 70),
                'initial_idr' => 0.0,
                'initial_usd' => 0.0,
                'recovery_idr' => 0.0,
                'recovery_usd' => 0.0,
                'remaining_idr' => 0.0,
                'remaining_usd' => 0.0,
                'finding_events' => 0,
                'recovery_events' => 0,
                'first_event_date' => null,
                'first_event_date_sort' => PHP_INT_MAX,
            ];

            foreach ($findingRecord->findlossdetails as $detail) {
                $initialAmountIdr = $normalizeAmount($detail->nilai ?? 0);
                $initialAmountUsd = $normalizeAmount($detail->nilai_usd ?? 0);

                if ($initialAmountIdr > 0 || $initialAmountUsd > 0) {
                    $eventDate = $resolveEventDate(
                        $detail->recorded_at,
                        $findingRecord->tanggal_temuan,
                        $findingRecord->start_date,
                        $findingRecord->created_at
                    );

                    if ($eventDate) {
                        $timestamp = $eventDate->timestamp;
                        if ($timestamp < $summary['first_event_date_sort']) {
                            $summary['first_event_date_sort'] = $timestamp;
                            $summary['first_event_date'] = $eventDate->format('d M Y');
                        }
                    }

                    $eventLabel = ($eventDate ? $eventDate->format('d M Y') : 'Temuan') . ' • ' . $fullTitle;
                    $timelineEvents->push([
                        'type' => 'initial',
                        'date' => $eventDate,
                        'amount_idr' => $initialAmountIdr,
                        'amount_usd' => $initialAmountUsd,
                        'label' => Str::limit($eventLabel, 60),
                        'sequence' => $sequenceIndex++,
                    ]);
                    $timelineFullLabels[] = $eventLabel;

                    $summary['initial_idr'] += $initialAmountIdr;
                    $summary['initial_usd'] += $initialAmountUsd;
                    $summary['finding_events']++;
                }

                $detail->recoveries
                    ->sortBy(fn ($recovery) => optional($resolveEventDate($recovery->recorded_at, $recovery->created_at))->timestamp ?? PHP_INT_MAX)
                    ->values()
                    ->each(function ($recovery) use (&$timelineEvents, $resolveEventDate, $findingRecord, $detail, &$sequenceIndex, $normalizeAmount, &$summary, &$timelineFullLabels, $fullTitle) {
                        $amountIdr = $normalizeAmount($recovery->amount ?? 0);
                        $amountUsd = $normalizeAmount($recovery->amount_usd ?? 0);

                        if ($amountIdr <= 0 && $amountUsd <= 0) {
                            return;
                        }

                        $eventDate = $resolveEventDate(
                            $recovery->recorded_at,
                            $recovery->created_at,
                            $findingRecord->start_date,
                            $findingRecord->tanggal_temuan,
                            $findingRecord->created_at
                        );
                        $notes = trim($recovery->notes ?? '') ?: ($detail->item ?? 'Recovery');

                        if ($eventDate) {
                            $timestamp = $eventDate->timestamp;
                            if ($timestamp < $summary['first_event_date_sort']) {
                                $summary['first_event_date_sort'] = $timestamp;
                                $summary['first_event_date'] = $eventDate->format('d M Y');
                            }
                        }

                        $eventLabel = ($eventDate ? $eventDate->format('d M Y') : 'Recovery') . ' • ' . $notes . ' • ' . $fullTitle;
                        $timelineEvents->push([
                            'type' => 'recovery',
                            'date' => $eventDate,
                            'amount_idr' => $amountIdr,
                            'amount_usd' => $amountUsd,
                            'label' => Str::limit($eventLabel, 60),
                            'sequence' => $sequenceIndex++,
                        ]);
                        $timelineFullLabels[] = $eventLabel;

                        $summary['recovery_idr'] += $amountIdr;
                        $summary['recovery_usd'] += $amountUsd;
                        $summary['recovery_events']++;
                    });
            }

            $summary['remaining_idr'] = max($summary['initial_idr'] - $summary['recovery_idr'], 0);
            $summary['remaining_usd'] = max($summary['initial_usd'] - $summary['recovery_usd'], 0);

            if ($summary['finding_events'] > 0 || $summary['recovery_events'] > 0) {
                $perFindingSummaries[] = $summary;
            }
        }

        usort($perFindingSummaries, function (array $a, array $b) {
            if ($a['first_event_date_sort'] === $b['first_event_date_sort']) {
                return strcmp($a['title'], $b['title']);
            }

            return $a['first_event_date_sort'] <=> $b['first_event_date_sort'];
        });

        $linkedLabels = [];
        $linkedSaldoAwalIdr = [];
        $linkedPenambahanIdr = [];
        $linkedRecoveryIdr = [];
        $linkedSaldoAkhirIdr = [];

        $linkedSaldoAwalUsd = [];
        $linkedPenambahanUsd = [];
        $linkedRecoveryUsd = [];
        $linkedSaldoAkhirUsd = [];

        $runningOutstandingIdr = 0.0;
        $runningOutstandingUsd = 0.0;

        foreach ($perFindingSummaries as $summary) {
            $label = $summary['title_short'] ?? $summary['title'] ?? 'Temuan';

            $initialIdr = round((float) ($summary['initial_idr'] ?? 0), 2);
            $recoveryIdr = round((float) ($summary['recovery_idr'] ?? 0), 2);
            $saldoAwalIdr = round($runningOutstandingIdr, 2);
            $saldoAkhirIdr = max($saldoAwalIdr + $initialIdr - $recoveryIdr, 0);

            $initialUsd = round((float) ($summary['initial_usd'] ?? 0), 2);
            $recoveryUsd = round((float) ($summary['recovery_usd'] ?? 0), 2);
            $saldoAwalUsd = round($runningOutstandingUsd, 2);
            $saldoAkhirUsd = max($saldoAwalUsd + $initialUsd - $recoveryUsd, 0);

            $linkedLabels[] = $label;

            $linkedSaldoAwalIdr[] = $saldoAwalIdr;
            $linkedPenambahanIdr[] = $initialIdr;
            $linkedRecoveryIdr[] = $recoveryIdr;
            $linkedSaldoAkhirIdr[] = $saldoAkhirIdr;

            $linkedSaldoAwalUsd[] = $saldoAwalUsd;
            $linkedPenambahanUsd[] = $initialUsd;
            $linkedRecoveryUsd[] = $recoveryUsd;
            $linkedSaldoAkhirUsd[] = $saldoAkhirUsd;

            $runningOutstandingIdr = $saldoAkhirIdr;
            $runningOutstandingUsd = $saldoAkhirUsd;
        }

        $timelineEvents = $timelineEvents
            ->filter(fn ($event) => ($event['amount_idr'] ?? 0) > 0 || ($event['amount_usd'] ?? 0) > 0)
            ->sort(function ($a, $b) {
                $timestampA = $a['date'] instanceof Carbon ? $a['date']->timestamp : PHP_INT_MAX;
                $timestampB = $b['date'] instanceof Carbon ? $b['date']->timestamp : PHP_INT_MAX;

                if ($timestampA === $timestampB) {
                    return ($a['sequence'] ?? 0) <=> ($b['sequence'] ?? 0);
                }

                return $timestampA <=> $timestampB;
            })
            ->values();

        $timelineLabels = [];
        $saldoAwalSeriesIdr = [];
        $recoverySeriesIdr = [];
        $sisaSeriesIdr = [];
        $saldoAwalSeriesUsd = [];
        $recoverySeriesUsd = [];
        $sisaSeriesUsd = [];

        $runningOutstandingIdr = 0.0;
        $runningOutstandingUsd = 0.0;

        $totalTemuanIdr = 0.0;
        $totalTemuanUsd = 0.0;
        $totalRecoveryIdr = 0.0;
        $totalRecoveryUsd = 0.0;

        $findingEventCount = 0;
        $recoveryEventCount = 0;

        foreach ($timelineEvents as $index => $event) {
            $label = $event['label'] ?? 'Event';
            $timelineLabels[] = $label;

            if ($event['type'] === 'initial') {
                $runningOutstandingIdr += (float) $event['amount_idr'];
                $runningOutstandingUsd += (float) $event['amount_usd'];

                $saldoAwalSeriesIdr[] = round($runningOutstandingIdr, 2);
                $recoverySeriesIdr[] = 0.0;
                $sisaSeriesIdr[] = round($runningOutstandingIdr, 2);

                $saldoAwalSeriesUsd[] = round($runningOutstandingUsd, 2);
                $recoverySeriesUsd[] = 0.0;
                $sisaSeriesUsd[] = round($runningOutstandingUsd, 2);

                $totalTemuanIdr += (float) $event['amount_idr'];
                $totalTemuanUsd += (float) $event['amount_usd'];
                $findingEventCount++;
            } else {
                $saldoAwalIdr = round($runningOutstandingIdr, 2);
                $saldoAwalUsd = round($runningOutstandingUsd, 2);

                $recoveryAmountIdr = min(round((float) $event['amount_idr'], 2), $saldoAwalIdr);
                $recoveryAmountUsd = min(round((float) $event['amount_usd'], 2), $saldoAwalUsd);

                $runningOutstandingIdr = max($runningOutstandingIdr - $recoveryAmountIdr, 0);
                $runningOutstandingUsd = max($runningOutstandingUsd - $recoveryAmountUsd, 0);

                $saldoAwalSeriesIdr[] = $saldoAwalIdr;
                $recoverySeriesIdr[] = $recoveryAmountIdr;
                $sisaSeriesIdr[] = round($runningOutstandingIdr, 2);

                $saldoAwalSeriesUsd[] = $saldoAwalUsd;
                $recoverySeriesUsd[] = $recoveryAmountUsd;
                $sisaSeriesUsd[] = round($runningOutstandingUsd, 2);

                $totalRecoveryIdr += $recoveryAmountIdr;
                $totalRecoveryUsd += $recoveryAmountUsd;
                $recoveryEventCount++;
            }
        }

        $recoveryChartData = [
            'labels' => $timelineLabels,
            'series' => [
                'idr' => [
                    'saldo_awal' => $saldoAwalSeriesIdr,
                    'recovery' => $recoverySeriesIdr,
                    'sisa' => $sisaSeriesIdr,
                ],
                'usd' => [
                    'saldo_awal' => $saldoAwalSeriesUsd,
                    'recovery' => $recoverySeriesUsd,
                    'sisa' => $sisaSeriesUsd,
                ],
            ],
            'totals' => [
                'agreed_idr' => round($totalTemuanIdr, 2),
                'recovery_idr' => round($totalRecoveryIdr, 2),
                'remaining_idr' => round($runningOutstandingIdr, 2),
                'agreed_usd' => round($totalTemuanUsd, 2),
                'recovery_usd' => round($totalRecoveryUsd, 2),
                'remaining_usd' => round($runningOutstandingUsd, 2),
            ],
            'meta' => [
                'finding_events' => $findingEventCount,
                'recovery_events' => $recoveryEventCount,
            ],
            'labels_full' => $timelineFullLabels,
            'summaries' => array_map(function (array $summary) {
                unset($summary['first_event_date_sort']);

                return $summary;
            }, $perFindingSummaries),
            'linked' => [
                'labels' => $linkedLabels,
                'idr' => [
                    'saldo_awal' => $linkedSaldoAwalIdr,
                    'temuan' => $linkedPenambahanIdr,
                    'recovery' => $linkedRecoveryIdr,
                    'saldo_akhir' => $linkedSaldoAkhirIdr,
                ],
                'usd' => [
                    'saldo_awal' => $linkedSaldoAwalUsd,
                    'temuan' => $linkedPenambahanUsd,
                    'recovery' => $linkedRecoveryUsd,
                    'saldo_akhir' => $linkedSaldoAkhirUsd,
                ],
            ],
        ];

        $plan->loadCount('findings');

        return view('admin.plans.show', [
            'plan' => $plan,
            'findings' => $findings,
            'totals' => [
                'total' => $totalFindings,
                'open' => $openCount,
                'closed' => $closedCount,
                'overdue' => $overdueCount,
            ],
            'filters' => $filters,
            'selectedYear' => $selectedYear,
            'availableYears' => $availableYears,
            'chartData' => $chartData,
            'statuses' => Status::orderBy('status')->get(),
            'priorities' => Priority::orderBy('name')->get(),
            'categories' => Kategori::orderBy('name')->get(),
            'finLossRecoveryChart' => $recoveryChartData,
        ]);
    }

    public function create()
    {
        return view('admin.plans.create', [
            'statuses' => $this->statusOptions,
            'defaultYear' => Carbon::now()->year,
        ]);
    }

    public function edit(AuditPlan $plan)
    {
        return view('admin.plans.edit', [
            'plan' => $plan,
            'statuses' => $this->statusOptions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'digits:4'],
            'status' => ['required', 'in:' . implode(',', array_keys($this->statusOptions))],
            'description' => ['nullable', 'string'],
        ]);

        AuditPlan::create($validated);

        return redirect()->route('admin.plans.index')
            ->with('success', 'Audit plan berhasil dibuat.');
    }

    public function update(Request $request, AuditPlan $plan)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'digits:4'],
            'status' => ['required', 'in:' . implode(',', array_keys($this->statusOptions))],
            'description' => ['nullable', 'string'],
        ]);

        $plan->update($validated);

        return redirect()->route('admin.findings')
            ->with('success', 'Judul laporan berhasil diperbarui.');
    }

    public function destroy(AuditPlan $plan)
    {
        DB::transaction(function () use ($plan) {
            $plan->findings()->update(['audit_plan_id' => null]);
            $plan->delete();
        });

        return redirect()->route('admin.findings')
            ->with('success', 'Judul laporan berhasil dihapus.');
    }

    private function applyFilters($query, array $filters, ?int $year = null): void
    {
        $query->when($year, function ($q) use ($year) {
            $q->where(function ($inner) use ($year) {
                $inner->whereYear('tanggal_temuan', $year)
                    ->orWhere(function ($fallback) use ($year) {
                        $fallback->whereNull('tanggal_temuan')
                            ->whereYear('start_date', $year);
                    });
            });
        });

        $query->when($filters['status'] ?? null, function ($q, $status) {
            $q->whereHas('status', fn ($sub) => $sub->where('status', $status));
        });

        $query->when($filters['priority'] ?? null, function ($q, $priority) {
            $q->whereHas('priority', fn ($sub) => $sub->where('name', $priority));
        });

        $query->when($filters['kategori'] ?? null, function ($q, $kategori) {
            $q->whereHas('kategori', fn ($sub) => $sub->where('name', $kategori));
        });

        $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where(function ($searchQuery) use ($search) {
                $searchQuery->where('judul_temuan', 'like', "%{$search}%")
                    ->orWhere('temuan_audit', 'like', "%{$search}%");
            });
        });

        $query->when($filters['due_start'] ?? null, function ($q, $start) {
            $q->whereDate('due_date', '>=', $start);
        });

        $query->when($filters['due_end'] ?? null, function ($q, $end) {
            $q->whereDate('due_date', '<=', $end);
        });
    }
}
