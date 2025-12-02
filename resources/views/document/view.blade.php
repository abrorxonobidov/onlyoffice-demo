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

  <div class="nav-align-top nav-tabs-shadow">
    <ul class="nav nav-tabs nav-fill" role="tablist">
      <li class="nav-item">
        <button
          type="button"
          class="nav-link active"
          role="tab"
          data-bs-toggle="tab"
          data-bs-target="#navs-justified-home"
          aria-controls="navs-justified-home"
          aria-selected="true">
        <span class="d-none d-sm-inline-flex align-items-center">
          <i class="icon-base ti tabler-file-type-pdf icon-sm me-1_5"></i>PDF
        </span>
          <i class="icon-base ti tabler-file-type-pdf icon-sm d-sm-none"></i>
        </button>
      </li>
      <li class="nav-item">
        <button
          type="button"
          class="nav-link"
          role="tab"
          data-bs-toggle="tab"
          data-bs-target="#navs-justified-profile"
          aria-controls="navs-justified-profile"
          aria-selected="false">
        <span class="d-none d-sm-inline-flex align-items-center"
        ><i class="icon-base ti tabler-file-info icon-sm me-1_5"></i>Info</span
        >
          <i class="icon-base ti tabler-file-info icon-sm d-sm-none"></i>
        </button>
      </li>
    </ul>
    <div class="tab-content">
      <div class="tab-pane fade show active" id="navs-justified-home" role="tabpanel">
        <a href="{{route('document-index')}}"
           class="btn text-white btn-primary rounded-pill btn-icon me-5"
           data-bs-toggle="tooltip"
           data-bs-placement="top"
           data-bs-original-title="Document list"
           data-bs-custom-class="tooltip-warning"
        >
          <i class="icon-base ti tabler-list"></i>
        </a>
        <a href="{{route('document-edit', ['code' => $document->code])}}"
           class="btn text-white btn-info rounded-pill btn-icon"
           data-bs-toggle="tooltip"
           data-bs-placement="top"
           data-bs-original-title="Edit"
           data-bs-custom-class="tooltip-info"
        >
          <i class="icon-base ti tabler-pencil"></i>
        </a>
        <form action="{{route('document-delete')}}" method="POST" class="d-inline">
          @csrf
          <input type="text" class="d-none" name="code" value="{{$document->code}}" />
          <button type="submit" class="btn text-white btn-danger rounded-pill btn-icon waves-effect waves-light"
                  data-bs-toggle="tooltip"
                  data-bs-placement="top"
                  data-bs-original-title="Delete"
                  data-bs-custom-class="tooltip-danger"
          >
            <i class="icon-base ti tabler-trash"></i>
          </button>
        </form>
        <div
          id="{{$document->code}}"
          class="pdf-viewer"
          data-name="{{$document->name}}"
          data-check-url="{{route('check-pdf', ['code' => $document->code])}}"
          data-get-url="{{route('get-pdf', ['code' => $document->code])}}"
        >
          <div class="spinner-border text-primary" role="status"></div>
          <p>Loading...</p>
        </div>
      </div>
      <div class="tab-pane fade" id="navs-justified-profile" role="tabpanel">
        <div class="table-responsive table-bordered">
        <table class="table mb-0">
          <tbody>
          <tr>
            <th scope="row">1</th>
            <td>ID</td>
            <td>{{$document->id}}</td>
          </tr>
          <tr>
            <th scope="row">1</th>
            <td>Name</td>
            <td>{{$document->name}}</td>
          </tr>
          <tr>
            <th scope="row">2</th>
            <td>File extension</td>
            <td>{{$document->ext}}</td>
          </tr>
          <tr>
            <th scope="row">3</th>
            <td>Table cell</td>
            <td>{{$document->created_at}}</td>
          </tr>
          </tbody>
        </table>
      </div>
      </div>
    </div>
  </div>

@endsection

