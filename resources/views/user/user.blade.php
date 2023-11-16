@extends('layouts.apk')
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- MULAI STYLE CSS -->
    <style>
        #profile {
            width: 250px;
        }
    </style>

    {{-- header primary --}}
    <div class="panel-header bg-primary-gradient">
        <div class="page-inner py-5">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
                <div>
                    <h2 class="text-white pb-2 fw-bold">My Profil</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 align-items-center text-center">
                            <img src="{{ url('foto/' . Auth::user()->avatar) }}" id="profile" class="rounded-circle"
                                alt=".....">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group col-sm-12">
                            <label for="name" class="control-label">Nama</label>
                            <input class="form-control" value="{{ Auth::user()->name }}" readonly>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group col-sm-12">
                            <label for="name" class="control-label">Email</label>
                            <input class="form-control" value="{{ Auth::user()->email }}" readonly>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group col-sm-12">
                            <label for="name" class="control-label">No Phone</label>
                            <input class="form-control" value="{{ Auth::user()->phone_number }}" readonly>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group col-sm-12">
                            <label for="name" class="control-label">Jabatan</label>
                            <input class="form-control" value="{{ Auth::user()->role }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
