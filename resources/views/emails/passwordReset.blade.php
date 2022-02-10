<!DOCTYPE html>
<html>
<head>
  <title>Reset your password</title>
</head>

<body style="max-width: 600px">
<div style="padding: 10px;">
  <div style="text-align: center">
    {{--<img src="localhost:8080/img/logo.png" style="width: 300px" />--}}
    <p>Reset Password</p>
  </div>
  <p style="font-size: 24px">Hi {{$email}}</p>
  <p style="font-size: 16px;">
    You requested to reset the password for your Take Charge NXT account with the email address ({{$email}}).
    Please click this button to reset your password.
    <br/>
    If this button is not working, please copy and paste below url to your browser.
  </p>
  @component('mail::button', ['url' => 'http://localhost:8080/reset-password?token='.$token])
    Change Password
  @endcomponent

  <a href="http://localhost:8080/reset-password?token={{$token}}">http://localhost:8080/reset-password?token={{$token}}</a>
  <br/>
  <p style="font-size: 20px;">
    Regards, <br/>
    The TakeChargeNXT Team
  </p>
</div>
</body>

</html>
