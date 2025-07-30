@php
  $configData = Helper::appClasses();
@endphp


@extends('layouts/layoutMaster')

@section('title', $doc->name)



<!-- Vendor Scripts -->
@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/pdfjs/pdf.js',
  ])
@endsection

<!-- Page Scripts -->
@section('page-script')
  @vite(['resources/assets/js/pdf-view.js'])
@endsection


@section('content')
  {{json_encode($doc->attributesToArray())}}
@endsection

