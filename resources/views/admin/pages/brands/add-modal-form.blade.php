<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form id="add-brand-form" action="@if(isset($user->id)) {{ route('admin.brands.update',[$user->id]) }} @else {{ route('admin.brands.store') }} @endif" method="POST" role="form" data-plugin="ajaxForm" enctype="multipart/form-data">
            @if(isset($user->id))
                <input type="hidden" name="_method" value="PUT" />
            @endif
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><span id="pop-title">{{ isset($user->id) ? 'Edit' : 'Add New' }}</span> Brand</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    @if(isset($slug))
                        <div class="form-group col-sm-12">
                            <input type="text" class="form-control" value="{{ config('app.website_url') }}/{{ $slug }}" disabled>
                        </div>
                    @endif
                        <div class="">
                            <div class="form-group col-sm-6 @if ($errors->has('email')) has-error @endif">
                                <label for="email">Email (User Name)</label>
                                <input class="form-control" id="email" name="email" placeholder="Enter User Name" value="{{ old('email', isset($user->email) ? $user->email : null) }}">
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        {{ $errors->first('email') }}
                                    </span>
                                @endif
                            </div>
                            {{--@if(!isset($user->id))--}}
                            <div class="form-group col-sm-6 @if ($errors->has('password')) has-error @endif">
                                <label for="password">Password</label>
                                <input class="form-control" id="password" name="password" placeholder="{{ isset($user->password) ? "Leave Blank If no change" : "Password" }}" >
                                @if ($errors->has('password'))
                                    <span class="help-block">
                                            {{ $errors->first('password') }}
                                        </span>
                                @endif
                            </div>
                            {{--@endif--}}
                        </div>
                    <div class="form-group col-md-12">
                        <label for="name">Name<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter Brand Name" value="{{ isset($user->name) ? $user->name : '' }}">
                    </div>
                    <div class="form-group col-sm-12">
                        <label for="name">URL Slug<span class="text-red">*</span> </label>
                        <input type="text" class="form-control" id="slug" name="slug" placeholder="Enter URL Slug" value="{{ isset($slug) ? $slug : '' }}" >
                    </div>
                    <div class="form-group col-md-12">
                        <label for="image">Image</label>
                        <input type="hidden" name="is_image" id="is_image" value="1" >
                        <div id="dvUploadImage" class="{{ isset($user->id)&&!empty($user->brand->image) ? 'hidden' : '' }}">
                            <input type="file" id="image" name="image">
                            <small>Upload images with a size of {{ Utility::IMAGE_BRAND }}</small>
                        </div>
                        @if(isset($user->id)&&!empty($user->brand->image))
                            <div id="dvImage">
                                <div class="col-md-8 col-xs-7">
                                    <a href="{{ asset(Utility::DEFAULT_STORAGE . App\Models\Brand::FILE_DIRECTORY .  '/' . $user->brand->image) }}" target="_blank">
                                        <img src="{{ asset(Utility::DEFAULT_STORAGE . App\Models\Brand::FILE_DIRECTORY .  '/' . $user->brand->image) }}" alt="" height="50" />
                                    </a>
                                    <a id="testtrig" data-target="#dvImage" data-replace="#dvUploadImage" data-changevalue='[{"selector":"#is_image","value":0}]' class="icon fa fa-trash close_data" style="padding-left: 15px; color: red;"></a>
                                </div>
                            </div>

                        @endif
                    </div>
                    <div class="form-group col-sm-12">
                        <label>Images</label>
                        <input type="hidden" name="is_images" id="is_images" value="1" >
                        <div id="dvUploadImageOther" class="{{ isset($user->id)&&!empty($user->brand->images) ? 'hidden' : '' }}">
                            <input type="file" id="images" name="images[]" multiple>
                            <p class="help-block">Upload images with a size of {{ Utility::IMAGE_INDIVIDUAL_BRAND }}</p>
                        </div>
                        @if(isset($user->id)&&!empty($user->brand->images))
                            <div id="dvImageOther">
                                <div class="col-md-8 col-xs-7">
                                    @foreach($user->brand->images as $otherImage)
                                        <a href="{{ asset(Utility::DEFAULT_STORAGE . App\Models\Brand::FILE_DIRECTORY .  '/' . $otherImage) }}" target="_blank">
                                            <img src="{{ asset(Utility::DEFAULT_STORAGE . App\Models\Brand::FILE_DIRECTORY .  '/' . $otherImage) }}" alt="" height="50" />
                                        </a>
                                    @endforeach
                                </div>
                                <div class="col-md-1 col-xs-2">
                                    <a data-target="#dvImageOther" data-replace="#dvUploadImageOther" data-changevalue='[{"selector":"#is_images","value":0}]' class="icon wb-close close_data"></a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <label class="control-label">SEO Tools</label>
                        <div id="dv_seotools" class="well">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="control-label">Site Title</label>
                                        <input type="text" class="form-control" id="site_title" name="site_title" placeholder="Enter Site Title"
                                               autocomplete="off" value="{{ old('site_title', isset($user->id) ? $user->brand->site_title : null) }}">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="control-label">Site Keywords</label>
                                        <textarea class="form-control" id="site_keywords" name="site_keywords" placeholder="Enter Site Keywords">{{ old('site_keywords', isset($user->id) ? $user->brand->site_keywords : null) }}</textarea>
                                        @if ($errors->has('site_keywords'))
                                            <span class="help-block">
                                        {{ $errors->first('site_keywords') }}
                                    </span>
                                        @endif
                                    </div>
                                    <div class="col-md-12">
                                        <label class="control-label">Site Description</label>
                                        <textarea class="form-control" id="site_description" name="site_description" placeholder="Enter Site Description">{{ old('site_description', isset($user->id) ? $user->brand->site_description : null) }}</textarea>
                                        @if ($errors->has('site_description'))
                                            <span class="help-block">
                                                {{ $errors->first('site_description') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="btn-brand-add">{{ isset($user->id) ? 'Update' : 'Create' }}</button>
            </div>
        </form>
    </div>
</div>
