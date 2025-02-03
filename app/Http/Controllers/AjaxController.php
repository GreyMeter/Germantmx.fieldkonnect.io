<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use DataTables;
use Validator;
use Gate;
use App\Models\{AdditionalPrice, Pincode, City, District, State, Country, Customers, Category, Product, Address, Attachment, Attendance, Brand, Order, Status, Settings, Tasks, ProductDetails, Sales, UserReporting, CheckIn, Complaint, ComplaintTimeline, ComplaintWorkDone, CustomerDetails, DealerAppointment, DealerAppointmentKyc, EndUser, Expenses, GiftModel, GiftSubcategory, Notes, OrderSchemeDetail, PrimarySales, Redemption, SchemeDetails, ServiceBill, ServiceChargeCategories, ServiceChargeProducts, Services, Subcategory, TourProgramme, TransactionHistory, UnitMeasure, User, UserCityAssign, WarrantyActivation};
use App\Models\UserLiveLocation;
use App\Models\UserActivity;
use App\Http\Controllers\SendNotifications;
use Carbon\Carbon;
use LDAP\Result;

class AjaxController extends Controller
{


    public function getState(Request $request)
    {
        try {
            $country = $request->input('country_id');
            $states = State::where(function ($query) use ($country) {
                if (isset($country)) {
                    $query->where('country_id', '=', $country);
                }
                $query->where('active', '=', 'Y');
            })
                ->select('id', 'state_name')->orderBy('state_name', 'asc')->get();
            return response()->json($states);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getDistrict(Request $request)
    {
        try {
            $state = $request->input('state_id');
            $district = District::where(function ($query) use ($state) {
                if (isset($state)) {
                    $query->where('state_id', '=', $state);
                }
                $query->where('active', '=', 'Y');
            })
                ->select('id', 'district_name')->orderBy('district_name', 'asc')->get();
            return response()->json($district);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getCity(Request $request)
    {
        try {
            $district = $request->input('district_id');
            $cities = City::where(function ($query) use ($district) {
                if (isset($district)) {
                    $query->where('district_id', '=', $district);
                }
                $query->where('active', '=', 'Y');
            })
                ->select('id', 'city_name')->orderBy('city_name', 'asc')->get();
            return response()->json($cities);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getPincode(Request $request)
    {
        try {
            $city = $request->input('city_id');
            $cities = Pincode::where(function ($query) use ($city) {
                if (isset($city)) {
                    $query->where('city_id', '=', $city);
                }
                $query->where('active', '=', 'Y');
            })
                ->select('id', 'pincode')->orderBy('pincode', 'asc')->get();
            return response()->json($cities);
        } catch (\Exception $e) {
            return $e;
        }
    }


    public function getAddressData(Request $request)
    {
        try {
            $pincode = $request->input('pincode_id');
            $data = Pincode::with('cityname', 'cityname.districtname', 'cityname.districtname.statename', 'cityname.districtname.statename.countryname')->where('id', '=', $pincode)->select('id', 'city_id')->first();
            $address = collect([
                'city_id' => isset($data['city_id']) ? $data['city_id'] : '',
                'city_name' => isset($data['cityname']['city_name']) ? $data['cityname']['city_name'] : '',
                'district_id' => isset($data['cityname']['district_id']) ? $data['cityname']['district_id'] : '',
                'district_name' => isset($data['cityname']['districtname']['district_name']) ? $data['cityname']['districtname']['district_name'] : '',
                'state_id' => isset($data['cityname']['districtname']['state_id']) ? $data['cityname']['districtname']['state_id'] : '',
                'state_name' => isset($data['cityname']['districtname']['statename']['state_name']) ? $data['cityname']['districtname']['statename']['state_name'] : '',
                'country_id' => isset($data['cityname']['districtname']['statename']['country_id']) ? $data['cityname']['districtname']['statename']['country_id'] : '',
                'country_name' => isset($data['cityname']['districtname']['statename']['countryname']['country_name']) ? $data['cityname']['districtname']['statename']['countryname']['country_name'] : '',
            ]);
            return response()->json($address);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getAddressInfo(Request $request)
    {
        try {
            $address_id = $request->input('address_id');
            $data = Address::with('cityname', 'districtname', 'statename', 'countryname', 'pincodename')->where('id', '=', $address_id)->select('id', 'address1', 'address2', 'landmark', 'locality', 'country_id', 'state_id', 'district_id', 'city_id', 'pincode_id')->first();
            $address = $data['address1'] . ' ' . $data['address2'] . ' ' . $data['landmark'] . ' ' . $data['locality'] . ' ' . $data['cityname']['city_name'] . ' ' . $data['districtname']['district_name'] . ' ' . $data['statename']['state_name'] . ' ' . $data['countryname']['country_name'] . $data['pincodename']['pincode'];
            return response()->json($address);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getUserInfo(Request $request)
    {
        try {
            $user_id = $request->input('user_id');
            $data = User::where('id', '=', $user_id)
                ->select('id', 'name', 'mobile')
                ->first();

            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getCustomerData(Request $request)
    {
        try {
            $customer_id = $request->input('customer_id');
            $data = Customers::with('customeraddress', 'addresslists')
                ->where('id', '=', $customer_id)
                ->select('id', 'name', 'first_name', 'last_name', 'mobile', 'email', 'customertype')
                ->first();
            $addresslists = collect([]);
            if ($data['addresslists']) {
                foreach ($data['addresslists'] as $key => $rows) {
                    $addresslists->push([
                        'address_id' => $rows['id'],
                        'address1' => $rows['address1'],
                        'address2' => $rows['address2'],
                        'landmark' => $rows['landmark'],
                        'locality' => $rows['locality'],
                    ]);
                }
            }
            $customer = collect([
                'name' => isset($data['name']) ? $data['name'] : '',
                'first_name' => isset($data['first_name']) ? $data['first_name'] : '',
                'last_name' => isset($data['last_name']) ? $data['last_name'] : '',
                'mobile' => isset($data['mobile']) ? $data['mobile'] : '',
                'customertype' => isset($data['customertype']) ? $data['customertype'] : '',
                'email' => isset($data['email']) ? $data['email'] : '',
                'address1' => isset($data['customeraddress']['address1']) ? $data['customeraddress']['address1'] . ' ' . $data['customeraddress']['address2'] . ' ' . $data['customeraddress']['landmark'] . ' ' . $data['customeraddress']['locality'] : '',
                'address2' => isset($data['customeraddress']['cityname']['city_name']) ? $data['customeraddress']['cityname']['city_name'] . ', ' . $data['customeraddress']['pincodename']['pincode'] : '',
                'addresslists' => $addresslists
            ]);
            return response()->json($customer);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getCategoryData(Request $request)
    {
        try {
            $data = Category::where(function ($query) {
                $query->where('active', '=', 'Y');
            })
                ->select('id', 'category_name', 'category_image')
                ->orderBy('category_name', 'asc')
                ->get();
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getSubCategoryData(Request $request)
    {
        try {
            $data = Subcategory::where(function ($query) {
                $query->where('active', '=', 'Y');
            });
            if ($request->cat_id && $request->cat_id != null && $request->cat_id != '') {
                $data->where('category_id', $request->cat_id);
            }
            $data = $data->select('id', 'subcategory_name')
                ->orderBy('subcategory_name', 'asc')
                ->get();
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getProductData(Request $request)
    {
        try {
            $sub_category = $request->sub_cat;
            $category = $request->category;
            $data = Product::where(function ($query) use ($sub_category, $category) {
                if ($sub_category && $sub_category != null) {
                    $query->where('subcategory_id', $sub_category);
                }
                if ($category && $category != null) {
                    $query->where('category_id', $category);
                }
                $query->where('active', '=', 'Y');
            })
                ->select('id', 'product_name', 'product_image', 'display_name', 'product_code')
                ->orderBy('product_name', 'asc')
                ->get();
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }
    public function getUserList(Request $request)
    {
        try {
            session()->forget('executive_id');
            $beat_id = $request->input('beat_id');
            $payroll = $request->input('payroll');
            $branch_id = $request->input('branch_id');
            $userids = getUsersReportingToAuth();
            $login_userid = Auth::user()->id;
            $all_users = User::all();
            $userinfo = User::where('id', '=', $login_userid)->first();
            $data = User::where(function ($query) use ($beat_id, $userids, $payroll, $userinfo) {
                if (isset($beat_id)) {
                    $query->whereHas('userbeats', function ($query) use ($beat_id) {
                        $query->where('beat_id', '=', $beat_id);
                    });
                }
                if (isset($payroll)) {
                    $query->where('payroll', '=', $payroll);
                }
                if (isset($branch_id)) {
                    $query->where('branch_id', '=', $branch_id);
                }
                if (!$userinfo->hasRole('superadmin') && !$userinfo->hasRole('Admin') && !$userinfo->hasRole('Sub_Admin') && !$userinfo->hasRole('HR_Admin') && !$userinfo->hasRole('HO_Account')  && !$userinfo->hasRole('Sub_Support') && !$userinfo->hasRole('Accounts Order') && !$userinfo->hasRole('Service Admin') && !$userinfo->hasRole('All Customers')) {
                    $query->whereIn('id', $userids);
                }
            })
                ->select('id', 'name', 'mobile', 'first_name', 'last_name', 'employee_codes')
                ->orderBy('name', 'asc')
                ->get();
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }
    public function getUserListAppoint(Request $request)
    {
        try {
            $branch_id = $request->input('branch_id');
            $data = User::where(function ($query) use ($branch_id) {

                if (isset($branch_id)) {
                    $query->where('branch_id', '=', $branch_id);
                }
                $query->where('active', '=', 'Y');
            })
                ->select('id', 'name', 'mobile', 'first_name', 'last_name', 'employee_codes')
                ->orderBy('name', 'asc')
                ->get();
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getRetailerlist(Request $request)
    {
        try {
            $state = $request->input('state_id');
            $district = $request->input('district_id');
            $city = $request->input('city_id');
            $users = $request->input('user_id');
            $data = Customers::where(function ($query) use ($users) {
                // if (isset($users)) {
                //     $query->whereIn('executive_id', $users);
                // }
                $query->where('active', '=', 'Y');
            })
                ->whereHas('customeraddress', function ($query) use ($state, $district, $city) {
                    if (isset($state)) {
                        $query->where('state_id', '=', $state);
                    }
                    if (isset($district)) {
                        $query->where('district_id', '=', $district);
                    }
                    if (isset($city)) {
                        $query->where('city_id', '=', $city);
                    }
                })
                ->select('id', 'name', 'mobile', 'first_name', 'last_name')
                ->orderBy('name', 'asc')
                ->get();
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getProductInfo(Request $request)
    {
        try {
            $product_id = $request->input('product_id');
            $data = Product::with('productdetails', 'categories')
                ->where(function ($query) use ($product_id) {
                    if (isset($product_id)) {
                        $query->where('id', '=', $product_id);
                    }
                    $query->where('active', '=', 'Y');
                })
                ->orderBy('product_name', 'asc')
                ->first();
            $product = collect([
                'id' => isset($data['id']) ? $data['id'] : '',
                'product_name' => isset($data['product_name']) ? $data['product_name'] : '',
                'product_code' => isset($data['product_code']) ? $data['product_code'] : '',
                'specification' => isset($data['specification']) ? $data['specification'] : '',
                'product_no' => isset($data['product_no']) ? $data['product_no'] : '',
                'phase' => isset($data['phase']) ? $data['phase'] : '',
                'product_image' => isset($data['product_image']) ? $data['product_image'] : '',
                'display_name' => isset($data['display_name']) ? $data['display_name'] : '',
                'mrp' => isset($data['productdetails'][0]['mrp']) ? $data['productdetails'][0]['mrp'] : '',
                'price' => isset($data['productdetails'][0]['price']) ? $data['productdetails'][0]['price'] : '',
                'selling_price' => isset($data['productdetails'][0]['selling_price']) ? $data['productdetails'][0]['selling_price'] : '',
                'gst' => isset($data['productdetails'][0]['gst']) ? $data['productdetails'][0]['gst'] : '',
                'discount' => isset($data['productdetails'][0]['discount']) ? $data['productdetails'][0]['discount'] : '',
                'max_discount' => ($data['productdetails'][0]['max_discount']) ? $data['productdetails'][0]['max_discount'] : 0.00,
                'scheme_discount' => $data['getSchemeDetail']['points'] ?? 0.00,
                'repetition' => $data['getSchemeDetail']['orderscheme']['repetition'] ?? '',
                'scheme_name' => $data['getSchemeDetail']['orderscheme']['scheme_name'] ?? '',
                'scheme_type' => $data['getSchemeDetail']['orderscheme']['scheme_type'] ?? '',
                'scheme_value_type' => $data['getSchemeDetail']['orderscheme']['scheme_basedon'] ?? '',
                'minimum' => $data['getSchemeDetail']['orderscheme']['minimum'] ?? 0,
                'maximum' => $data['getSchemeDetail']['orderscheme']['maximum'] ?? 0,
                'start_date' => $data['getSchemeDetail']['orderscheme']['start_date'] ?? 0,
                'end_date' => $data['getSchemeDetail']['orderscheme']['end_date'] ?? 0,

                'productdetails' => $data['productdetails'],
                'categories' => $data['categories']
            ]);


            if ($product['repetition'] == '3' || $product['repetition'] == '4') {
                $start_date = $data['getSchemeDetail']['orderscheme']['start_date'] ?? '';
                $end_date = $data['getSchemeDetail']['orderscheme']['end_date'] ?? '';

                if ($product['repetition'] == '3') {
                    $startCarbon = Carbon::parse($start_date);
                    $endCarbon = Carbon::parse($end_date);
                    $today = Carbon::today();
                    $startDay = $startCarbon->day;
                    $endDay = $endCarbon->day;
                    $todayDay = $today->day;
                    if ($todayDay >= $startDay && $todayDay <= $endDay) {
                    } else {
                        $product['scheme_discount'] = 0.00;
                    }
                }

                if ($product['repetition'] == '4') {
                    $startMonthDay = Carbon::parse($start_date)->format('m-d');
                    $endMonthDay = Carbon::parse($end_date)->format('m-d');
                    $todayMonthDay = Carbon::today()->format('m-d');
                    if (($startMonthDay <= $todayMonthDay && $endMonthDay >= $todayMonthDay) ||
                        ($startMonthDay >= $todayMonthDay && $endMonthDay <= $todayMonthDay)
                    ) {
                    } else {
                        $product['scheme_discount'] = 0.00;
                    }
                }
            }
            if ($product['repetition'] == '2') {
                $currentDate = Carbon::now();
                $weekOfMonth = ceil($currentDate->day / 7);
                $week_repeat = $data['getSchemeDetail']['orderscheme']['week_repeat'] ?? '';
                if ((int)$week_repeat == (int)$weekOfMonth) {
                } else {
                    $product['scheme_discount'] = 0.00;
                }
            }
            if ($product['repetition'] == '1') {
                $day_repeat = explode(',', $data['getSchemeDetail']['orderscheme']['day_repeat']) ?? [];
                $todayDayOfWeek = Carbon::today()->format('D');
                if (in_array($todayDayOfWeek, $day_repeat)) {
                } else {
                    $product['scheme_discount'] = 0.00;
                }
            }
            return response()->json($product);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getProductDetailInfo(Request $request)
    {
        try {
            $productdetail_id = $request->input('productdetail_id');
            $data = ProductDetails::where('id', '=', $productdetail_id)
                ->select('id', 'detail_title', 'mrp', 'price', 'discount', 'max_discount', 'selling_price', 'gst')
                ->first();
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getOrderInfo(Request $request)
    {
        try {
            $order_id = $request->input('order_id');
            $data = Order::with('buyers', 'sellers', 'orderdetails')->where('id', '=', $order_id)->select('id', 'buyer_id', 'seller_id', 'orderno', 'order_date', 'total_gst', 'total_discount', 'extra_discount', 'extra_discount_amount', 'sub_total', 'grand_total', 'address_id')->first();
            $order = collect([
                'buyer_id' => isset($data['buyer_id']) ? $data['buyer_id'] : '',
                'buyer_name' => isset($data['buyers']['name']) ? $data['buyers']['name'] : '',
                'seller_id' => isset($data['seller_id']) ? $data['seller_id'] : '',
                'seller_name' => isset($data['sellers']['name']) ? $data['sellers']['name'] : '',
                'orderno' => isset($data['orderno']) ? $data['orderno'] : '',
                'order_date' => isset($data['order_date']) ? $data['order_date'] : '',
                'total_gst' => isset($data['total_gst']) ? $data['total_gst'] : '',
                'total_discount' => isset($data['total_discount']) ? $data['total_discount'] : '',
                'extra_discount' => isset($data['extra_discount']) ? $data['extra_discount'] : '',
                'extra_discount_amount' => isset($data['extra_discount_amount']) ? $data['extra_discount_amount'] : '',
                'sub_total' => isset($data['sub_total']) ? $data['sub_total'] : '',
                'grand_total' => isset($data['grand_total']) ? $data['grand_total'] : '',
                'address_id' => isset($data['address_id']) ? $data['address_id'] : '',
                'address_name' => isset($data['address']['address1']) ? $data['address']['address1'] . ' ' . $data['address']['address2'] . ' ' . $data['address']['landmark'] . ' ' . $data['address']['locality'] . ' ' . $data['address']['cityname']['city_name'] . ' ' . $data['address']['districtname']['district_name'] . ' ' . $data['address']['statename']['state_name'] . ' ' . $data['address']['countryname']['country_name'] . $data['address']['pincodename']['pincode'] : '',
            ]);
            return response()->json($order);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function uniqueValidation(Request $request)
    {
        $data = DB::table($request['table'])
            ->where(function ($query) use ($request) {
                if (!empty($request['id'])) {
                    $query->where('id', '!=', $request['id']);
                }
                if (!empty($request['customer_id'])) {
                    $query->where('customer_id', '!=', $request['customer_id']);
                }
                $query->where($request['column'], $request['value']);
            })
            ->first();
        if ($data) {
            return response()->json(false);
        } else {
            return response()->json(true);
        }
    }

    public function getCustomerLatLong(Request $request)
    {
        try {
            $data = Customers::where(function ($query) {
                $query->where('active', '=', 'Y');
            })
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->select('id', 'name', 'latitude', 'longitude')
                ->get();
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getUppaidInvouces(Request $request)
    {
        try {
            $customer_id = $request->input('customer_id');
            $data = Sales::where('buyer_id', '=', $customer_id)
                ->whereIn('status_id', [4, 5])
                ->select('id', 'invoice_date', 'invoice_no', 'grand_total', 'order_id', 'status_id', 'paid_amount')
                ->get();
            $sales = collect([]);
            if (!empty($data)) {
                foreach ($data as $key => $rows) {
                    $sales->push([
                        'id' => isset($rows['id']) ? $rows['id'] : '',
                        'invoice_date' => isset($rows['invoice_date']) ? $rows['invoice_date'] : '',
                        'invoice_no' => isset($rows['invoice_no']) ? $rows['invoice_no'] : '',
                        'grand_total' => isset($rows['grand_total']) ? $rows['grand_total'] : '',
                        'amount_unpaid' => isset($rows['paid_amount']) ? $rows['grand_total'] - $rows['paid_amount'] : $rows['grand_total'],
                        'order_id' => isset($rows['order_id']) ? $rows['order_id'] : '',
                        'status_id' => isset($rows['status_id']) ? $rows['status_id'] : '',
                    ]);
                }
            }
            return response()->json($sales);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function dashboardActivity(Request $request)
    {
        try {
            $reporting = UserReporting::where('userid', '=', Auth::user()->id)->select('users')->first();
            if (!empty($request->users)) {
                $users = $request->users;
            } else {
                $users = (!empty($reporting)) ? json_decode($reporting['users']) : [];
                array_push($users, Auth::user()->id);
            }
            $activities = collect([]);
            $customeractivity = Customers::where(function ($query) use ($users) {
                $query->whereDate('created_at', date('Y-m-d'));
                $query->whereIn('created_by', $users);
            })
                ->select('id', 'created_by', 'name', 'created_at')
                ->get();
            if ($customeractivity->isNotEmpty()) {
                $customeractivity->map(function ($item, $key) use ($activities) {
                    $activities->push([
                        'time' => date('H:i', strtotime($item->created_at)),
                        'profile' => isset($item['createdbyname']['profile_image']) ? $item['createdbyname']['profile_image'] : '',
                        'user_name' => isset($item['createdbyname']['name']) ? $item['createdbyname']['name'] : '',
                        'description' => $item['name'] . 'is Added in CRM',
                    ]);
                });
            }
            $checkinactivity = CheckIn::where(function ($query) use ($users) {
                $query->whereDate('checkin_date', date('Y-m-d'));
                $query->whereIn('user_id', $users);
            })
                ->select('customer_id', 'checkin_time', 'user_id')
                ->get();
            if ($checkinactivity->isNotEmpty()) {
                $checkinactivity->map(function ($item, $key) use ($activities) {
                    $activities->push([
                        'time' => date('G:i', strtotime($item->checkin_time)),
                        'profile' => isset($item['users']['profile_image']) ? $item['users']['profile_image'] : '',
                        'user_name' => isset($item['users']['name']) ? $item['users']['name'] : '',
                        'description' => $item['customers']['name'] . ' Counter Visited',
                    ]);
                });
            }
            $ordersactivity = Order::where(function ($query) use ($users) {
                $query->whereDate('order_date', date('Y-m-d'));
                $query->whereIn('created_by', $users);
            })
                ->select('id', 'grand_total', 'created_at', 'buyer_id', 'seller_id', 'created_by')
                ->get();
            if ($ordersactivity->isNotEmpty()) {
                $ordersactivity->map(function ($item, $key) use ($activities) {
                    $activities->push([
                        'time' => date('H:i', strtotime($item->created_at)),
                        'profile' => isset($item['createdbyname']['profile_image']) ? $item['createdbyname']['profile_image'] : '',
                        'user_name' => isset($item['createdbyname']['name']) ? $item['createdbyname']['name'] : '',
                        'description' => 'Received Order from ' . $item['buyers']['name']
                    ]);
                });
            }
            $sorted = $activities->sortByDesc('time');
            $collection = $sorted->values()->all();
            return response()->json($collection);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getUserLocationData(Request $request)
    {
        try {
            $users = $request->input('user_id');
            $date = !empty($request->input('date')) ? $request->input('date') : date('Y-m-d');
            $collection = UserLiveLocation::where(function ($query) use ($users, $date) {
                $query->whereDate('time', $date);
                $query->where('userid', $users);
            })
                ->select('address', 'time', 'latitude', 'longitude')
                ->get();
            return response()->json($collection);
        } catch (\Exception $e) {
            return $e;
        }
    }
    public function getUserActivityData(Request $request)
    {
        try {
            $date = date('Y-m-d', strtotime($request->input('date')));
            $user_id = $request->input('user_id');

            $punchInOut = Attendance::where('user_id', $user_id)->where('punchin_date', $date)->get();
            $checkInOut = CheckIn::with('visitreports')->with('customers')->where('user_id', $user_id)->where('checkin_date', $date)->get();
            $orders = Order::with('customer')->where('created_by', $user_id)->whereRaw('DATE(created_at)="' . $date . '"')->get();
            $customer_add = Customers::with('customeraddress')->where('created_by', $user_id)->whereRaw('DATE(created_at)="' . $date . '"')->get();
            $customer_update = Customers::with('customeraddress')->where('created_by', $user_id)->whereColumn('updated_at', '>', 'created_at')->whereRaw('DATE(updated_at)="' . $date . '"')->get();

            $punchInData = array();
            $punchOutData = array();
            $checkInData = array();
            $checkOutData = array();
            $orderData = array();
            $customerAddData = array();
            $customerUpdateData = array();

            foreach ($punchInOut as $k => $val) {
                if ($val->punchin_time != null) {
                    $punch_in_city = getLatLongToCity($val->punchin_latitude, $val->punchin_longitude);
                    $punchInData[$k]['title'] = 'Punchin';
                    $punchInData[$k]['time'] = $val->punchin_time;
                    $punchInData[$k]['latitude'] = $val->punchin_latitude != null ? $val->punchin_latitude : '';
                    $punchInData[$k]['longitude'] = $val->punchin_longitude != null ? $val->punchin_longitude : '';
                    $punchInData[$k]['msg'] = $val->punchin_summary . ' - ' . $punch_in_city;
                }
                if ($val->punchout_time != null) {
                    $punchOutData[$k]['title'] = 'Punchout';
                    $punchOutData[$k]['time'] = $val->punchout_time;
                    $punchOutData[$k]['latitude'] = $val->punchout_latitude != null ? $val->punchout_latitude : '';
                    $punchOutData[$k]['longitude'] = $val->punchout_longitude != null ? $val->punchout_longitude : '';
                    $punchOutData[$k]['msg'] = $val->punchout_address;
                }
            }

            foreach ($checkInOut as $k => $val) {
                if ($val->checkin_time != null) {
                    $check_in_city = getLatLongToCity($val->checkin_latitude, $val->checkin_longitude);
                    $checkInData[$k]['title'] = 'Checkin';
                    $checkInData[$k]['time'] = $val->checkin_time;
                    $checkInData[$k]['latitude'] = $val->checkin_latitude != null ? $val->checkin_latitude : '';
                    $checkInData[$k]['longitude'] = $val->checkin_longitude != null ? $val->checkin_longitude : '';
                    $checkInData[$k]['msg'] = $val->customers->name . ' - ' . $check_in_city;
                }
                if ($val->checkout_time != null) {
                    $check_out_city = getLatLongToCity($val->checkout_latitude, $val->checkout_longitude);
                    $checkOutData[$k]['title'] = 'Checkout';
                    $checkOutData[$k]['time'] = $val->checkout_time;
                    $checkOutData[$k]['latitude'] = $val->checkout_latitude != null ? $val->checkout_latitude : '';
                    $checkOutData[$k]['longitude'] = $val->checkout_longitude != null ? $val->checkout_longitude : '';
                    $checkOutData[$k]['msg'] = $val->customers->name . ' - ' . $check_out_city . '<br>Remark - ' . $val->visitreports->description;
                }
            }

            foreach ($orders as $k => $val) {
                $orderData[$k]['title'] = 'Order';
                $orderData[$k]['time'] = date('H:i:s', strtotime($val->created_at));
                $orderData[$k]['latitude'] = '';
                $orderData[$k]['longitude'] = '';
                $orderData[$k]['msg'] = $val->customer->name . ' - ' . $val->customer->customeraddress->cityname->city_name . ',<br>Qty : ' . $val->qtys;
            }

            foreach ($customer_add as $k => $val) {
                $customerAddData[$k]['title'] = 'New Customer Registration';
                $customerAddData[$k]['time'] = date('H:i:s', strtotime($val->created_at));
                $customerAddData[$k]['latitude'] = $val->latitude;
                $customerAddData[$k]['longitude'] = $val->longitude;
                if ($val->customeraddress->cityname != null) {
                    $customerAddData[$k]['msg'] = $val->name . ' - ' . $val->customeraddress->cityname->city_name;
                } else {
                    $customerAddData[$k]['msg'] = $val->name . ' - City not enter';
                }
            }

            foreach ($customer_update as $k => $val) {
                $customerUpdateData[$k]['title'] = 'Customer Edit';
                $customerUpdateData[$k]['time'] = date('H:i:s', strtotime($val->created_at));
                $customerUpdateData[$k]['latitude'] = $val->latitude;
                $customerUpdateData[$k]['longitude'] = $val->longitude;
                $customerUpdateData[$k]['msg'] = $val->name . ' - ' . $val->customeraddress->cityname->city_name;
            }

            $collections = array_merge($punchInData, $punchOutData, $checkInData, $checkOutData, $orderData, $customerAddData, $customerUpdateData);

            usort($collections, function ($a, $b) {
                return strtotime($a['time']) - strtotime($b['time']);
            });
            foreach ($collections as $k => $val) {
                $collections[$k]['time'] = date('h:i A', strtotime($val['time']));
            }
            return response()->json($collections);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getCustomerActivityData(Request $request)
    {
        try {
            $notes = Notes::with('customerinfo', 'users')
                ->where(function ($query) use ($request) {
                    if (!empty($request['customer_id'])) {
                        $query->where('customer_id', $request['customer_id']);
                    }
                })
                ->select('id', 'user_id', 'customer_id', 'note', 'purpose', 'status_id', 'created_at', 'callstatus')
                ->latest()
                ->get();
            return response()->json($notes);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function removeSchemesdetails(Request $request)
    {
        try {
            $scheme_details = SchemeDetails::find($request->id);
            $scheme_details->delete();

            return response()->json(["status" => true]);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getCustomerDataSelect(Request $request)
    {
        if ($request->ajax()) {

            $term = trim($request->term);

            $coins = Customers::select("id as id", "name as text")->whereIN('customertype', ['1', '2', '3'])->where('name', 'LIKE',  '%' . $term . '%')->orderBy('id', 'asc')->simplePaginate(10);


            $morePages = true;
            $pagination_obj = json_encode($coins);
            if (empty($coins->nextPageUrl())) {
                $morePages = false;
            }
            $results = array(
                "results" => $coins->items(),
                "pagination" => array(
                    "more" => $morePages
                )
            );
            return response()->json($results);
        }
    }

    public function getProductDataSelect(Request $request)
    {
        if ($request->ajax()) {

            $term = trim($request->term);

            $coins = Product::select("id as id", "product_name as text")->where('product_name', 'LIKE',  '%' . $term . '%')->orderBy('id', 'asc')->simplePaginate(10);


            $morePages = true;
            $pagination_obj = json_encode($coins);
            if (empty($coins->nextPageUrl())) {
                $morePages = false;
            }
            $results = array(
                "results" => $coins->items(),
                "pagination" => array(
                    "more" => $morePages
                )
            );
            return response()->json($results);
        }
    }

    public function getStateDataSelect(Request $request)
    {
        if ($request->ajax()) {

            $term = trim($request->term);

            $coins = State::select("id as id", "state_name as text")->where('state_name', 'LIKE',  '%' . $term . '%')->orderBy('id', 'asc')->simplePaginate(10);


            $morePages = true;
            $pagination_obj = json_encode($coins);
            if (empty($coins->nextPageUrl())) {
                $morePages = false;
            }
            $results = array(
                "results" => $coins->items(),
                "pagination" => array(
                    "more" => $morePages
                )
            );
            return response()->json($results);
        }
    }

    public function getDealerDisDataSelect(Request $request)
    {
        if ($request->ajax()) {

            $term = trim($request->term);

            $coins = Customers::select("id as id", "name as text")->whereIN('customertype', ['1', '3'])->where('name', 'LIKE',  '%' . $term . '%')->orderBy('id', 'asc')->simplePaginate(10);


            $morePages = true;
            $pagination_obj = json_encode($coins);
            if (empty($coins->nextPageUrl())) {
                $morePages = false;
            }
            $results = array(
                "results" => $coins->items(),
                "pagination" => array(
                    "more" => $morePages
                )
            );
            return response()->json($results);
        }
    }

    public function getRetailerDataSelect(Request $request)
    {
        if ($request->ajax()) {

            $term = trim($request->term);

            $coins = Customers::select("id as id", "name as text")
                ->where('customertype', '2')
                ->where(function ($query) use ($term) {
                    $query->where('name', 'LIKE', '%' . $term . '%')
                        ->orWhere('mobile', 'LIKE', '%' . $term . '%');
                })
                ->orderBy('id', 'asc')
                ->simplePaginate(10);


            $morePages = true;
            $pagination_obj = json_encode($coins);
            if (empty($coins->nextPageUrl())) {
                $morePages = false;
            }
            $results = array(
                "results" => $coins->items(),
                "pagination" => array(
                    "more" => $morePages
                )
            );
            return response()->json($results);
        }
    }


    public function changeDocumnetStatus(Request $request)
    {
        if ($request->ajax()) {

            $column = $request->type;
            $customer_id = $request->customer_id;
            $status = $request->status;
            $update = CustomerDetails::where('customer_id', $customer_id)->update([$column => $status, 'status_update_by' => auth()->user()->id]);
            if ($request->status == '2') {
                switch ($request->type) {
                    case 'aadhar_no_status':
                        $update = CustomerDetails::where('customer_id', $customer_id)->update(['aadhar_no' => NULL, 'status_update_by' => auth()->user()->id]);
                        Attachment::where('customer_id', $customer_id)->where('document_name', 'aadhar')->delete();
                        Attachment::where('customer_id', $customer_id)->where('document_name', 'aadharback')->delete();
                        break;

                    case 'gstin_no_status':
                        $update = CustomerDetails::where('customer_id', $customer_id)->update(['gstin_no' => NULL, 'status_update_by' => auth()->user()->id]);
                        Attachment::where('customer_id', $customer_id)->where('document_name', 'gstin')->delete();
                        break;

                    case 'pan_no_status':
                        $update = CustomerDetails::where('customer_id', $customer_id)->update(['pan_no' => NULL, 'status_update_by' => auth()->user()->id]);
                        Attachment::where('customer_id', $customer_id)->where('document_name', 'pan')->delete();
                        break;

                    case 'bank_status':
                        $update = CustomerDetails::where('customer_id', $customer_id)->update(['account_holder' => NULL, 'account_number' => NULL, 'bank_name' => NULL, 'ifsc_code' => NULL]);
                        Attachment::where('customer_id', $customer_id)->where('document_name', 'bankpass')->delete();
                        break;

                    case 'otherid_no_status':
                        $update = CustomerDetails::where('customer_id', $customer_id)->update(['otherid_no' => NULL]);
                        Attachment::where('customer_id', $customer_id)->where('document_name', 'other')->delete();
                        break;

                    default:

                        break;
                }
            }
            $customer = Customers::with('customerdetails')->find($customer_id);
            if ($update) {
                if ($status == 1) {
                    $msg = "Verified Successfully !!";
                    $title = 'KYC Approval';
                    $pmsg = 'KYC is Approved ✅';
                } elseif ($status == 2) {
                    $msg = "Rejected Successfully !!";
                    $title = 'KYC Rejection 🚫';
                    $pmsg = 'KYC is Rejected';
                } else {
                    $msg = "";
                }
                $noti_data = [
                    'fcm_token' =>  $customer->customerdetails->fcm_token,
                    'title' => $title,
                    'msg' => $pmsg,
                ];
                $send_notification = SendNotifications::send($noti_data);
                $results = array(
                    "status" => true,
                    "msg" => $msg
                );
            } else {
                $results = array(
                    "status" => false,
                    "msg" => "Somthing went wrong"
                );
            }
            return response()->json($results);
        }
    }

    public function getGiftSubCategoryData(Request $request)
    {
        try {
            $data = GiftSubcategory::where(function ($query) {
                $query->where('active', '=', 'Y');
            });
            if ($request->cat_id && $request->cat_id != null && $request->cat_id != '') {
                $data->where('category_id', $request->cat_id);
            }
            $data = $data->select('id', 'subcategory_name')
                ->orderBy('subcategory_name', 'asc')
                ->get();
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getExpensesData(Request $request)
    {
        if ($request->ajax()) {

            $term = trim($request->term);

            $coins = Expenses::select("id as id", "id as text")->where('id', 'LIKE',  '%' . $term . '%')->orderBy('id', 'desc')->simplePaginate(10);


            $morePages = true;
            $pagination_obj = json_encode($coins);
            if (empty($coins->nextPageUrl())) {
                $morePages = false;
            }
            $results = array(
                "results" => $coins->items(),
                "pagination" => array(
                    "more" => $morePages
                )
            );
            return response()->json($results);
        }
    }

    public function getGiftModelData(Request $request)
    {
        try {
            $data = GiftModel::where(function ($query) {
                $query->where('active', '=', 'Y');
            });
            if ($request->cat_id && $request->cat_id != null && $request->cat_id != '') {
                $data->where('sub_category_id', $request->cat_id);
            }
            $data = $data->select('id', 'model_name')
                ->orderBy('model_name', 'asc')
                ->get();
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getBankdetailandPoints(Request $request)
    {
        try {
            $shop_img = Customers::where('id', $request->cust_id)->value('profile_image');
            $customer_bank_details = CustomerDetails::select('account_number', 'account_holder', 'ifsc_code', 'bank_name', 'bank_status')->where('customer_id', $request->cust_id)->first();
            $customer_aadhar_details = CustomerDetails::select('aadhar_no', 'aadhar_no_status')->where('customer_id', $request->cust_id)->first();
            $thistorys = TransactionHistory::where('customer_id', $request->cust_id)->get();
            $active_points = 0;
            $provision_points = 0;
            foreach ($thistorys as $thistory) {
                if ($thistory->status == '1') {
                    $active_points += $thistory->point;
                } else {
                    $active_points += $thistory->active_point;
                    $provision_points += $thistory->provision_point;
                }
            }
            $total_redemption = Redemption::where('customer_id', $request->cust_id)->whereNot('status', '2')->sum('redeem_amount') ?? 0;
            $total_balance = (int)$active_points - (int)$total_redemption;

            $data['bank_details'] = $customer_bank_details;
            $data['aadhar_details'] = $customer_aadhar_details;
            $data['Total_points'] = $total_balance;
            $data['shop_img'] = $shop_img;

            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getProductByCoupon(Request $request)
    {
        try {
            $serial_no = $request->serial_no;
            $serial_no_product_code = Services::where('serial_no', $serial_no)->value('product_code');
            $all_products = Product::all();
            $html = '<option value="">Select Product</option>';
            $slected = false;
            foreach ($all_products as $product) {
                if ($serial_no_product_code && $product->product_code == $serial_no_product_code && $serial_no_product_code != null && $serial_no_product_code != '') {
                    $html .= '<option value="' . $product->id . '" selected>' . $product->product_name . '</option> ';
                    $slected = true;
                } else {
                    $html .= '<option value="' . $product->id . '">' . $product->product_name . '</option> ';
                }
            }
            $data['status'] = true;
            $data['html'] = $html;
            $data['slected'] = $slected;
            return response()->json($data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getTourPlanByUserAndDate(Request $request)
    {
        try {
            $data = TourProgramme::where('date', $request->date)->where('userid', $request->user_id)->first();
            if ($data && $data != NULL && !empty($data)) {
                $response = ['status' => true, 'data' => $data];
                return response()->json($response);
            } else {
                $response = ['status' => false, 'data' => $data];
                return response()->json($response);
            }
        } catch (\Exception $e) {
            return $e;
        }
    }
    public function userCityList(Request $request)
    {
        try {
            $cityname = $request->input('cityname');
            $user_id = $request->user()->id;
            $cityids = UserCityAssign::where('userid', '=', $user_id)->pluck('city_id')->toArray();
            //$data = City::whereIn('id',$cityids)->select('id','city_name', 'grade')->orderBy('city_name','asc')->get();

            $data = City::whereIn('id', $cityids)->select('id', 'city_name', 'grade');
            if ($cityname) {
                $data->where('city_name', 'LIKE', trim($cityname) . '%');
            }
            $data = $data->orderBy('city_name', 'asc')->get();

            if ($data->isNotEmpty()) {
                return response()->json(['status' => 'success', 'message' => 'Data retrieved successfully.', 'data' => $data], 200);
            }
            return response(['status' => 'error', 'message' => 'No Record Found.', 'data' => $data], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getProductInfoBySerialNo(Request $request)
    {
        try {
            $serial_no = $request->input('serial_no');
            if ($serial_no != NULL && $serial_no != '') {
                $data = Services::with('product')
                    ->where(function ($query) use ($serial_no) {
                        if (isset($serial_no)) {
                            $query->where('serial_no', '=', $serial_no);
                        }
                    })
                    ->first();
                if ($data) {
                    $data->product->categories = $data->product->categories;
                    $check_Warranty = WarrantyActivation::with('media', 'customer', 'seller_details')->where('status', '!=', '3')->where('product_serail_number', $serial_no)->first();
                    return response()->json(['status' => true, 'data_all' => $data, 'data' => $data->product, 'check_Warranty' => $check_Warranty]);
                } else {
                    return response()->json(['status' => false, 'data' => null]);
                }
            } else {
                return response()->json(['status' => false, 'data' => null]);
            }
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getEndUserData(Request $request)
    {
        try {
            $customer_number = $request->input('customer_number');
            if (isset($customer_number)) {
                $data = EndUser::where(function ($query) use ($customer_number) {
                    $query->where('customer_number', '=', $customer_number);
                })
                    ->first();
                if ($data) {
                    return response()->json(['status' => true, 'data' => $data]);
                } else {
                    return response()->json(['status' => false, 'data' => null]);
                }
            } else {
                return response()->json(['status' => false, 'data' => null]);
            }
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getComplaintsData(Request $request)
    {
        try {
            $search = $request->input('search');
            $end_user = EndUser::where('customer_number', $search)->first();
            $data = Complaint::where(function ($query) use ($search, $end_user) {
                if (isset($search)) {
                    $query->where('product_serail_number', '=', $search);
                }
                if (isset($end_user) && $end_user && $end_user != NULL) {
                    $query->orwhere('end_user_id', '=', $end_user->id);
                }
            })
                ->get();
            if (count($data) > 0) {
                $html = '';
                foreach ($data as $val) {
                    $html .= '<tr><td>';
                    $html .= $val->complaint_number;
                    $html .= '</td><td>';
                    $html .= date('d M Y', strtotime($val->complaint_date));
                    $html .= '</td><td>';
                    $html .= strtoupper($val->product_serail_number);
                    $html .= '</td><td>';
                    $html .= $val->claim_amount;
                    $html .= '</td><td>';
                    if ($val->complaint_status == '0') {
                        $html .= 'Open';
                    } elseif ($val->complaint_status == '1') {
                        $html .= 'Pending';
                    }

                    $html .= '</td><td>';
                    $html .= $val->service_center_details ? $val->service_center_details->name : '';
                    $html .= '</td><td>';
                    $html .= $val->seller_details ? $val->seller_details->name : '';
                    $html .= '</td><td>';
                    $html .= $val->party ? $val->party->name : '';
                    $html .= '</td><td>';
                    $html .= '</td><tr>';
                }
                return response()->json(['status' => true, 'data' => $html]);
            } else {
                $data = '<tr><td class="text-center" colspan="8">No record Found</td></tr>';
                return response()->json(['status' => false, 'data' => $data]);
            }
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function fetchPieChartData()
    {
        $labels = ['Active', 'Provision', 'Redeem'];
        $total_points = TransactionHistory::sum('point') ?? 0;
        $active_points = TransactionHistory::where('status', '1')->sum('point') ?? 0;
        $provision_points = TransactionHistory::where('status', '0')->sum('point') ?? 0;
        $total_redemption = Redemption::whereNot('status', '2')->sum('redeem_amount') ?? 0;
        $total_rejected = Redemption::where('status', '2')->sum('redeem_amount') ?? 0;
        $total_balance = (int)$active_points - (int)$total_redemption;
        $values = [$active_points, $provision_points, $total_redemption];

        return response()->json([
            'labels' => $labels,
            'values' => $values
        ]);
    }

    public function remove_session(Request $request)
    {
        $request->session()->forget('executive_id');
        return response()->json(['status' => 'success']);
    }

    public function getComplaintsDataProduct(Request $request)
    {
        $complaint = Complaint::with('createdbyname')->where('complaint_number', $request->complaint_number)->first();
        $service_bill = ServiceBill::where('complaint_no', $request->complaint_number)->first();

        $product = $complaint->product_details ?? '';

        $data['complaint'] = $complaint;
        $data['product'] = $product;
        $data['service_bill'] = $service_bill;

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    public function getServiceCategory(Request $request)
    {
        if ($request->ajax()) {

            $data = ServiceChargeCategories::where('division_id', $request->division_id)->get();

            return response()->json($data);
        }
    }

    public function getPrimaryTotal(Request $request)
    {
        $query = PrimarySales::query();
        if ($request->user_id && $request->user_id != '' && $request->user_id != null) {
            $usersIds = User::where('id', $request->user_id)->where('sales_type', 'Secondary')->pluck('id');
        } else {
            $usersIds = User::with('attendance_details')->where('sales_type', 'Secondary')->pluck('id');
        }

        if ($request->branch_id && $request->branch_id != '' && $request->branch_id != null) {
            $query->where('final_branch', $request->branch_id);
        }

        if ($request->division_id && $request->division_id != '' && $request->division_id != null) {
            $query->where('division', $request->division_id);
        }

        if ($request->dealer_id && $request->dealer_id != '' && $request->dealer_id != null) {
            $query->where('dealer', 'like', '%' . $request->dealer_id . '%');
        }

        if ($request->product_model && $request->product_model != '' && $request->product_model != null) {
            $query->where('product_name', $request->product_model);
        }

        if ($request->new_group && $request->new_group != '' && $request->new_group != null) {
            $query->where('new_group', $request->new_group);
        }

        if ($request->executive_id && $request->executive_id != '' && $request->executive_id != null) {
            $query->where('sales_person', $request->executive_id);
        }

        if ($request->financial_year && $request->financial_year != '' && $request->financial_year != null) {
            $f_year_array = explode('-', $request->financial_year);

            $financial_year_start = $f_year_array[0] . '-04-01';
            $financial_year_end = $f_year_array[1] . '-03-31';

            $query->where(function ($q) use ($f_year_array, $financial_year_start, $financial_year_end) {
                $q->where('invoice_date', '>=', $financial_year_start)
                    ->where('invoice_date', '<=', $financial_year_end);;
            });
        }

        if ($request->month && $request->month != '' && $request->month != null && $request->financial_year && $request->financial_year != '' && $request->financial_year != null) {

            $f_year_array = explode('-', $request->financial_year);

            if ($request->month == 'Jan' || $request->month == 'Feb' || $request->month == 'Mar') {
                $currentYear = $f_year_array[1];
                $monthNumbers = array_map(function ($month) {
                    return Carbon::parse($month)->month;
                }, $request->month);

                // Get the first month number and the last month number
                $firstMonthNumber = min($monthNumbers);
                $lastMonthNumber = max($monthNumbers);

                // Create Carbon instances for the first and last dates
                $firstDate = Carbon::createFromDate($currentYear, $firstMonthNumber, 1)->startOfMonth();
                $lastDate = Carbon::createFromDate($currentYear, $lastMonthNumber, 1)->endOfMonth();
                $startDateFormatted = $firstDate->toDateString();
                $endDateFormatted = $lastDate->toDateString();
            } else {
                $currentYear = $f_year_array[0];
                $monthNumbers = array_map(function ($month) {
                    return Carbon::parse($month)->month;
                }, $request->month);

                // Get the first month number and the last month number
                $firstMonthNumber = min($monthNumbers);
                $lastMonthNumber = max($monthNumbers);

                // Create Carbon instances for the first and last dates
                $firstDate = Carbon::createFromDate($currentYear, $firstMonthNumber, 1)->startOfMonth();
                $lastDate = Carbon::createFromDate($currentYear, $lastMonthNumber, 1)->endOfMonth();
                $startDateFormatted = $firstDate->toDateString();
                $endDateFormatted = $lastDate->toDateString();
            }

            $query->where(function ($q) use ($startDateFormatted, $endDateFormatted) {
                $q->where('invoice_date', '>=', $startDateFormatted)
                    ->where('invoice_date', '<=', $endDateFormatted);;
            });
        }

        $data['total_qty'] = $query->sum('quantity');
        $data['total_sale'] = number_format(($query->sum('net_amount') / 100000), 2, '.', '') . " (Lac)";

        return response()->json($data);
    }

    public function getServiceProduct(Request $request)
    {
        if ($request->ajax()) {

            $data = ServiceChargeProducts::where('charge_type_id', $request->charge_type_id)->where('division_id', $request->charge_cat_id)->get();

            return response()->json($data);
        }
    }

    public function getServiceProductDetails(Request $request)
    {
        if ($request->ajax()) {

            $data = ServiceChargeProducts::where('id', $request->id)->first();

            return response()->json($data);
        }
    }

    public function changeAppointmentStatus(Request $request)
    {
        if ($request->status == '3') {
            $update = DealerAppointment::where('id', $request->appo_id)->update(['approval_status' => $request->status, 'ho_approve' => auth()->user()->id]);
            DealerAppointmentKyc::updateOrCreate(
                ['appointment_id' => $request->appo_id],
                ['dealer_code' => $request->dealer_code]
            );
        } else {
            if ($request->status == '1') {
                $update = DealerAppointment::where('id', $request->appo_id)->update(['approval_status' => $request->status, 'sales_approve' => auth()->user()->id]);
            } else {
                $update = DealerAppointment::where('id', $request->appo_id)->update(['approval_status' => $request->status]);
            }
        }
        if ($update) {
            return response()->json(['status' => 'success', 'message' => 'Approved Successfully !!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Somthing went wrong.']);
        }
    }

    public function getOrderLimit(Request $request)
    {
        $today_order_qty = Order::where('customer_id', $request->customer_id)
            ->whereDate('created_at', today())
            ->whereNot('status', '4')
            ->sum('qty');

        return response()->json(['status' => 'success', 'today_order_qty' => $today_order_qty]);
    }

    public function getAdditionalPrice(Request $request)
    {
        $model_id = $request->model_id;
        $model_name = $request->model_name;

        $additional_price = optional(AdditionalPrice::where(['model_id' => $model_id, 'model_name' => $model_name])->first())->price_adjustment;

        return response()->json(['status' => 'success', 'additional_price' => $additional_price]);
    }

    public function getBrand()
    {
        $brands = Brand::select('id', 'brand_name')->get();
        return response()->json(['status' => 'success', 'brands' => $brands]);
    }
    public function getGrade()
    {
        $grade = UnitMeasure::select('id', 'unit_name')->get();
        return response()->json(['status' => 'success', 'grade' => $grade]);
    }
    public function getSize()
    {
        $size = Category::select('id', 'category_name')->get();
        return response()->json(['status' => 'success', 'size' => $size]);
    }

    public function sodaDiscount(Request $request)
    {
        $update = Order::where('id', $request->soda_id)->update(['discount_amt'=>$request->dis_amt]);

        if ($update) {
            return response()->json(['status' => 'success', 'message' => 'Discount Add Successfully !!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Somthing went wrong.']);
        }
    }

    // get prices according to to 
    public function getPricesOfOrder(Request $request){
        $brand = $request->brand ?? '';
        $grade = $request->grade ?? '';
        $size = $request->size ?? '';

        $additional_price = 0;
        if(isset($brand) && isset($grade) && isset($size)){
           $brand_price = AdditionalPrice::where(['model_name' => 'brand' , 'model_id' => $brand])->first();
           $grade_price  = AdditionalPrice::where(['model_name' => 'grade' , 'model_id' => $grade])->first();
           $size_price  = AdditionalPrice::where(['model_name' => 'size' , 'model_id' => $size])->first();

           //calculate addition price according to brand , size , grade    
           $additional_price = $additional_price + (isset($brand_price->price_adjustment) ? $brand_price->price_adjustment : 0) + (isset($grade_price->price_adjustment) ? $grade_price->price_adjustment : 0) +(isset($size_price->price_adjustment) ? $size_price->price_adjustment : 0);
        }
        return response()->json(['status' => true , 'additional_price' => $additional_price]);
    }
    
}
