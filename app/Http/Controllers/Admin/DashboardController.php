<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Post;
use App\Models\Property;
use App\Models\Staff;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Yoeunes\Toastr\Facades\Toastr;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\Period;
use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\Dimension;





class DashboardController extends Controller
{

    public function index()
    {
        $propertyId = '420353366';

    // Set the credentials file path
    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . base_path('storage/app/analytics/service-account-credentials.json'));

    // Initialize the Analytics Data API client
    $client = new BetaAnalyticsDataClient();
        $totalUsers = 0;
    $totalPageViews = 0;
    $countriesData = [];
        $newUsers = 0;


        try {
            // Fetch the analytics data
            $response = $client->runReport([
                'property' => 'properties/' . $propertyId, // Ensure 'properties/' is prefixed
                'dateRanges' => [
                    new DateRange([
                        'start_date' => '14daysAgo',
                        'end_date' => 'today'
                    ]),
                ],
                'metrics' => [
                    new Metric(['name' => 'activeUsers']),
                    new Metric(['name' => 'newUsers']),
                    new Metric(['name' => 'screenPageViews']),
                ],
                'dimensions' => [
                    new Dimension(['name' => 'country']),
                    new Dimension(['name' => 'pageTitle']),
                    new Dimension(['name' => 'streamName']),
    
                ],
            ]);
            
            $pagesData = [];
            foreach ($response->getRows() as $row) {
                $country = $row->getDimensionValues()[0]->getValue();
                $activeUsers = (int) $row->getMetricValues()[0]->getValue();
                $newUsersForCountry = (int) $row->getMetricValues()[1]->getValue();
                $pageViews = (int) $row->getMetricValues()[1]->getValue();
                $pageTitle = $row->getDimensionValues()[1]->getValue();
                $streamName = $row->getDimensionValues()[2]->getValue();
               
    
                // Add the data to countriesData
                $countriesData[] = [
                    'country' => $country,
                    'activeUsers' => $activeUsers,
                    'pageViews' => $pageViews,
                ];
                
                // Increment the count for each pageTitle
        if (isset($pageTitleCounts[$pageTitle])) {
            $pageTitleCounts[$pageTitle]++;
        } else {
            $pageTitleCounts[$pageTitle] = 1;
        }
    
    
    
                // Sum up total users and total page views
                $totalUsers += $activeUsers;
                $newUsers += $newUsersForCountry;
                $totalPageViews += $pageViews;
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
        // $pageTitleCounts = [];
        $propertycount = Property::count();
        $postcount     = Post::count();
        $staffcount  = Staff::count();
        $usercount     = User::count();

        $properties    = Property::latest()->with('user')->take(5)->get();
        $posts         = Post::latest()->withCount('comments')->take(5)->get();
        $users         =  User::take(10)->get();
        // $comments      = Comment::with('users')->take(5)->get();
        // dd($posts);
        return view('admin.dashboard', compact(
            'propertycount',
            'usercount',
            'postcount',
            'staffcount',
            'properties',
            'posts',
            'users',
            'countriesData',
            'totalUsers',
            'totalPageViews',
            'newUsers',
            'pageTitleCounts'
        ));
    }

    public function changePassword()
    {
        return view('admin.settings.changepassword');
    }

    public function changePasswordUpdate(Request $request)
    {
        if (!(Hash::check($request->get('currentpassword'), Auth::guard('admin')->user()->password))) {

            Toastr::error('message', 'Your current password does not matches with the password you provided! Please try again.');
            return redirect()->back();
        }
        if (strcmp($request->get('currentpassword'), $request->get('newpassword')) == 0) {

            Toastr::error('message', 'New Password cannot be same as your current password! Please choose a different password.');
            return redirect()->back();
        }

        $this->validate($request, [
            'currentpassword' => 'required',
            'newpassword' => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::guard('admin')->user();
        $user->password = bcrypt($request->get('newpassword'));
        $user->save();

        Toastr::success('message', 'Password changed successfully.');
        return redirect()->route('admin.dashboard');
    }
    public function profile()
    {
        $profile = Auth::guard('admin')->user();
        return view('admin.settings.profile', compact('profile'));
    }
    public function profileUpdate(Request $request)
    {
        $request->validate([
            'name'      => 'required',
            'email'     => 'required|email',
            'image'     => 'image|mimes:jpeg,jpg,png'
        ]);

        $user = Admin::find(Auth::guard('admin')->id());

        $image = $request->file('image');
        if (isset($image)) {
            $currentDate = Carbon::now()->toDateString();
            $imagename = 'admin-' . Auth::guard('staff')->id() . '-' . $currentDate . '.' . $image->getClientOriginalExtension();

            if (!Storage::disk('public')->exists('admin')) {
                Storage::disk('public')->makeDirectory('admin');
            }
            if (Storage::disk('public')->exists('admin/' . $user->image) && $user->image != 'default.png') {
                Storage::disk('public')->delete('admin/' . $user->image);
            }
            // $userimage = $image::make($image)->stream();
            $userimage = Image::make($image)->stream();
            Storage::disk('public')->put('admin/' . $imagename, $userimage);
        } else {
            $imagename = $user->image;
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->image = $imagename;

        $user->save();
        return redirect()->route('admin.dashboard');
    }
}
