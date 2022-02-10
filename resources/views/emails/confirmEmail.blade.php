<!DOCTYPE html>
<html>
<head>
  <title>Hi {{$user['full_name']}}</title>
</head>

<body>
<p style="font-size: 24px">Hi {{$user['full_name']}}</p>
<br/>
<p style="font-size: 16px;">{{$user['email']}} is trying to signup to takechargenxt.com</p>
<p style="font-size: 16px;">Please enter the following verification code to verify this signup attempt.</p>
<p style="text-align:center; font-size: 24px">{{$verification_code}}</p>

<p style="font-size: 20px;">Regards,</p>
<p style="font-size: 20px;">
The TakeChargeNXT Team
</p>
</body>

</html>
