@php
  $configData = Helper::appClasses();
@endphp

@extends('layouts/blankLayout')

@section('title', 'Document Editor')

@section('content')

  <div style="height: 100vh;">
    <div id="editor"></div>
  </div>

  <script src="{{$serverUrl}}/web-apps/apps/api/documents/api.js"></script>
  <script>
    const payload = {!! $config !!};
    new DocsAPI.DocEditor('editor', payload);
  </script>

@endsection


