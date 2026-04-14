@extends('errors.layout')

@section('title', '404')

@php
    $code = '404';
    $message = 'Halaman yang kamu cari tidak ditemukan';
    $image = 'https://illustrations.popsy.co/pink/crashed-error.svg';
@endphp