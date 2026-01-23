<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\OperatorWalletTransaction;
use App\Models\User;
use App\Models\RechargeCard;
use App\Services\ExcelExportService;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class YearlyReportController extends Controller
{
    /**
     * Display yearly reports dashboard
     */
    public function index(): View
    {
        $currentYear = Carbon::now()->year;
        $years = range($currentYear, $currentYear - 5);

        return view('panels.admin.reports.yearly.index', compact('years'));
    }

    /**
     * Yearly Card Distributor Payments Report
     */
    public function cardDistributorPayments(Request $request): View
    {
        $year = $request->input('year', Carbon::now()->year);
        
        // Get card distributors
        $distributors = User::where('role_level', 90) // Card distributor role level
            ->with(['payments' => function ($query) use ($year) {
                $query->whereYear('created_at', $year);
            }])
            ->get();

        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthlyData[$month] = [];
            foreach ($distributors as $distributor) {
                $monthlyData[$month][$distributor->id] = $distributor->payments()
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->sum('amount');
            }
        }

        $totalByDistributor = [];
        foreach ($distributors as $distributor) {
            $totalByDistributor[$distributor->id] = $distributor->payments()
                ->whereYear('created_at', $year)
                ->sum('amount');
        }

        $grandTotal = array_sum($totalByDistributor);

        return view('panels.admin.reports.yearly.card-distributor-payments', compact(
            'year',
            'distributors',
            'monthlyData',
            'totalByDistributor',
            'grandTotal'
        ));
    }

    /**
     * Yearly Cash In Report (Income)
     */
    public function cashIn(Request $request): View
    {
        $year = $request->input('year', Carbon::now()->year);
        
        // Get all payments received (cash in)
        $monthlyIncome = [];
        $sourceBreakdown = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $payments = Payment::whereYear('payment_date', $year)
                ->whereMonth('payment_date', $month)
                ->get();

            $monthlyIncome[$month] = [
                'total' => $payments->sum('amount'),
                'count' => $payments->count(),
                'by_method' => $payments->groupBy('payment_method')->map(function ($items) {
                    return [
                        'amount' => $items->sum('amount'),
                        'count' => $items->count()
                    ];
                }),
            ];

            // Track sources
            foreach ($payments->groupBy('payment_method') as $method => $items) {
                if (!isset($sourceBreakdown[$method])) {
                    $sourceBreakdown[$method] = array_fill(1, 12, 0);
                }
                $sourceBreakdown[$method][$month] = $items->sum('amount');
            }
        }

        $yearlyTotal = array_sum(array_column($monthlyIncome, 'total'));
        $averageMonthly = $yearlyTotal / 12;

        return view('panels.admin.reports.yearly.cash-in', compact(
            'year',
            'monthlyIncome',
            'sourceBreakdown',
            'yearlyTotal',
            'averageMonthly'
        ));
    }

    /**
     * Yearly Cash Out Report (Expenses)
     */
    public function cashOut(Request $request): View
    {
        $year = $request->input('year', Carbon::now()->year);
        
        // Get all operator wallet deductions and system expenses
        $monthlyExpenses = [];
        $categoryBreakdown = [];
        
        for ($month = 1; $month <= 12; $month++) {
            // Operator commissions and withdrawals
            $operatorTransactions = OperatorWalletTransaction::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->where('transaction_type', 'debit')
                ->get();

            $monthlyExpenses[$month] = [
                'operator_commissions' => $operatorTransactions->where('description', 'like', '%commission%')->sum('amount'),
                'operator_withdrawals' => $operatorTransactions->where('description', 'like', '%withdrawal%')->sum('amount'),
                'total' => $operatorTransactions->sum('amount'),
                'count' => $operatorTransactions->count(),
            ];

            // Category breakdown
            $categoryBreakdown['Operator Commissions'][$month] = $monthlyExpenses[$month]['operator_commissions'];
            $categoryBreakdown['Operator Withdrawals'][$month] = $monthlyExpenses[$month]['operator_withdrawals'];
        }

        $yearlyTotal = array_sum(array_column($monthlyExpenses, 'total'));
        $averageMonthly = $yearlyTotal / 12;

        return view('panels.admin.reports.yearly.cash-out', compact(
            'year',
            'monthlyExpenses',
            'categoryBreakdown',
            'yearlyTotal',
            'averageMonthly'
        ));
    }

    /**
     * Yearly Operator Income Report
     */
    public function operatorIncome(Request $request): View
    {
        $year = $request->input('year', Carbon::now()->year);
        
        // Get all operators
        $operators = User::whereIn('role_level', [30, 40]) // Operator and Sub-Operator
            ->get();

        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthlyData[$month] = [];
            foreach ($operators as $operator) {
                // Get payments collected by operator
                $operatorPayments = Payment::whereYear('payment_date', $year)
                    ->whereMonth('payment_date', $month)
                    ->where('collected_by', $operator->id)
                    ->sum('amount');

                // Get operator's commissions
                $commissions = OperatorWalletTransaction::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->where('user_id', $operator->id)
                    ->where('transaction_type', 'credit')
                    ->where('description', 'like', '%commission%')
                    ->sum('amount');

                $monthlyData[$month][$operator->id] = [
                    'collections' => $operatorPayments,
                    'commissions' => $commissions,
                    'total' => $operatorPayments + $commissions,
                ];
            }
        }

        $totalByOperator = [];
        foreach ($operators as $operator) {
            $collections = Payment::whereYear('payment_date', $year)
                ->where('collected_by', $operator->id)
                ->sum('amount');

            $commissions = OperatorWalletTransaction::whereYear('created_at', $year)
                ->where('user_id', $operator->id)
                ->where('transaction_type', 'credit')
                ->where('description', 'like', '%commission%')
                ->sum('amount');

            $totalByOperator[$operator->id] = [
                'collections' => $collections,
                'commissions' => $commissions,
                'total' => $collections + $commissions,
            ];
        }

        return view('panels.admin.reports.yearly.operator-income', compact(
            'year',
            'operators',
            'monthlyData',
            'totalByOperator'
        ));
    }

    /**
     * Yearly Expense Report
     */
    public function expenses(Request $request): View
    {
        $year = $request->input('year', Carbon::now()->year);
        
        $monthlyExpenses = [];
        $categoryTotals = [];
        
        // Define expense categories
        $categories = [
            'Salaries & Wages',
            'Equipment & Hardware',
            'Software & Licenses',
            'Internet & Connectivity',
            'Office Rent & Utilities',
            'Marketing & Advertising',
            'Maintenance & Repairs',
            'Professional Services',
            'Other Expenses',
        ];

        for ($month = 1; $month <= 12; $month++) {
            $monthlyExpenses[$month] = [
                'total' => 0,
                'categories' => [],
            ];

            foreach ($categories as $category) {
                $amount = 0; // In production, fetch from expenses table
                $monthlyExpenses[$month]['categories'][$category] = $amount;
                $monthlyExpenses[$month]['total'] += $amount;

                if (!isset($categoryTotals[$category])) {
                    $categoryTotals[$category] = 0;
                }
                $categoryTotals[$category] += $amount;
            }
        }

        $yearlyTotal = array_sum(array_column($monthlyExpenses, 'total'));
        $averageMonthly = $yearlyTotal / 12;

        return view('panels.admin.reports.yearly.expenses', compact(
            'year',
            'monthlyExpenses',
            'categoryTotals',
            'categories',
            'yearlyTotal',
            'averageMonthly'
        ));
    }

    /**
     * Export yearly report to Excel
     */
    public function exportExcel(Request $request, string $reportType)
    {
        $year = $request->input('year', Carbon::now()->year);
        
        // Implementation would use ExcelExportService
        // For now, return a placeholder
        return response()->json([
            'message' => 'Excel export for ' . $reportType . ' will be implemented',
            'year' => $year
        ]);
    }

    /**
     * Export yearly report to PDF
     */
    public function exportPdf(Request $request, string $reportType)
    {
        $year = $request->input('year', Carbon::now()->year);
        
        // Implementation would use PdfService
        // For now, return a placeholder
        return response()->json([
            'message' => 'PDF export for ' . $reportType . ' will be implemented',
            'year' => $year
        ]);
    }
}
