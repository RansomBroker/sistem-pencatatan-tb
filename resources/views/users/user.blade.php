@extends('master')
@section('title', 'User')
@section('content')
    <div class="container-fluid p-0">
        {{-- csrf --}}
        @csrf

        {{-- title --}}
        <div class="mb-3">
            <h2 class="h2 d-inline align-middle fw-bold">Users</h2>
        </div>

        {{-- card --}}
        <div class="card card-body row d-flex flex-column flex-wrap">

            @if($message = Session::get('message'))
                @if($status = Session::get('status'))
                    <div class="alert alert-{{ $status}} alert-dismissible fade show" role="alert">
                        {{ $message }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            @endif

            <a href="{{ URL::to('user/user-add') }}" class="col-12 col-lg-2 btn btn-secondary mb-3"> <i class='bx bx-plus'></i> Add New User</a>

            <div class="card card-body shadow-lg">
                <h3 class="d-inline align-middle"> Data User </h3>
                <div class="table-responsive">
                    <table class="table table-striped table-hover w-100" id="user-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user['name'] }}</td>
                                    <td>
                                        @if($user['role'] == 0)
                                            {{ 'Admin' }}
                                        @endif
                                            @if($user['role'] == 1)
                                                {{ 'User' }}
                                            @endif
                                    </td>
                                    <td>
                                        <a href="{{ URL::to('user/user-edit/') .'/'. $user['id'] }}" class="btn btn-success">Edit</a>
                                        <button class="btn-delete btn btn-danger" data-id="{{ $user['id'] }}">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('custom-js')
    <script>
        $(document).ready(function () {
            $("#user-table").DataTable()

            $('.btn-delete').on('click', function() {
                let userID = $(this).attr("data-id");
                swal.fire({
                    icon: 'question',
                    title: 'Konfirmasi Penghapusan User',
                    text: 'Apakah anda yakin akan menghapus user ',
                    showCancelButton: true,
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ URL::to('user/user-delete') }}" + '/' + userID,
                            method: "GET",
                            beforeSend: function () {
                                Swal.fire({
                                    html: `
                                            <div class="d-flex justify-content-center fs-4 ">
                                                  <span class="spinner-border spinner-border-sm text-primary fs-4" role="status" aria-hidden="true"></span>
                                                    Loading...
                                            </div>
                                        `,
                                    showConfirmButton: false,
                                    allowOutsideClick: false,
                                    allowEscapeKey: false
                                })
                            },
                            success: function (response) {
                                console.log(response)
                                if (response.status === "success") {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil Menghapus User',
                                        text: response.message,
                                        showConfirmButton: false,
                                        allowOutsideClick: false,
                                        allowEscapeKey: false
                                    })
                                    setTimeout(function () {
                                        window.location.reload();
                                    }, 1250)
                                }

                                if (response.status === "failed") {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal Menghapus User',
                                        text: response.message,
                                        showConfirmButton: false,
                                        allowOutsideClick: false,
                                        allowEscapeKey: false
                                    })
                                    setTimeout(function () {
                                        window.location.reload();
                                    }, 1250)
                                }
                            }
                        })
                    }
                })
            })
        })
    </script>
@endsection
