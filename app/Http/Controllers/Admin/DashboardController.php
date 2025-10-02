<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Contact;
use App\Models\Contacts;
use App\Models\GhlUser;
use App\Models\Message;
use App\Models\Opportunity;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserPermission;
use App\Models\UserScriptPermission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use App\Helpers\formatMonetaryValue;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = login_id();
        $permissions = UserPermission::where('user_id', $userId)
            ->distinct('module')  // only unique modules
            ->get(['module']);
        $scriptPermissions = UserScriptPermission::where('user_id', $userId)->pluck('permission');
        $totalPermissions = count($permissions);

        // Announcements
        $totalannouncements = Announcement::where('user_id', $userId)->count();
        $announcements = Announcement::where('user_id', $userId)
            ->latest()
            ->limit(3)
            ->get();

        // Contacts
        $totalContactbutton = Contacts::where('user_id', $userId)->count();
        $Contactbutton = Contacts::where('user_id', $userId)
            ->latest()
            ->limit(3)
            ->get();
        //  super admin 
         $users=User::where('status',1)->get();
        $activeuser = User::where('status', 1)->count();
        // dd($activeuser);
        return view('admin.dashboard', compact(
            'totalPermissions',
            'scriptPermissions',
            'announcements',
            'totalannouncements',
            'Contactbutton',
            'users',
            'totalContactbutton',
            'activeuser'
        ));

    }

    public function getDashboardData(Request $request)
    {

        $data = $this->fetchDashboardData($request);
        return response()->json($data);
    }

    public function getSalesRecordData(Request $request)
    {
        $dateRange = $request->get('customDateRange', '');
        [$startDate, $endDate] = $this->parseDateRange($dateRange);

        $authUser = auth()->user();
        $user = User::find($authUser->id);
        $assignedUserIds = $this->getFilteredUserIds($user, $request->input('location_user'));

        $query = GhlUser::whereIn('user_id', $assignedUserIds)
            ->select(['id', 'first_name', 'last_name'])
            ->withCount([
                'contacts' => fn($q) => $q->whereBetween('contacts.date_added', [$startDate, $endDate]),
                'wonOpportunities' => fn($q) => $q->whereBetween('opportunities.date_added', [$startDate, $endDate]),
            ])
            ->withSum(['wonOpportunities' => fn($q) => $q->whereBetween('opportunities.date_added', [$startDate, $endDate])], 'monetary_value')
            ->orderByDesc('won_opportunities_sum_monetary_value');
        return DataTables::of($query)
            ->addColumn('name', fn($user) => $user->first_name . ' ' . $user->last_name)
            ->addColumn('sale', fn($user) => '$' . formatMonetaryValue($user->won_opportunities_sum_monetary_value, 2))
            ->addColumn('won', fn($user) => $user->won_opportunities_count)
            ->addColumn('ratio', function ($user) {
                $contacts = $user->contacts_count;
                $won = $user->won_opportunities_count;
                return $contacts > 0 ? round(($won / $contacts) * 100, 2) . '%' : '0%';
            })
            ->rawColumns(['name', 'sale', 'won', 'ratio'])
            ->make(true);
    }

    private function fetchDashboardData(Request $request)
    {
        $dateRange = $request->get('customDateRange', '');
        [$startDate, $endDate] = $this->parseDateRange($dateRange);

        $authUser = auth()->user();
        $user = User::find($authUser->id);
        $userLocationId = $user->location_id;
        $assignedUserIds = $this->getFilteredUserIds($user, $request->input('location_user'));

        $stats = $this->getDashboardStats($userLocationId, $assignedUserIds, $startDate, $endDate);
        return [
            'stats' => $stats,
            'chartData' => $this->getChartData($userLocationId, $assignedUserIds, $startDate, $endDate),
            'topUsers' => $this->getTopUsers($assignedUserIds, $user->role),
            'dateRange' => $dateRange,
        ];
    }

    private function parseDateRange($dateRange)
    {
        if (!empty($dateRange)) {
            try {
                $dates = explode(' - ', $dateRange);
                if (count($dates) === 2) {
                    $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                    return [$startDate, $endDate];
                }
            } catch (\Exception $e) {
                \Log::error('Invalid date range: ' . $e->getMessage());
            }
        }
        return [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()];
    }

    private function getFilteredUserIds($user, $locationUserId)
    {
        if ($locationUserId) {
            $validAgent = User::where('id', $locationUserId)->exists();
            if ($validAgent)
                return collect([$locationUserId]);
        }
        return $this->getAssignedUserIds($user);
    }

    private function getAssignedUserIds($user)
    {
        if ($user->role === 2) {
            return User::where('assigned_to', $user->id)->pluck('id');
        } elseif ($user->role === 3) {
            return collect([$user->id]);
        }
        return collect();
    }

    private function getDashboardStats($userLocationId, $assignedUserIds, $startDate, $endDate)
    {
        $assignedGhlUserIds = GhlUser::whereIn('user_id', $assignedUserIds)->pluck('ghl_user_id');

        $stats = [
            'totalWonValueRaw' => 0,
            'outboundCallCount' => 0,
            'totalCallTalkTime' => 0,
            'conversionRatio' => 0,
            'totalGoal' => 0,
            'totalpoliciesGoal' => 0,
            'wonOpportunities' => 0,
            'progressPercent' => 0,
            'progressPercentPolicies' => 0,
        ];

        if ($assignedGhlUserIds->isNotEmpty()) {
            $settings = Setting::whereIn('key', ['policies', 'premium'])->pluck('value', 'key')->toArray();
            $policyValue = floatval($settings['policies'] ?? 0);
            $premiumValue = floatval($settings['premium'] ?? 0);

            $userCount = $assignedGhlUserIds->count();
            $now = Carbon::now();
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();

            $isFullMonth = $start->isSameDay($now->startOfMonth()) && $end->isSameDay($now->endOfMonth());

            if ($isFullMonth) {
                $stats['totalpoliciesGoal'] = round($userCount * $policyValue);
                $stats['totalGoal'] = round($userCount * $premiumValue);
            } else {
                $daysInRange = $start->diffInDays($end) + 1;
                $daysInMonth = $now->daysInMonth;
                $stats['totalpoliciesGoal'] = round(($userCount * $policyValue / $daysInMonth) * $daysInRange, 2);
                $stats['totalGoal'] = round(($userCount * $premiumValue / $daysInMonth) * $daysInRange, 2);
            }

            $stats['totalWonValueRaw'] = Opportunity::where('status', 'won')
                ->whereIn('assigned_to', $assignedGhlUserIds)
                ->whereBetween('date_added', [$startDate, $endDate])
                ->sum('monetary_value');

            $stats['wonOpportunities'] = Opportunity::where('status', 'won')
                ->whereIn('assigned_to', $assignedGhlUserIds)
                ->whereBetween('date_added', [$startDate, $endDate])
                ->count();

            $stats['outboundCallCount'] = Message::where('type', 'OutboundMessage')
                ->where('message_type', 'CALL')
                ->whereIn('assigned_to', $assignedGhlUserIds)
                ->whereBetween('date_added', [$startDate, $endDate])
                ->count();

            $stats['totalCallTalkTime'] = Message::where('type', 'OutboundMessage')
                ->where('message_type', 'CALL')
                ->whereIn('assigned_to', $assignedGhlUserIds)
                ->whereBetween('date_added', [$startDate, $endDate])
                ->sum('call_duration');

            $totalContacts = Contact::whereIn('assigned_to', $assignedGhlUserIds)
                ->whereBetween('date_added', [$startDate, $endDate])
                ->count();

            $stats['conversionRatio'] = $totalContacts > 0
                ? round(($stats['wonOpportunities'] / $totalContacts) * 100, 2)
                : 0;

            $stats['progressPercent'] = $stats['totalGoal'] > 0
                ? min(($stats['totalWonValueRaw'] / $stats['totalGoal']) * 100, 100)
                : 0;

            $stats['progressPercentPolicies'] = $stats['totalpoliciesGoal'] > 0
                ? min(($stats['wonOpportunities'] / $stats['totalpoliciesGoal']) * 100, 100)
                : 0;
        }

        return $stats;
    }

    private function getChartData($userLocationId, $assignedUserIds, $startDate, $endDate)
    {
        $assignedGhlUserIds = GhlUser::whereIn('user_id', $assignedUserIds)->pluck('ghl_user_id');

        return [
            'email_count' => Contact::whereIn('assigned_to', $assignedGhlUserIds)
                ->whereNotNull('email')
                ->whereBetween('date_added', [$startDate, $endDate])
                ->count(),
            'won_opportunity_count' => Opportunity::whereIn('assigned_to', $assignedGhlUserIds)
                ->where('status', 'won')
                ->whereBetween('date_added', [$startDate, $endDate])
                ->count(),
            'call_count' => Message::whereIn('assigned_to', $assignedGhlUserIds)
                ->whereBetween('date_added', [$startDate, $endDate])
                ->count(),
        ];
    }

    private function getTopUsers($assignedUserIds, $userRole)
    {
        $limit = $userRole === 3 ? 1 : 3;
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        return GhlUser::select(['first_name', 'last_name', 'profile_photo'])
            ->withSum(['wonOpportunities' => fn($q) => $q->whereBetween('opportunities.date_added', [$monthStart, $monthEnd])], 'monetary_value')
            // ->whereIn('user_id', $assignedUserIds)
            ->orderByDesc('won_opportunities_sum_monetary_value')
            ->take($limit)
            ->get();
    }

    private function getDataTableResponse(Request $request)
    {
        $dateRange = $request->get('customDateRange', '');
        [$startDate, $endDate] = $this->parseDateRange($dateRange);

        $authUser = auth()->user();
        $user = User::find($authUser->id);
        $assignedUserIds = $this->getFilteredUserIds($user, $request->input('location_user'));

        $query = GhlUser::select(['id', 'first_name', 'last_name', 'email', 'role', 'ghl_user_id'])
            ->withCount([
                'contacts' => fn($q) => $q->whereBetween('contacts.date_added', [$startDate, $endDate]),
                'opportunities' => fn($q) => $q->whereBetween('opportunities.date_added', [$startDate, $endDate]),
                'OutboundMessage' => fn($q) => $q->whereBetween('messages.date_added', [$startDate, $endDate]),
                'inboundMessage' => fn($q) => $q->whereBetween('messages.date_added', [$startDate, $endDate]),
                'smsMessage' => fn($q) => $q->whereBetween('messages.date_added', [$startDate, $endDate]),
                'wonOpportunities' => fn($q) => $q->where('status', 'won')->whereBetween('opportunities.date_added', [$startDate, $endDate]),
            ])
            ->withSum(['wonOpportunities' => fn($q) => $q->where('status', 'won')->whereBetween('opportunities.date_added', [$startDate, $endDate])], 'monetary_value')
            ->addSelect([
                'note_count' => function ($q) use ($startDate, $endDate) {
                    $q->selectRaw('count(*)')
                        ->from('notes')
                        ->join('contacts', 'contacts.contact_id', '=', 'notes.contact_id')
                        ->whereColumn('contacts.assigned_to', 'ghl_users.ghl_user_id')
                        ->whereBetween('notes.date_added', [$startDate, $endDate]);
                },
            ])
            ->whereIn('user_id', $assignedUserIds)
            ->orderByDesc('won_opportunities_sum_monetary_value');
        return DataTables::of($query)
            ->addColumn('full_name', fn($user) => $user->first_name . ' ' . $user->last_name)
            ->addColumn('contacts_count', fn($user) => $user->contacts_count)
            ->addColumn('opportunities_count', fn($user) => $user->opportunities_count)
            ->addColumn('outbound_message_count', fn($user) => $user->outbound_message_count)
            ->addColumn('inbound_message_count', fn($user) => $user->inbound_message_count)
            ->addColumn('total_won_opportunities', fn($user) => $user->won_opportunities_count)
            ->addColumn('total_won_value', fn($user) => '$' . formatMonetaryValue($user->won_opportunities_sum_monetary_value))
            ->addColumn('sms_message_count', fn($user) => $user->sms_message_count)
            ->addColumn('note_count', fn($user) => $user->note_count)
            ->make(true);
    }

    private function permissions()
    {
        $userId = Auth::user();

        $permissions = UserPermission::where('user_id', $userId)->get(['module', 'permission']);
        $scriptPermissions = UserScriptPermission::where('user_id', $userId)->pluck('permission')->toArray();

        $moduleMap = [];
        foreach ($permissions as $item) {
            $moduleMap[$item->module][] = $item->permission;
        }

        return response()->json([
            'permissions' => $moduleMap,
            'script_permissions' => $scriptPermissions,
        ]);

    }


}
