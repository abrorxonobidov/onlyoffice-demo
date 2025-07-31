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

<!-- Vendor Style -->
@section('vendor-style')
  @vite(['resources/assets/vendor/libs/pdfjs/pdf.scss'])
@endsection

<!-- Page Scripts -->
@section('page-script')
  @vite(['resources/assets/js/pdf-view.js'])
@endsection


@section('content')
  <div
    id="{{$doc->code}}"
    class="pdf-viewer"
    data-name="{{$doc->name}}"
    data-check-url="{{route('check-pdf', ['code' => $doc->code])}}"
    data-get-url="{{route('get-pdf', ['code' => $doc->code])}}"
  >
    <div class="spinner-border text-primary" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
  </div>
@endsection

