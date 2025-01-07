<?php

use App\Models\Attachment;
use App\Models\BranchStock;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
// use Mail;
use Illuminate\Support\Facades\Storage;
// use Image;
use App\Models\User;
use App\Models\Customers;
use App\Models\UserActivity;
use App\Models\UserLiveLocation;
use Illuminate\Support\Facades\File;
use App\Models\Notification;
use App\Models\Wallet;
use App\Models\WalletDetail;
use App\Models\Sales;
use App\Models\SalesDetails;
use App\Models\Settings;
use App\Models\SchemeHeader;
use App\Models\Coupons;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

if (! function_exists('sendmessage')) {
    function sendmessage($data, $mobile)
    {
        $message = urldecode('Your OTP ' . $data . ' for TRIDENT TEXTILE app generated on ' . Carbon::now('Asia/Kolkata')->format('Y-m-d H:i:s'));
        $postData = array(
            'authkey' => '816d984a2eec5f3f1b72d0aef4f6b236',
            'mobiles' => $mobile,
            'message' => $message,
            'sender' => 'TRITXT',
            'route' => 'B'
        );
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => 'http://smsservice.imast.in/api/send_http.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $output = curl_exec($ch);
        if (curl_errno($ch)) {
            return 'error:' . curl_error($ch);
        }
        curl_close($ch);
        return $output = array('error' => '0');
    }
}
if (! function_exists('sendNotification')) {
    function sendNotification($userid, $data)
    {
        $token = User::where('id', $userid)->pluck('notification_id')->first();
        $url = "https://fcm.googleapis.com/fcm/send";
        // $serverKey = 'AAAAjMeiBjY:APA91bGtua9m0x8v1pNNAX6JhNDjnCvm4HgVQnpUhaFID4WonakTivV72RzttdSs5Aux1ua0BUZQGM3RkzAYuGr8BnQcit2rMEF7-aMhzWnWtoLoMNxsbzRTpTy8k8x6sYPHoLbIh9vX';

        $serverKey = 'AAAAVO4fLoE:APA91bHceRDC8GZgOFCIzfiBjqMx5vqgpC14s3Z-4dh-qOqvyTWg6zl8TTeIwZrepNs_cojgUcY6PbXwGPLx5VuGTiw-5vZUlj7jvasgatM4x22yEyj0gaYVCwpl9vJeJDmdo7E5vWEy';
        if (!empty($token)) {
            $notification = array('title' => $data['title'], 'message' => $data['body'], 'time' => date('Y-m-d'), 'image' => "https://source.unsplash.com/user/c_v_r/1900x800");
            $arrayToSend = array('to' => $token, 'data' => $notification);
            $json = json_encode($arrayToSend);
            $headers = array(
                'Content-Type:application/json',
                'Authorization:key=' . $serverKey
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            //Send the request
            $response = curl_exec($ch);
            //Close request
            if ($response === FALSE) {
                die('FCM Send Error: ' . curl_error($ch));
            }
            curl_close($ch);
            Notification::create([
                'type' => isset($data['title']) ? $data['title'] : '',
                'data' => isset($data['body']) ? $data['body'] : '',
                'customer_id' => isset($data['customer_id']) ? $data['customer_id'] : null,
                'user_id' => $userid
            ]);
        }
    }
}
if (! function_exists('receiverNotification')) {
    function receiverNotification($data, $receiver_id)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        // $server_key = 'AAAAz12287M:APA91bELeMYiEsqBNzFfKvKgcdPA645159iYFc9fMLxiPTDvWJwoS2xOP14m1ZfbyOkVT9m6qe4aviKIaXUdk3NeO12Ft3NQBJt5J9rnM6fCeMyK98Qsjp5eZdhpj79h07Em7nJ_482Y';  
        $serverKey = 'AAAAVO4fLoE:APA91bHceRDC8GZgOFCIzfiBjqMx5vqgpC14s3Z-4dh-qOqvyTWg6zl8TTeIwZrepNs_cojgUcY6PbXwGPLx5VuGTiw-5vZUlj7jvasgatM4x22yEyj0gaYVCwpl9vJeJDmdo7E5vWEy';
        $notification_id = User::where('id', $receiver_id)->pluck('notification_id')->first();
        $fields = array();
        $fields['data'] = $data;
        $fields['registration_ids'] = array($notification_id);
        $headers = array(
            'Content-Type:application/json',
            'Authorization:key=' . $server_key
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }
}

if (! function_exists('fileupload')) {
    function fileupload($image = '', $path = '', $filename = '')
    {
        // $filepath =  Storage::disk('s3')->put($path, $image);
        // return $filepath;
        $filename = $filename . date('ymdHis') . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('uploads/' . $path), $filename);
        return $path . '/' . $filename;
    }
}
if (! function_exists('base64tofile')) {
    function base64tofile($image = '', $path = '', $filename = '')
    {
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace('""', '', $image);
        $image = str_replace(' ', '+', $image);
        $filename = $filename . date('ymdHis') . '.jpg';
        $image = base64_decode($image);
        $filepath =  Storage::disk('s3')->put($path, $image);
        return $filepath;
        // if(Storage::disk('uploads')->exists($path.$filename)) {
        //     Storage::disk('uploads')->delete($path.$filename);
        // }
        // Storage::disk('uploads')->put($path.$filename, $image);
        // Storage::disk('uploads')->setVisibility($path.$filename, 'public');
        // return $path.$filename;

        // $url = Storage::disk('uploads')->url($path.$filename);
        // //return $url;
        // return url('/').str_replace('storage/', 'public/uploads/', $url);
    }
}

if (! function_exists('autoIncrementId')) {
    function autoIncrementId($tableName, $primaryColumn)
    {
        $model_name = '\\App\\Models\\' . $tableName;
        $model = new $model_name;
        $primaryId = $model->max($primaryColumn);
        if (!empty($primaryId)) {
            return $primaryId + 1;
        }
        return 1;
    }
}

if (! function_exists('numberFormat')) {
    function numberFormat($number, $point)
    {
        return number_format($number, $point);
    }
}

if (! function_exists('trimReplace')) {
    function trimReplace($strings)
    {
        return trim(preg_replace("/[^a-zA-Z0-9%\/\s]/", "", $strings));
    }
}
if (! function_exists('showdateformat')) {
    function showdateformat($date)
    {
        return Carbon::parse($date)->format('M,d Y');
    }
}

if (! function_exists('showdtimeformat')) {
    function showdtimeformat($time)
    {
        return Carbon::parse(strtotime($time))->format('H:i');
    }
}

if (! function_exists('showdatetimeformat')) {
    function showdatetimeformat($date)
    {
        return Carbon::parse($date)->format('M,d Y h:i');
    }
}
if (! function_exists('getcurentDateTime')) {
    function getcurentDateTime()
    {
        return Carbon::now('Asia/Kolkata');
    }
}
if (! function_exists('getcurentDate')) {
    function getcurentDate()
    {
        return Carbon::now('Asia/Kolkata')->format('Y-m-d');
    }
}
if (! function_exists('getcurentTime')) {
    function getcurentTime()
    {
        return Carbon::now('Asia/Kolkata')->format('H:i');
    }
}
if (! function_exists('stringtodate')) {
    function stringtodate($date)
    {
        return date("Y-m-d", strtotime($date));
    }
}

if (! function_exists('teamusers')) {
    function teamusers($user)
    {
        $users = TeamUser::whereHas('teams', function ($query) use ($user) {
            $query->where('manager_id', $user);
            $query->orWhere('leader_id', $user);
        })->orWhere('user_id', $user)->pluck('user_id');

        return $users->push($user)->unique();
    }
}
if (! function_exists('distance')) {
    function distance($latitude, $longitude, $customer_id)
    {
        $data =  Customers::where('id', '=', $customer_id)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('latitude', 'longitude', DB::raw('( 6367 * acos( cos( radians(' . $latitude . ') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(' . $longitude . ') ) + sin( radians(' . $latitude . ') ) * sin( radians( latitude ) ) ) ) AS distance'))
            ->first();
        return isset($data['distance']) ? round($data['distance'] * 1000, 2) : '';
    }
}
if (! function_exists('amountConversion')) {
    function amountConversion($amount)
    {
        $final = '';
        switch (true) {
            case ($amount >= 1000 && $amount <= 100000):
                $final = ($amount / 1000) . 'K';
                break;
            case ($amount > 100000):
                $final = ($amount / 100000) . 'L';
                break;
            default:
                $final = $amount;
        }
        return (string)$final;
    }
}
if (! function_exists('submitUserActivity')) {
    function submitUserActivity($data)
    {
        // $address = getLatLongToAddress($data['latitude'] , $data['longitude']);
        // return UserActivity::insert([
        //     "active"   =>  "Y",
        //     "userid" => isset($data['userid']) ? $data['userid'] : $data['user_id'] , 
        //     "customerid" => isset($data['customer_id']) ? $data['customer_id'] : null ,
        //     'latitude' => isset($data['latitude']) ? $data['latitude'] : null , 
        //     'longitude' => isset($data['longitude']) ? $data['longitude'] : null , 
        //     'time' => isset($data['time']) ? date('Y-m-d H:i:s',strtotime($data['time'])) : date('Y-m-d H:i:s') , 
        //     // 'address' => isset($address) ? $address : '' ,
        //     'address' => '', 
        //     'description' => isset($data['description']) ? $data['description'] : '' , 
        //     'type' => isset($data['type']) ? $data['type'] : '' , 
        //     'created_at' => date('Y-m-d H:i:s')

        // ]);

        $address = getLatLongToAddress($data['latitude'], $data['longitude']);

        return UserActivity::insert([
            "active"   =>  "Y",
            "userid" => isset($data['userid']) ? $data['userid'] : $data['user_id'],
            "customerid" => isset($data['customer_id']) ? $data['customer_id'] : null,
            'latitude' => isset($data['latitude']) ? $data['latitude'] : null,
            'longitude' => isset($data['longitude']) ? $data['longitude'] : null,
            //'time' => isset($data['time']) ? date('Y-m-d H:i:s',strtotime($data['time'])) : date('Y-m-d H:i:s') , 
            'address' => isset($address) ? $address : '',
            //'address' => '', 
            //'description' => isset($data['description']) ? $data['description'] : '' , 
            //'type' => isset($data['type']) ? $data['type'] : '' , 
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
if (! function_exists('getLatLongToAddress')) {
    function getLatLongToAddress($latitude, $longitude)
    {
        // $addressline = UserLiveLocation::where('latitude','=',$latitude)->where('longitude','=',$longitude)->whereNotNull('address')->pluck('address')->first();

        // if(empty($addressline))
        // {
        //     $queryString = http_build_query([
        //       'access_key' => 'd342b3255ee297b500728db66a690965',
        //       'query' => "$latitude,$longitude",
        //       'output' => 'json',
        //       'limit' => 1,
        //     ]);

        //     $ch = curl_init(sprintf('%s?%s', 'http://api.positionstack.com/v1/reverse', $queryString));
        //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //     $json = curl_exec($ch);
        //     curl_close($ch);
        //     $results = json_decode($json, true);
        //     if(!empty($results['data']))
        //     {
        //         $addressline = $results['data'][0]['name'].', '.$results['data'][0]['county'].', '.$results['data'][0]['region'].', '.$results['data'][0]['postal_code'];

        //     }
        // } 
        $addressline = '';
        $queryString = http_build_query([
            //   'access_key' => 'd342b3255ee297b500728db66a690965',
            'access_key' => 'cb11435aa9960016039084830621463b',

            'query' => "$latitude,$longitude",
            'output' => 'json',
            'limit' => 1,
        ]);

        $ch = curl_init(sprintf('%s?%s', 'http://api.positionstack.com/v1/reverse', $queryString));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($ch);
        curl_close($ch);
        $results = json_decode($json, true);
        if (!empty($results['data'])) {
            $addressline = $results['data'][0]['name'] . ', ' . $results['data'][0]['county'] . ', ' . $results['data'][0]['region'] . ', ' . $results['data'][0]['postal_code'];
        }
        return $addressline;
    }
}
if (! function_exists('getLatLongToCity')) {
    function getLatLongToCity($latitude, $longitude)
    {
        $addressline = '';
        $queryString = http_build_query([
            //   'access_key' => 'd342b3255ee297b500728db66a690965',
            'access_key' => 'cb11435aa9960016039084830621463b',

            'query' => "$latitude,$longitude",
            'output' => 'json',
            'limit' => 1,
        ]);

        $ch = curl_init(sprintf('%s?%s', 'http://api.positionstack.com/v1/reverse', $queryString));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($ch);
        curl_close($ch);
        $results = json_decode($json, true);
        if (!empty($results['data'])) {
            $addressline = $results['data'][0] ? $results['data'][0]['county'] : '';
        }
        return $addressline;
    }
}
if (! function_exists('getUsersReportingToAuth')) {
    function getUsersReportingToAuth($userid = '')
    {
        $userid = !empty($userid) ? $userid : Auth::user()->id;
        $userinfo = User::where('id', '=', $userid)->first();

        $all_users = User::where('active', 'Y')->get();

        // if(!$userinfo->hasRole('superadmin') && !$userinfo->hasRole('Admin'))
        // {
        //     $all_ids_array = array($userid);
        //     $test = getAllChild(array($userid), $all_users);
        //     while(count($test) > 0){
        //         $all_ids_array = array_merge($all_ids_array, $test);
        //         $test = getAllChild($test, $all_users);
        //     }
        // }else{
        //     $all_ids_array = User::pluck('id')->toArray();
        // }

        if (!$userinfo->hasRole('superadmin') && !$userinfo->hasRole('Admin') && !$userinfo->hasRole('Sub_Admin') && !$userinfo->hasRole('HR_Admin') && !$userinfo->hasRole('HO_Account')  && !$userinfo->hasRole('Sub_Support') && !$userinfo->hasRole('Accounts Order') && !$userinfo->hasRole('Service Admin') && !$userinfo->hasRole('All Customers') && !$userinfo->hasRole('Sub billing') && !$userinfo->hasRole('Sales Admin')) {
            $all_ids_array = array($userid);
            $test = getAllChild(array($userid), $all_users);
            while (count($test) > 0) {
                $all_ids_array = array_merge($all_ids_array, $test);
                $test = getAllChild($test, $all_users);
            }
        } elseif ($userinfo->hasRole('Accounts Order')) {
            $all_ids_array = User::where('active', 'Y')->whereIn('branch_id', explode(',', $userinfo->branch_show))->pluck('id')->toArray();
            $test = getAllChild(array($userid), $all_users);
            while (count($test) > 0) {
                $all_ids_array = array_merge($all_ids_array, $test);
                $test = getAllChild($test, $all_users);
            }
        } else {
            $all_ids_array = User::where('active', 'Y')->pluck('id')->toArray();
        }

        return $all_ids_array;
    }
}

if (! function_exists('insertSales')) {
    function insertSales($data)
    {
        $sallers = $data->pluck('seller_id');
        $buyers = $data->pluck('buyer_id');
        $customers = Customers::whereIn('id', $sallers)->orWhereIn('id', $buyers)->select('id', 'customertype')->get();
        $existsales = Sales::whereIn('seller_id', $sallers)->orwhereIn('buyer_id', $buyers)->select('buyer_id', 'seller_id', 'sales_no', 'invoice_no', 'grand_total', 'invoice_date')->get();
        $sales = collect([]);
        $saledetails = collect([]);

        $data->filter(function ($item, $key) use ($existsales, $sales, $saledetails) {
            $item['sales_no'] = uniqueSalesNo($item['invoice_no'], $item['seller_id'], fiscalYear($item['invoice_date']));

            if (!$existsales->contains('sales_no', $item['sales_no'])) {
                $sales->push([
                    'buyer_id' => isset($item['buyer_id']) ? $item['buyer_id'] : null,
                    'seller_id' => isset($item['seller_id']) ? $item['seller_id'] : null,
                    'order_id' => isset($item['order_id']) ? $item['order_id'] : null,
                    'total_qty' => isset($item['total_qty']) ? $item['total_qty'] : 0,
                    'shipped_qty' => isset($item['shipped_qty']) ? $item['shipped_qty'] : 0,
                    'orderno' => isset($item['orderno']) ? $item['orderno'] : null,
                    'invoice_no' => isset($item['invoice_no']) ? $item['invoice_no'] : null,
                    'invoice_date' => isset($item['invoice_date']) ? $item['invoice_date'] : null,
                    'transport_details' => isset($item['transport_details']) ? $item['transport_details'] : null,
                    'total_gst' => isset($item['total_gst']) ? $item['total_gst'] : 0,
                    'sub_total' => isset($item['sub_total']) ? $item['sub_total'] : 0.00,
                    'grand_total' => isset($item['grand_total']) ? $item['grand_total'] : 0.00,
                    'description' => isset($item['description']) ? $item['description'] : '',
                    'lr_no' =>  isset($item['lr_no']) ? $item['lr_no'] : null,
                    'dispatch_date' =>  isset($item['dispatch_date']) ? $item['dispatch_date'] : null,
                    // 'status_id' => isset($item['status_id']) ? $item['status_id'] : 6 ,
                    'status_id' => isset($item['status_id']) ? $item['status_id'] : null,
                    'fiscal_year' => fiscalYear($item['invoice_date']),
                    'sales_no' => isset($item['sales_no']) ? $item['sales_no'] : null,
                    'active' => 'Y',
                    'created_at' => getcurentDateTime(),
                    'created_by' => Auth::user()->id,
                ]);
                if (!empty($item['saledetail'])) {
                    foreach ($item['saledetail'] as $key => $rows) {
                        $saledetails->push([
                            'sales_no' => isset($item['sales_no']) ? $item['sales_no'] : null,
                            'active' => 'Y',
                            'product_id' => isset($rows['product_id']) ? $rows['product_id'] : null,
                            'product_detail_id' => isset($rows['product_detail_id']) ? $rows['product_detail_id'] : null,
                            'quantity' => isset($rows['quantity']) ? $rows['quantity'] : 0,
                            'shipped_qty' => isset($rows['shipped_qty']) ? $rows['shipped_qty'] : 0,
                            'price' => isset($rows['price']) ? $rows['price'] : 0.00,
                            'tax_amount' => isset($rows['tax_amount']) ? $rows['tax_amount'] : 0.00,
                            'line_total' => isset($rows['line_total']) ? $rows['line_total'] : 0.00,
                            // 'status_id' => '6',
                            'status_id' => null,
                            'created_at' => getcurentDateTime(),
                        ]);
                    }
                }
                $existsales->push([
                    'buyer_id' => isset($item['buyer_id']) ? $item['buyer_id'] : null,
                    'seller_id' => isset($item['seller_id']) ? $item['seller_id'] : null,
                    'invoice_no' => isset($item['invoice_no']) ? $item['invoice_no'] : null,
                    'invoice_date' => isset($item['invoice_date']) ? $item['invoice_date'] : null,
                    'grand_total' => isset($item['grand_total']) ? $item['grand_total'] : 0.00,
                    'sales_no' => isset($item['sales_no']) ? $item['sales_no'] : null,
                ]);
                return $item;
            }
        });
        if ($sales->isNotEmpty()) {
            Sales::insert($sales->toArray());
            checkValidScheme($sales);
        }
        if ($saledetails->isNotEmpty()) {
            $sales_no = $saledetails->pluck('sales_no');
            $sales_id = Sales::whereIn('sales_no', $sales_no)->select('id', 'sales_no', 'seller_id', 'grand_total', 'invoice_date', 'invoice_no', 'created_by')->get();
            $finalsaledetails = $saledetails->map(function ($item, $key) use ($sales_id) {
                $item['sales_id'] = $sales_id->where('sales_no', $item['sales_no'])->pluck('id')->first();
                unset($item['sales_no']);
                return $item;
            });
            SalesDetails::insert($finalsaledetails->toArray());

            $stocks = $finalsaledetails->map(function ($item, $key) use ($sales_id) {
                $sales = $sales_id->where('id', $item['sales_id'])->first();
                $item['grand_total'] = isset($sales['grand_total']) ? $sales['grand_total'] : 0.00;
                $item['invoice_date'] = isset($sales['invoice_date']) ? $sales['invoice_date'] : null;
                $item['customer_id'] = isset($sales['seller_id']) ? $sales['seller_id'] : null;
                $item['invoice_no'] = isset($sales['invoice_no']) ? $sales['invoice_no'] : null;
                $item['created_by'] = isset($sales['created_by']) ? $sales['created_by'] : null;
                $item['stock_type'] = 'Dr';
                $item['description'] = '';
                $item['purchase_price'] = $item['price'];
                $item['selling_price'] = 0.00;
                unset($item['price'], $item['shipped_qty']);
                return $item;
            });
            //debitStockEntry($stocks);
        }
        if ($sales->count() == 1) {
            $sales_id = Sales::where('sales_no', $sales->pluck('sales_no')->first())->pluck('id')->first();
        } else {
            $sales_id = null;
        }
        return $response = array('status' => 'success', 'message' => 'Sales Insert Successfully', 'sales_id' => $sales_id);
    }
}
if (! function_exists('couponScans')) {
    function couponScans($data)
    {
        $customer_id = $data->pluck('customer_id');
        $couponcodes = $data->pluck('coupon_code');

        $customers = Customers::whereIn('id', $customer_id)->select('id', 'customertype')->get();
        $existcodes = Wallet::whereIn('coupon_code', $couponcodes)
            ->select('id', 'coupon_code', 'customer_id', 'invoice_no', 'invoice_date', 'invoice_amount', 'active', 'transaction_at')
            ->get();
        $coupons = Coupons::where('active', '=', 'Y')->whereIn('coupon', $couponcodes)->select('coupon', 'expiry_date', 'product_id', 'points')->get();
        $sales = collect([]);
        $scheme = SchemeHeader::where('start_date', '<=', date("Y-m-d"))
            ->where('end_date', '>=', date("Y-m-d"))
            ->where('active', '=', 'Y')
            ->where('scheme_type', '=', 'couponCode')->first();
        if (!empty($scheme)) {
            $responsedata = $data->filter(function ($item, $key) use ($existcodes, $sales, $coupons, $scheme) {
                $scandate = getcurentDateTime();
                if (!$existcodes->contains('coupon_code', $item['coupon_code']) && $coupons->contains('coupon_code', $item['coupon_code'])) {
                    $getcoupons = $coupons->where('coupon', $item['coupon_code']);

                    $schemedetails = $scheme['schemedetails']->first();
                    $sales->push([
                        'active' => 'Y',
                        'customer_id' => isset($item['customer_id']) ? $item['customer_id'] : null,
                        'scheme_id' => $scheme['id'],
                        'schemedetail_id' => $schemedetails['id'],
                        'points' => isset($getcoupons['points']) ? $getcoupons['points'] : 0,
                        'point_type' => 'CP',
                        'transaction_type' => 'Cr',
                        'coupon_code' => isset($item['coupon_code']) ? $item['coupon_code'] : null,
                        'invoice_no' => isset($getcoupons['invoice_no']) ? $getcoupons['invoice_no'] : null,
                        'invoice_date' => isset($getcoupons['invoice_date']) ? $getcoupons['invoice_date'] : null,
                        'invoice_amount' => 0.00,
                        'transaction_at' => $scandate,
                        'created_at' => $scandate,
                    ]);
                    $item['status_id'] =  25;
                    return $item;
                } else if ($coupons->contains('coupon', $item['coupon_code'])) {
                    $item['status_id'] =  24;
                    return $item;
                }
            });
        } else {
            return $response = array('status' => 'error', 'message' => 'Scheme Not Found');
        }
        if ($sales->isNotEmpty()) {
            Wallet::insert($sales->toArray());
            return $response = array('status' => 'success', 'message' => 'Coupons Scan Successfully');
        }
        return $response = array('status' => 'error', 'message' => 'Error In Coupon Scan');
    }
}

if (! function_exists('checkValidScheme')) {
    function checkValidScheme($sales)
    {
        $sales_nos = $sales->pluck('sales_no');
        $saleSettings = Settings::where('module', '=', 'sales')->select('key_name', 'value')->get();
        $insertedSales = Sales::whereIn('sales_no', $sales_nos)->select('id', 'grand_total', 'buyer_id', 'seller_id', 'invoice_date', 'invoice_no')->get();
        $schemes = SchemeHeader::where(function ($query) {
            $query->where('active', '=', 'Y');
        })
            ->select('id', 'scheme_name', 'start_date', 'end_date', 'scheme_type', 'point_value')->get();
        $transection = collect([]);
        $insertedSales->map(function ($item, $key) use ($schemes, $transection, $saleSettings) {

            $datafilter = $schemes->filter(function ($scheme) use ($item, $saleSettings, $transection) {
                $saleSchemesum = Sales::whereDate('invoice_date', '>=', date('Y-m-d', strtotime($scheme['start_date'])))->where('buyer_id', $item['buyer_id'])->whereDate('invoice_date', '<=', date('Y-m-d', strtotime($scheme['end_date'])))->sum('grand_total');
                $saleSchemesum = ($saleSchemesum == 0) ? 1 : $saleSchemesum;
                $schemedetails = $scheme['schemedetails'];
                $schemedetails = $schemedetails->where('minimum', '<=', intval($saleSchemesum))->where('maximum', '>=', intval($saleSchemesum))->first();
                $point_type = 'PP';
                if (date('Y-m-d', strtotime($item['invoice_date'])) >= date('Y-m-d', strtotime($scheme['start_date'])) &&  date('Y-m-d', strtotime($item['invoice_date'])) <= date('Y-m-d', strtotime($scheme['end_date'])) && !empty($schemedetails)) {
                    $transection->push([
                        'customer_id' => $item['buyer_id'],
                        'scheme_id' => $scheme['id'],
                        'schemedetail_id' => $schemedetails['id'],
                        'points' => round(($item['grand_total'] / $scheme['point_value']) * $schemedetails['points']),
                        'point_type' => $point_type,
                        'invoice_amount' => $item['grand_total'],
                        'invoice_no' => $item['invoice_no'],
                        'invoice_date' => $item['invoice_date'],
                        'transaction_type' => 'Cr',
                        'sales_id' => $item['id'],
                        'created_at' => getcurentDateTime(),
                    ]);
                }
            });
        });
        if ($transection->isNotEmpty()) {
            Wallet::insert($transection->toArray());
        }
    }
}
if (! function_exists('checkCodeScanScheme')) {
    function checkCodeScanScheme($sales) {}
}
if (! function_exists('salesApproval')) {
    function salesApproval($sales_id)
    {
        // $sales = Sales::where('id',$sales_id)->update(['status_id' => 12]);
        // return Wallet::where('sales_id',$sales_id)->update(['point_type' => 'CP']);
        Wallet::where('sales_id', $sales_id)->update(['point_type' => 'CP']);
        return Sales::where('id', $sales_id)->update(['status_id' => 12]);
    }
}
if (! function_exists('salesReject')) {
    function salesReject($sales_id)
    {
        $sales = Sales::where('id', $sales_id)->update(['status_id' => 14]);
        $wallet = Wallet::where('sales_id', $sales_id)->first();
        WalletDetail::where('wallet_id', $wallet['id'])->delete();
        return $wallet->delete();
    }
}
if (! function_exists('uniqueSalesNo')) {
    function uniqueSalesNo($invoice_no, $seller_id, $fiscalYear)
    {
        return $seller_id . '-' . $invoice_no . '-' . $fiscalYear;
    }
}
if (! function_exists('fiscalYear')) {
    function fiscalYear($invoice_date)
    {
        if (Carbon::parse($invoice_date)->format('m') > 3) {
            $fiscalYear =  Carbon::parse($invoice_date)->format('Y') . "-" . (Carbon::parse($invoice_date)->format('Y') + 1);
        } else {
            $fiscalYear = (Carbon::parse($invoice_date)->format('Y') - 1) . "-" . Carbon::parse($invoice_date)->format('Y');
        }
        return $fiscalYear;
    }
}
if (! function_exists('walletRedemption')) {
    function walletRedemption($data)
    {
        try {
            $redeemed = redeemablebalance($data['customer_id']);
            if ($redeemed['points'] < $data['total_points']) {
                return $response = array('status' => 'error', 'message' => 'insufficient balance', 'redeemable_point' => $redeemed['points']);
            }

            if ($wallet_id = Wallet::insertGetId([
                'active' => 'Y',
                'customer_id' => $data['customer_id'],
                'points' => isset($data['total_points']) ? $data['total_points'] : 0,
                'point_type' => '',
                'transaction_at' => date('Y-m-d H:i:s'),
                'transaction_type' => 'Dr',
                'created_at' => getcurentDateTime(),
            ])) {
                $details = collect([]);

                if (!empty($data['orderdetail'])) {
                    foreach ($data['orderdetail'] as $key => $order) {
                        $details->push([
                            'active' => 'Y',
                            'wallet_id' => $wallet_id,
                            'points' => $order['points'],
                            'product_id' => isset($order['product_id']) ? $order['product_id'] : null,
                            'category_id' => isset($order['category_id']) ? $order['category_id'] : null,
                            'subcategory_id' => isset($order['subcategory_id']) ? $order['subcategory_id'] : null,
                            'quantity' => isset($order['quantity']) ? $order['quantity'] : 0,
                            'created_at' => getcurentDateTime(),
                        ]);
                    }

                    if ($details->isNotEmpty()) {
                        WalletDetail::insert($details->toArray());
                    }
                }
                return $response = array('status' => 'success', 'message' => 'Redeem Successfully', 'wallet_id' => $wallet_id);
            }
            return $response = array('status' => 'error', 'message' => 'insufficient balance');
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
if (! function_exists('redeemablebalance')) {
    function redeemablebalance($customer_id)
    {
        $scheme = SchemeHeader::whereDate('start_date', '>=', date('Y-m-d'))->whereDate('end_date', '<=', date('Y-m-d'))->where('active', '=', 'Y')->select('id', 'points_start_date', 'points_end_date', 'block_points', 'redeem_percents', 'schemes')->first();

        $point = Wallet::where(function ($query) use ($scheme, $customer_id) {
            if (!empty($scheme['points_start_date'])) {
                $query->whereDate('transaction_at', '>=', date('Y-m-d', strtotime($scheme['points_start_date'])));
            }
            if (!empty($scheme['points_end_date'])) {
                $query->whereDate('transaction_at', '<=', date('Y-m-d', strtotime($scheme['points_end_date'])));
            }
            if (!empty($scheme['schemes'])) {
                $query->whereIn('scheme_id', explode(',', $scheme['schemes']));
            }
            $query->where('transaction_type', '=', 'Cr');
            $query->where('point_type', '=', 'CP');
            $query->where('customer_id', '=', $customer_id);
        })->sum('points');
        $debitedpoint = Wallet::where(function ($query) use ($customer_id) {
            if (!empty($scheme['points_start_date'])) {
                $query->whereDate('transaction_at', '>=', date('Y-m-d', strtotime($scheme['points_start_date'])));
            }
            if (!empty($scheme['points_end_date'])) {
                $query->whereDate('transaction_at', '<=', date('Y-m-d', strtotime($scheme['points_end_date'])));
            }
            if (!empty($scheme['schemes'])) {
                $query->whereIn('scheme_id', explode(',', $scheme['schemes']));
            }
            $query->where('transaction_type', '=', 'Dr');
            $query->where('customer_id', '=', $customer_id);
        })->sum('points');
        $point = ($point ==  0) ? $point : ($point - $debitedpoint);
        if (!empty($scheme['block_points'])) {
            $point = ($scheme['redeem_percents'] ==  0) ? $point : ($point - $scheme['block_points']);
        }
        if (!empty($scheme['redeem_percents'])) {
            $point = ($scheme['block_points'] ==  0) ? $point : ($point * $scheme['redeem_percents'] / 100);
        }
        $scheme_id = !empty($scheme['id']) ? $scheme['id'] : null;

        return $response = array('points' => $point, 'scheme_id' => $scheme_id);
    }
}

if (! function_exists('totalDueAmount')) {
    function totalDueAmount($customer_id)
    {
        $totalcredit = Payment::where('customer_id', $customer_id)->sum('amount');
        $totalsales = Sales::where('buyer_id', $customer_id)->sum('grand_total');
        return $totalsales - $totalcredit;
    }
}


function getAllChild($users_id, $all_user)
{

    $children = $all_user->whereIn('reportingid', $users_id)->pluck('id')->toArray();

    return $children;
}

function getCustomerAttachmentByDocumentName($document_name, $customer_id)
{
    return Attachment::where('document_name', $document_name)->where('customer_id', $customer_id)->first();
}

function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371)
{
    // convert from degrees to radians
    $latFrom = deg2rad($latitudeFrom);
    $lonFrom = deg2rad($longitudeFrom);
    $latTo = deg2rad($latitudeTo);
    $lonTo = deg2rad($longitudeTo);

    $latDelta = $latTo - $latFrom;
    $lonDelta = $lonTo - $lonFrom;

    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
        cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
    return $angle * $earthRadius;
}


function generatePoNumber()
{
    $lastOrder = Order::latest('id')->first();
    $lastOrderId = $lastOrder ? $lastOrder->id : 0;
    $poNumber = 1000000 + $lastOrderId + 1;

    return $poNumber;
}

function addNotification(array $data)
{
    Notification::create([
        'type' => $data['type'],
        'data' => $data['data'],
        'customer_id' => $data['customer_id'],
    ]);
    Customers::where('id', $data['customer_id'])->update(['notify'=>true]);

    return true;
}

function getAllNotification(){
    return Notification::where('active', 'Y')->orderBy('id', 'desc')->get();
}

function manageStock($data){
    $PlantStock = BranchStock::where(['plant_id'=>$data['plant_id'], 'brand_id'=>$data['brand_id'], 'unit_id'=>$data['unit_id'], 'category_id'=>$data['category_id']])->first();
    if($PlantStock){
        if($PlantStock->stock < $data['qty']){
            return false;
        }
        $PlantStock->stock = $PlantStock->stock - $data['qty'];
        $PlantStock->save();
        return true;
    }else{
        return false;
    }
}