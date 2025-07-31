@php
  $configData = Helper::appClasses();
@endphp


@extends('layouts/layoutMaster')

@section('title', 'Document Editor')


<!-- Vendor Styles -->
@section('vendor-style')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
    'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
    'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
    'resources/assets/vendor/libs/@form-validation/form-validation.scss',
  ])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
  @vite([
    'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/popular.js',
    'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/auto-focus.js',
  ])
@endsection

<!-- Page Scripts -->
@section('page-script')
  @vite(['resources/assets/js/document-index.js'])
@endsection


@section('content')

  <div class="card">
    <h5 class="card-header">File upload</h5>
    <div class="card-body demo-vertical-spacing demo-only-element">
      <div class="row">
        <div class="col-6">
          <form method="POST" id="file-upload-form" onsubmit="return false" action="{{ route('document-upload') }}"
                enctype="multipart/form-data">
            @csrf
            <div class="input-group">
              <input type="file" name="file" class="form-control" id="file-upload-input">
              <button class="btn btn-primary waves-effect" type="submit" id="file-upload-input">
                <i class="icon-base ti tabler-upload"></i> &nbsp;
                Upload
              </button>
            </div>
          </form>
        </div>
        <div class="col-6">
          <form method="POST" id="form-add-new-file" onsubmit="return false" action="{{ route('document-create') }}">
            @csrf
            <div class="input-group input-group-validator">
              <input type="text" name="name" class="form-control" id="name-input" value="New File">
              <select name="ext" class="form-control select2">
                <option value="docx">
                  Document (.docx)
                </option>
                <option value="xlsx">
                  Spreadsheet (.xlsx)
                </option>
                <option value="pptx">
                  Presentation (.pptx)
                </option>
                <option value="pdf">
                  PDF (.pdf)
                </option>
              </select>
              <button class="btn btn-primary waves-effect" type="submit" id="name-input">
                <i class="icon-base ti tabler-plus"></i> &nbsp;
                Create
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="card-datatable table-responsive">
      <table class="table table-bordered">
        <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>File Type</th>
          <th>Pdf</th>
          <th>Created at</th>
          <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($documentPagination->getCollection() as $key => $document)
          <tr class="odd">
            <td>{{$documentPagination->perPage() * ($documentPagination->currentPage()-1) + $key+1 }}</td>
            <td>{{$document->name}}</td>
            <td><i class="icon-base ti tabler-file-type-{{$document->documentTypeIcon()}}"></i></td>
            <td>{{$document->pdfStatusLabel()}}</td>
            <td>{{$document->created_at}}</td>
            <td>
              <a href="{{route('document-edit', ['code' => $document->code])}}"
                 class="btn text-white btn-info rounded-pill btn-icon"
                 data-bs-toggle="tooltip"
                 data-bs-placement="top"
                 data-bs-original-title="Edit"
                 data-bs-custom-class="tooltip-info"
              >
                <i class="icon-base ti tabler-pencil"></i>
              </a>
              <a href="{{route('document-view', ['code' => $document->code])}}"
                 class="btn text-white btn-primary rounded-pill btn-icon"
                 data-bs-toggle="tooltip"
                 data-bs-placement="top"
                 data-bs-original-title="View"
                 data-bs-custom-class="tooltip-primary"
              >
                <i class="icon-base ti tabler-eye"></i>
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
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    <div class="card-footer">
      <div class="col-12 dt-container dt-bootstrap5">
        {{$documentPagination->appends(request()->query())->links()}}
      </div>
    </div>

  </div>

@endsection

