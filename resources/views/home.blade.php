@extends('layouts.master')

@section('content')

@auth
    @if (auth()->user()->type == 'admin')
        <a href="admin/dashboard">Admin Panel</a>
    @endif
@endauth

@endsection
