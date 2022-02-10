<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Mail\ResetPasswordMail;
use Mail;
use DB;
use App\User;

use Validator;
use Response;
use Illuminate\Support\Facades\Auth;

class ResetPasswordController extends Controller
{
  //
  public function sendPasswordResetEmail(Request $request) {
    if (!$this->validateEmail($request->email)) {
      return response()->json(['failed'=>'Email doesn\'t registered on Take Charge NXT'], 404);
    }

    $this->send($request->email);
    return response()->json(['success'=>'Reset Email is sent successfully, Please check your inbox'], 200);
  }

  public function validateEmail($email) {
    return !!User::where('email', $email)->first();
  }

  public function send($email) {
    $token = $this->createToken($email);
    Mail::to($email)->send(new ResetPasswordMail($email, $token));
  }

  public function createToken($email) {
    $old = DB::table('password_resets')->where('email', $email)->first();
    if ($old){
      return $old->token;
    }
    $token = str_random(60);
    $this->saveToken($token, $email);
    return $token;
  }

  public function saveToken($token, $email) {
    DB::table('password_resets')->insert([
      'email' => $email,
      'token' => $token
    ]);
  }

  public function resetPassword(Request $request) {
    $validator = Validator::make($request->all(), [
      'token' => 'required',
      'password'=> 'required'
    ]);
    if ($validator->fails()) {
      $error = $validator->errors();
      return response()->json(['failed'=>'Token or Password is not correct']);
    }
    $resetUser = $this->getPasswordResetTableRow($request);
    return  $resetUser ? $this->changePassword($resetUser->email, $request->password) : $this->tokenNotFoundResponse();
  }

  private function getPasswordResetTableRow($request) {
    return DB::table('password_resets')->where('token',$request->token)->first();
  }

  private function changePassword($email, $password) {
    $user = User::whereEmail($email)->first();
    $user->update(['password'=>bcrypt($password)]);
    $user = User::first();

    DB::table('password_resets')->where('email',$email)->delete();

    return response()->json(['success'=>'Password Successfully Changed', 'user'=>$user], 201);
  }

  private function tokenNotFoundResponse() {
    return response()->json(['error'=>'Token or Email is not correct'], 404);
  }
}
