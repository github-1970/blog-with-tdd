@extends('layouts.master')

@section('content')

@auth
    <form action="{{route('posts.comments.store', $post)}}">
    </form>
@endauth

@endsection
