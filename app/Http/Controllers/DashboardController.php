<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Auth middleware is already applied in web.php
    }

    /**
     * Show the dashboard.
     */
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();

        // Get recent transactions
        $recentTransactions = $user->transactions()
            ->latest('date')  // Order by date instead of created_at
            ->take(5)
            ->get();

        // Calculate monthly totals
        $currentMonth = now();

        $monthlyIncome = $user->transactions()
            ->whereYear('date', $currentMonth->year)
            ->whereMonth('date', $currentMonth->month)
            ->where('type', 'income')
            ->sum('amount');

        $monthlyExpenses = $user->transactions()
            ->whereYear('date', $currentMonth->year)
            ->whereMonth('date', $currentMonth->month)
            ->where('type', 'expense')
            ->sum('amount');

        // Get transaction history for trends (last 6 months)
        $transactionTrends = $user->transactions()
            ->selectRaw("strftime('%Y-%m', date) as month,
                SUM(CASE WHEN type = 'income' THEN amount ELSE -amount END) as total")
            ->where('date', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Prepare data for income vs expenses pie chart
        $distributionData = [
            ['label' => 'Income', 'value' => $monthlyIncome],
            ['label' => 'Expenses', 'value' => abs($monthlyExpenses)]
        ];

        return view('dashboard', compact(
            'recentTransactions',
            'monthlyIncome',
            'monthlyExpenses',
            'transactionTrends',
            'distributionData'
        ));
    }
}
