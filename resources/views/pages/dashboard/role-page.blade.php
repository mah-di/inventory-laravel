@extends('layout.sidenav-layout')

@section('content')
    @include('components.role.role-list')
    @include('components.role.role-delete')
    @include('components.role.role-create')
    @include('components.role.role-update')
@endsection
