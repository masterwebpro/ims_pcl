@extends('layouts.master')
@section('title') User @endsection
@section('css')
@endsection
@section('content')

@component('components.breadcrumb')
@slot('li_1') User @endslot
@slot('title') Edit User @endslot
@endcomponent

<div class="card" id="tasksList">
    <div class="card-header border-0">
        <div class="d-flex align-items-center">
            <h5 class="card-title mb-0 flex-grow-1">&nbsp;</h5>
            <div class="flex-shrink-0">
                <button id="user-save" class="btn btn-success btn-label rounded-pill"><i class="bx bx-save label-icon align-middle rounded-pill fs-16 me-2"></i> Save</button>
            </div>
        </div>
    </div>
    <!--end card-body-->
</div>

<div class="card">
    <div class="card-body">
        <ul class="nav nav-tabs nav-justified nav-border-top nav-border-top-success mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" data-bs-toggle="tab" href="#nav-border-personal" role="tab" aria-selected="true">
                    <i class="ri-user-line align-middle me-1"></i> Personal Details
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#nav-border-login-details" role="tab" aria-selected="false" tabindex="-1">
                    <i class="ri-shield-user-line me-1 align-middle"></i> Login Details
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#nav-border-user-access" role="tab" aria-selected="false" tabindex="-1">
                    <i class="ri-menu-fill align-middle me-1"></i>User Access Menu
                </a>
            </li>

            <li class="nav-item" role="presentation">
                <a class="nav-link" data-bs-toggle="tab" href="#nav-border-user-permission" role="tab" aria-selected="false" tabindex="-1">
                    <i class="ri-key-fill align-middle me-1"></i>User Permission
                </a>
            </li>
        </ul>
        <div class="tab-content text-muted" id="form-user">
           
            <div class="tab-pane active" id="nav-border-personal" role="tabpanel">
                <div class="row">
                    <div class="col-9">
                        <div class="row g-3">
                            <div class="col-3">
                                @csrf
                                <input type="hidden" id="user_id" name="user_id" value="<?=isset($user->id) ? $user->id : '' ?>">
                                <input type="hidden" id="image_path" value="<?=isset($user->avatar) ? $user->avatar : '' ?>">
                                
                                <h6 class="text-muted text-uppercase fw-semibold mb-3">First Name <span class="text-danger">*</span></h6>
                                <p class=" mb-2">
                                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?=isset($user->first_name) ? $user->first_name : '' ?>" placeholder="First Name">
                                    <span class="text-danger error-msg first_name_error"></span>
                                </p>
                            </div>
                            
                            <div class="col-3">
                                <h6 class="text-muted text-uppercase fw-semibold mb-3">Middle Name </h6>
                                <p class=" mb-2">
                                    <input type="text" class="form-control" id="middle_name" name="middle_name" value="<?=isset($user->middle_name) ? $user->middle_name : '' ?>" placeholder="Middle Name">
                                </p>
                            </div>

                            <!--end col-->
                            <div class="col-3">
                                <h6 class="text-muted text-uppercase fw-semibold mb-3">Last Name <span class="text-danger">*</span></h6>
                                <p class=" mb-2">
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?=isset($user->last_name) ? $user->last_name : '' ?>" placeholder="Last Name">
                                    <span class="text-danger error-msg last_name_error"></span>
                                </p>
                            </div>

                            <div class="col-3">
                                <h6 class="text-muted text-uppercase fw-semibold mb-3">Is Active</h6>
                                <p class=" mb-2">
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" id="is_active" <?=($user->is_active == 1) ? 'checked' : '' ?> >
                                        <label class="form-check-label" for="is_active">Is Active</label>
                                    </div>
                                </p>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-3">
                                <h6 class="text-muted text-uppercase fw-semibold mb-3">Mobile Number <span class="text-danger">*</span></h6>
                                <p class=" mb-2">
                                    <input type="text" class="form-control" id="mobile_no" name="mobile_no" value="<?=isset($user->mobile_no) ? $user->mobile_no : '' ?>" placeholder="Mobile Number">
                                    <span class="text-danger error-msg mobile_no_error"></span>
                                </p>
                            </div>
                            <!--end col-->
                            <div class="col-3">
                                <h6 class="text-muted text-uppercase fw-semibold mb-3">Company</h6>
                                <p class=" mb-2">
                                    <input type="text" class="form-control" id="company" name="company" value="<?=isset($user->company) ? $user->company : '' ?>" placeholder="Company">
                                    <span class="text-danger error-msg company_error"></span>
                                </p>
                            </div>

                            <div class="col-3">
                                <h6 class="text-muted text-uppercase fw-semibold mb-3">Department </h6>
                                <p class=" mb-2">
                                    <input type="text" class="form-control" id="department" name="department" value="<?=isset($user->department) ? $user->department : '' ?>" placeholder="Department">
                                    <span class="text-danger error-msg middle_name_error"></span>
                                </p>
                            </div>

                            <div class="col-3">
                                <h6 class="text-muted text-uppercase fw-semibold mb-3">Position</h6>
                                <p class="mb-2">
                                    <input type="text" class="form-control" id="position" name="position" value="<?=isset($user->position) ? $user->position : '' ?>" placeholder="Postion">
                                    <span class="text-danger error-msg position_error"></span>
                                </p>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-3">
                                <h6 class="text-muted text-uppercase fw-semibold mb-3">Reported To <span class="text-danger">*</span></h6>
                                <p class="mb-2">
                                    <select class="form-select select2" required="reported_to" id="reported_to" name="warehouse">
                                        <option value="">Select Reported To</option>                                                            
                                    </select>
                                    <span class="text-danger error-msg reported_to_error"></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-3 ">
                        <form action="" id="avatar_user_upload" method="POST" enctype="multipart/form-data">
                            <div class="profilepic">
                                <img class="profilepic-image" src="{{ isset($user->avatar) ? $user->avatar : 'https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg' }}" width="200" height="200" alt="profile_picture" />
                                <div class="profilepic-content">
                                <span class="profilepic-icon"><i class="fas fa-camera"></i></span>
                                <span class="profilepic-text">Change Photo</span>
                                </div>
                            </div>
                            <div class="p-image">
                                <i class="fa fa-camera upload-button"></i>
                                <input class="file_upload" name="file_upload" id="file_upload" type="file" accept="image/*"/>
                            </div>
                        </form>
                        <!-- <button type="submit" class="btn btn-success">Upload</button> -->
                    </div>
                </div>
                
            </div>
            
            

            <div class="tab-pane" id="nav-border-login-details" role="tabpanel">
                <div class="row mb-2">
                    <div class="col">
                        <div class="form-floating mb-1">
                            <input type="email" class="form-control" value="<?=isset($user->email) ? $user->email : '' ?>" id="email_address" placeholder="Email Address" autocomplete="off">
                            <label for="name">Username/Email <span class="text-danger">*</span></label>
                            <span class="text-danger error-msg email_address_error"></span>
                        </div>
                    </div>
                </div>

                <div class="row mb-2 {{ ($is_create) ? 'd-none': '' }}">
                    <div class="col-6">
                        <div class="form-check">
                            <input class="form-check-input is_edit_password" name="is_edit_password" type="checkbox" value="" id="is_edit_password" autocomplete="off" >
                            <label class="form-check-label" for="is_edit_password">Edit password</label>
                        </div>
                    </div>
                </div>

                <div class="row mb-2 d-none"  id="password_row">
                    <div class="col col-md-6">
                        <div class="form-floating mb-1">
                            <input type="password" class="form-control" id="password" name="password" placeholder="*******" value="" >
                            <label for="new_password">New Password <span class="text-danger">*</span></label>
                            <span class="text-danger error-msg password_error"></span>
                        </div>
                    </div>
                    <div class="col col-md-6">
                        <div class="form-floating mb-1">
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="*******" value="" >
                            <label for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                            <span class="text-danger error-msg password_confirmation_error"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="nav-border-user-access" role="tabpanel">
                @include('users.components.useraccess')
            </div>

            <div class="tab-pane" id="nav-border-user-permission" role="tabpanel">
                @include('users.components.permission')
            </div>
        </div>
    </div><!-- end card-body -->
</div>

	@endsection
@section('script')

<script src="{{ URL::asset('assets/js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/select2/select2.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/cleave.js/cleave.js.min.js') }}"></script>
<script src="{{ URL::asset('assets/libs/masks/jquery.mask.min.js') }}"></script>

<script src="{{ URL::asset('assets/js/users/user.js') }}"></script>

<script src="{{ URL::asset('assets/js/app.min.js') }}"></script>


@endsection