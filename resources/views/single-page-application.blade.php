{{--<!DOCTYPE html>--}}
{{--<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">--}}
    {{--<head>--}}
        {{--<meta charset="utf-8">--}}
        {{--<meta http-equiv="X-UA-Compatible" content="IE=edge">--}}
        {{--<meta name="viewport" content="width=device-width, initial-scale=1">--}}
        {{--<meta name="csrf-token" content="{{ csrf_token() }}">--}}
        {{--<title>{{ config('app.name', 'Laravel') }}</title>--}}
        {{--<script src="{{ asset('js/app.js') }}" defer></script>--}}
        {{--<link rel="dns-prefetch" href="//fonts.gstatic.com">--}}
        {{--<link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">--}}
        {{--<link href="{{ asset('css/app.css') }}" rel="stylesheet">--}}
    {{--</head>--}}
    {{--<body>--}}
        {{--<div id="app"></div>--}}
    {{--</body>--}}
{{--</html>--}}

@php
    @include_once 'index.html';
@endphp
