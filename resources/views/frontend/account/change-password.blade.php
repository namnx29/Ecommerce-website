@extends('frontend.layouts.app')


@section('content')
<main>
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('frontend.account.profile') }}">Tài khoản</a></li>
                    <li class="breadcrumb-item">Đổi mật khẩu</li>
                </ol>
            </div>
        </div>
    </section>

    <section class=" section-11 ">
        <div class="container  mt-5">
            <div class="row">
                <div class="col-md-12">
                    @include('frontend.account.common.message')
                </div>
                <div class="col-md-3">
                    @include('frontend.account.common.sidebar')
                </div>
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="h5 mb-0 pt-2 pb-2">Đổi mật khẩu</h2>
                        </div>
                        <form action="" method="post" id="changePasswordForm" name="changePasswordForm">
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="mb-3">
                                        <label for="name">Mật khẩu cũ</label>
                                        <input type="password" name="old_password" id="old_password" placeholder="Mật khẩu cũ" class="form-control">
                                        <p></p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="name">Mật khẩu mới</label>
                                        <input type="password" name="new_password" id="new_password" placeholder="Mật khẩu mới" class="form-control">
                                        <p></p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="name">Xác nhận mật khẩu mới</label>
                                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Xác nhận mật khẩu mới" class="form-control">
                                        <p></p>
                                    </div>
                                    <div class="d-flex">
                                        <button id="submit" name="submit" type="submit" class="btn btn-dark">Lưu</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection

@section('customJS')
    <script>
        $("#changePasswordForm").submit(function(event){
            event.preventDefault();

            $.ajax({
                url: '{{route("frontend.account.processChangePassword")}}',
                type: 'post',
                data: $(this).serializeArray(),
                dataType: 'json',
                success: function(response) {
                    //$("#submit").prop('disabled', false);
                    if (response.status == true) {

                        window.location.href="{{ route('frontend.account.changePassword')}}";

                        $("#old_password").removeClass('is-invalid')
                        .siblings('p')
                        .removeClass('invalid-feedback').html("");

                        $("#new_password").removeClass('is-invalid')
                        .siblings('p')
                        .removeClass('invalid-feedback').html("");

                        $("#confirm_password").removeClass('is-invalid')
                        .siblings('p')
                        .removeClass('invalid-feedback').html("");
                    } else {
                        var errors = response['errors'];
                        if (errors['old_password']) {
                            $("#old_password").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback').html(errors['old_password']);
                        } else {
                            $("#old_password").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                        }

                        if (errors['new_password']) {
                            $("#new_password").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback').html(errors['new_password']);
                        } else {
                            $("#new_password").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                        }

                        if (errors['confirm_password']) {
                            $("#confirm_password").addClass('is-invalid')
                            .siblings('p')
                            .addClass('invalid-feedback').html(errors['confirm_password']);
                        } else {
                            $("#confirm_password").removeClass('is-invalid')
                            .siblings('p')
                            .removeClass('invalid-feedback').html("");
                        }
                    }
                }
            });
        })
    </script>
@endsection
