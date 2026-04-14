@extends('errors.layout')

@section('title', '401')

@php
    $code = '401';
    $message = 'Kamu harus login terlebih dahulu untuk mengakses halaman ini.';
    $image = 'https://illustrations.popsy.co/yellow/crashed-error.svg';
@endphp