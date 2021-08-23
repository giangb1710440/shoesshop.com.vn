<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\RatingStar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Session;

class HomeController extends Controller
{
    public function index(){

        // return redirect()->route('home')->with('register_success','thanh cong');
        return view('home.index');
    }

    //HÀM HIỂN THỊ TRANG SẢN PHẨM
    public function page_product($id)
    {
        $cate = DB::table('categorys')->get();
        $product = DB::table('products')->get();
        return view('home.page_product')->with([
            'id'=>$id,
            'product'=>$product,
            'cate'=>$cate
        ]);
    }
    //HÀM HIỂN THỊ TRANG TIN TỨC
    public function page_news()
    {
        return view('home.page_news');
    }
    //HÀM HIỂN THỊ TRANG LIÊN LẠC
    public function page_contact()
    {
        return view('home.page_contact');
    }
    //HÀM HIỂN THỊ TRANG ABOUT US
    public function page_about()
    {
        return view('home.page_about');
    }

    //Trang đăng nhập
    public function page_login()
    {
        return view('home.page_login');
    }

    //kiem tra dang nhap
    public function check_login(Request $request){
        $email = $request->input('email');
        $password = $request->input('password');
        if (Auth::attempt(['email' => $email, 'password' => $password,'role_id'=>1])){
            $register_success = Session::get('register_success');
            Session()->put('register_success');
            return redirect()->route('page-admin')->with('register_success','thanh cong');
        }elseif (Auth::attempt(['email' => $email, 'password' => $password,'role_id'=>2])){
            $register_success = Session::get('register_success');
            Session()->put('register_success');
            // return redirect()->route('home')->with('register_success','thanh cong');
            return redirect('/');
        }else{
            $register_success = Session::get('no_success');
            Session()->put('no_success');
            return redirect()->back()->with('no_success', 'Email hoặc mật khẩu của bạn không đúng!');
        }
    }


    //Kiểm tra đăng nhập
    // public function post_login(Request $request)
    // {
    //     $email = $request->input('email');
    //     $password = $request->input('password');

    //     //Đặt kiểu kiện để chuyển hướng đến page-admin hoặc trang chủ
    //     if (Auth::attempt(['email' => $email, 'password' => $password, 'role_id' => 1])) {
    //         return redirect('page-admin');
    //     }elseif (Auth::attempt(['email' => $email, 'password' => $password, 'role_id' => 2])){
    //         return redirect('/');
    //     }else{
    //         $message = $request->session()->get('message');
    //         return redirect()->back()->with('message','');
    //     }
    // }

    //Trang đăng ký
    public function page_signup()
    {
        $role=DB::table('role_accesss')->get();
        return view('home.page_signup')->with([
            'role'=>$role
        ]);
    }
    //kiem tra dang ky
    public function check_signup(Request $res){
        try {
            $this->validate(
                $res,
                [
                    'email' => 'required|unique:users,email',
                    'fullname' => 'required',
                    'username' => 'required',
                    'password' => 'required',
                    'confirm' => 'required',
                    'gender' => 'required',
                    'address' => 'required',
                    'birthday' => 'required',
                    'phone' => 'required'
                ],
                [
                    'email.unique' => 'Email đã tồn tại',
                    'email.required' => 'Vui lòng nhập email',
                    'fullname.required' => 'Vui lòng nhập đầy đủ họ tên',
                    'username.required' => 'Vui lòng nhập tên tài khoản của bạn',
                    'password.required' => 'Vui lòng nhập mật khẩu',
                    'confirm.required' => 'Vui lòng xác nhận mật khẩu',
                    'gender.required' => 'Vui lòng cung cấp giới tính',
                    'address.required' => 'Vui lòng nhập địa chỉ',
                    'birthday.required' => 'Vui lòng nhập ngày sinh',
                    'phone.required' => 'Vui lòng nhập Số điện thoại',
                ]
            );
        } catch (ValidationException $e) {
        }
        $fullname = $res->input('fullname');
        $username = $res->input('username');
        $email = $res->input('email');
        $password = $res->input('password');
        $confirm = $res->input('confirm');
        $gender=$res->input('gender');
        $diachi = $res->input('address');
        $phone = $res->input('phone');
        $birthday = $res->input('birthday');

        // kiem tra mat khau co trung khong
        if (strcasecmp($password, $confirm) != 0){
            return redirect()->back()->with('message', 'Xác nhận mật khẩu sai');
        }else{
            $user = new User;
            $user->role_id =2;
            $user->name=$fullname;
            $user->username=$username;
            $user->email = $email;
            $user->password = bcrypt($password);
            $user->address=$diachi;
            $user->phone=$phone;
            $user->sex=$gender;
            $user->birthday=$birthday;
            $user->save();
            $register_success = Session::get('register_success');
            Session::put('register_success');
            return redirect()->route('page_login')->with('register_success', 'Đăng ký tài khoản thành công');
        }
    }


    //Trang cửa hàng
    public function page_cart()
    {
        return view('home.page_cart');
    }

    //Trang thanh toán
    public function page_checkout()
    {
        return view('home.page_checkout');
    }

    //Trang thanh toán
    public function product_detail($id)
    {
        $product = DB::table('products')->where('id',$id)->get();
        return view('home.product_detail')->with([
            'product'=>$product,
            'id'=>$id
        ]);
    }

    //Trang profile
    public function page_infor_user()
    {
        return view('home.profile_user.infor_user');
    }

    //Trang chờ thanh toán
    public function wait_payment()
    {
        return view('home.profile_user.wait_payment');
    }

    //Trang chờ giao hàng
    public function page_delivery()
    {
        return view('home.profile_user.page_delivery');
    }

    //Trang đã hủy
    public function page_cancel()
    {
        return view('home.profile_user.page_cancel');
    }

    //sao san pham
    public function postRatingStar($userId, $productId, Request $request){
        $get_count_rating = DB::table('rating_stars')->where([['user_id', '=', $userId], ['product_id', '=', $productId]])->count();
        if ($get_count_rating >= 1){
            Session::put('message_error');
            return redirect()->back()->with('message_error', 'Bạn đã đánh giá rồi!');
        }else{
            $add_rating = new RatingStar();
            $add_rating->avg_number_star = $request->input('rating');
            $add_rating->user_id = $userId;
            $add_rating->product_id = $productId;
            $add_rating->save();
            Session::put('message_success');
            return redirect()->back()->with('message_success', 'Đã đánh giá SAO');
        }
    }

}


