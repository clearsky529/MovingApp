@extends('auth.frontend.layout.master')
@section('page-title','Landing page')
@section('content')
{!! html_entity_decode($slug->description) !!}
@endsection