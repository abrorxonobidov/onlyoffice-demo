@php
  $configData = Helper::appClasses();
@endphp


@extends('layouts/layoutMaster')

@section('title', $document->name)

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
    id="{{$document->code}}"
    class="pdf-viewer"
    data-name="{{$document->name}}"
    data-check-url="{{route('check-pdf', ['code' => $document->code])}}"
    data-get-url="{{route('get-pdf', ['code' => $document->code])}}"
  >
    <div class="spinner-border text-primary" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
    <p>Loading...</p>
  </div>
@endsection

