@extends('backend.master')
@section('title', 'Barcode Generator')
@section('content')
    <div id="barcode-root"></div>
@endsection

@push('js')
    @viteReactRefresh
    @vite('resources/js/app.jsx')
@endpush
