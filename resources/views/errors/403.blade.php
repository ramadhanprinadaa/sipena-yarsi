@extends('errors.layout')

@section('title', '403')

@php
    $code = '403';
    $message = 'Kamu tidak memiliki izin untuk mengakses halaman ini.';
    $image = 'https://illustrations.popsy.co/red/crashed-error.svg';
@endphp