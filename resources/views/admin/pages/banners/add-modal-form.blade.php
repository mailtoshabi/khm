<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form id="add-banner-form" action="@if(isset($banner->id)) {{ route('admin.banners.update',[$banner->id]) }} @else {{ route('admin.banners.store') }} @endif" method="POST" role="form" data-plugin="ajaxForm" enctype="multipart/form-data">
            @if(isset($banner->id))
                <input type="hidden" name="_method" value="PUT" />
            @endif
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><span id="pop-title">{{ isset($banner->id) ? 'Edit' : 'Add New' }}</span> banner</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="link">Link</label>
                        <input type="text" class="form-control" id="link" name="link" placeholder="Enter Link" value="{{ isset($banner->link) ? $banner->link : '' }}">
                    </div>
                    <div class="form-group col-md-12">
                        <label class="control-label" for="district">Affiliate</label>
                        <select class="form-control select2" id="user_id" name="user_id" style="width: 100%;">
                            <option value="">Select Affiliate</option>
                            <option value="{{ Auth::user()->id }}">{{ Auth::user()->name . ' - (ADMIN)' }}</option>
                            @foreach($users as $id => $user)
                                <option value="{{ $id }}" {{ isset($banner->id) && ($id == $banner->user_id) ? 'selected' : '' }}>{{ $user }}</option>
                            @endforeach
                        </select>
                        {{--<input type="text" class="form-control" id="district" name="district" placeholder="Enter District"
                               autocomplete="off" value="{{ old('district', isset($clinic->district) ? $clinic->district : null) }}">--}}
                    </div>
                    <div class="form-group col-md-8">
                        <label for="image">Image</label>
                        <div id="dvUploadImage" class="{{ isset($banner->id)&&!empty($banner->image) ? 'hidden' : '' }}">
                            <input type="file" id="image" name="image">
                            <small>Upload images with a size of 1200x200</small>
                            <input type="hidden" name="is_image" id="is_image" value="1" >
                        </div>
                        @if(isset($banner->id)&&!empty($banner->image))
                            <div id="dvImage">
                                <div class="col-md-8 col-xs-7">
                                    <a href="{{ asset(Utility::DEFAULT_STORAGE . App\Models\Banner::FILE_DIRECTORY .  '/' . $banner->image) }}" target="_blank">
                                        <img src="{{ asset(Utility::DEFAULT_STORAGE . App\Models\Banner::FILE_DIRECTORY .  '/' . $banner->image) }}" alt="" height="50" />
                                    </a>
                                </div>
                                <div class="col-md-1 col-xs-2">
                                    <a id="testtrig" data-target="#dvImage" data-replace="#dvUploadImage" data-changevalue='[{"selector":"#is_image","value":0}]' class="icon wb-close close_data"></a>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="form-group col-md-4">
                        <label for="name">Order</label>
                        <input type="text" class="form-control" id="order_no" name="order_no" placeholder="Enter Order" value="{{ isset($banner->order_no) ? ($banner->order_no == Utility::DEFAULT_DB_ORDER) ? '' : $banner->order_no : '' }}">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="btn-banner-add">{{ isset($banner->id) ? 'Update' : 'Create' }}</button>
            </div>
        </form>
    </div>
</div>
