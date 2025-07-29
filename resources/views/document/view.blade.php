@php
  $configData = Helper::appClasses();
@endphp


@extends('layouts/layoutMaster')

@section('title', $doc->name)

@section('content')
  {{json_encode($doc->attributesToArray())}}
@endsection

