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
    'resources/assets/vendor/libs/moment/moment.js',
    'resources/assets/vendor/libs/flatpickr/flatpickr.js',
    'resources/assets/vendor/libs/@form-validation/popular.js',
    'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
    'resources/assets/vendor/libs/@form-validation/auto-focus.js',
  ])
@endsection

@section('content')


  <div class="card">
    <h5 class="card-header">File upload</h5>
    <div class="card-body demo-vertical-spacing demo-only-element">
      <form method="POST" action="{{ route('document-upload') }}" enctype="multipart/form-data">
        @csrf
        <div class="input-group">
          <input type="file" name="file" class="form-control" id="file-upload-input">
          <button class="btn btn-outline-primary waves-effect" type="submit" id="file-upload-input">
            Save
          </button>
        </div>
      </form>
    </div>

    <div class="card-datatable table-responsive">
      <table class="table table-bordered">
        <thead>
        <tr>
          <th>#</th>
          <th>ID</th>
          <th>Name</th>
          <th>Created at</th>
          <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($documentPagination as $key => $document)
          <tr class="odd">
            <td>{{$documentPagination->perPage() * ($documentPagination->currentPage()-1) + $key+1 }}</td>
            <td>{{$document->id}}</td>
            <td>{{$document->name}}</td>
            <td>{{$document->created_at}}</td>
            <td>
              <a href="{{route('document-edit', ['id' => $document->id])}}"
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
                <input type="text" class="d-none" name="document_id" value="{{$document->id}}" />
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
    <div class="card-body">
      <div class="col-12">
        {{$documentPagination->appends(request()->query())->links()}}
      </div>
    </div>

  </div>

@endsection

