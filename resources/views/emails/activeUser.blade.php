<!DOCTYPE html>
<html>
<head>
  <title>You are activated by Admin</title>
</head>

<body style="max-width: 600px">
<div style="padding: 10px;">
  <div style="text-align: center">
    {{--<img src="localhost:8080/img/logo.png" style="width: 300px" />--}}
    <p>Activated Email</p>
  </div>
  <p style="font-size: 24px">Hi {{$email}}</p>
  <p style="font-size: 16px;">
    You are allowed to login into UKCourier Website with this email address ({{$email}}).
  </p>
  
  <br/>
  <p style="font-size: 20px;">
    Regards, <br/>
    The UKCourier Team
  </p>
</div>
</body>

</html>
