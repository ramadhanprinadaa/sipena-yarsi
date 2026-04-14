@extends('errors.layout')

@section('title', '500')

@php
    $code = '500';
    $message = 'Terjadi kesalahan pada server. Silakan coba beberapa saat lagi.';
    $image = 'https://illustrations.popsy.co/purple/crashed-error.svg';
@endphp