<!DOCTYPE html>
<html>
<head>
  <title>Please Verify your email</title>
</head>

<body style="max-width: 600px">
<div style="padding: 10px;">
  <div style="text-align: center">
    {{--<img src="localhost:8080/img/logo.png" style="width: 300px" />--}}
    <p>Verify Email</p>
  </div>
  <p style="font-size: 24px">Hi {{$email}}</p>
  <p style="font-size: 16px;">
    You requested to register Seventh Root with the email address ({{$email}}).
    Please click this button to verify your email.
    <br/>
    If this button is not working, please copy and paste below url to your browser.
  </p>
  @component('mail::button', ['url' => 'http://localhost:8080/confirm-user?token='.$token])
    Verify Email
  @endcomponent

  <a href="http://localhost:8080/confirm-user?token={{$token}}">http://localhost:8080/confirm-user?token={{$token}}</a>
  <br/>
  <p style="font-size: 20px;">
    Regards, <br/>
    The Seventh Root Team
  </p>
</div>
</body>

</html>
