<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form id="add-slider-form" action="@if(isset($slider->id)) {{ route('admin.sliders.update',[$slider->id]) }} @else {{ route('admin.sliders.store') }} @endif" method="POST" role="form" data-plugin="ajaxForm" enctype="multipart/form-data">
            @if(isset($slider->id))
                <input type="hidden" name="_method" value="PUT" />
            @endif
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><span id="pop-title">{{ isset($slider->id) ? 'Edit' : 'Add New' }}</span> slider</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-md-12">
                        <label class="control-label" for="district">Affiliate</label>
                        <select class="form-control select2" id="type" name="type" style="width: 100%;">
                            <option value="">Select Type</option>
                            @foreach(Utility::slider_type() as $id => $slider_type)
                                <option value="{{ $id }}" {{ isset($slider->id) && ($id == $slider->type) ? 'selected' : '' }}>{{ $slider_type }}</option>
                            @endforeach
                        </select>
                        {{--<input type="text" class="form-control" id="district" name="district" placeholder="Enter District"
                               autocomplete="off" value="{{ old('district', isset($clinic->district) ? $clinic->district : null) }}">--}}
                    </div>

                    <div class="form-group col-md-6">
                        <label for="image">Image</label>
                        <div id="dvUploadImage" class="{{ isset($slider->id)&&!empty($slider->image) ? 'hidden' : '' }}">
                            <input type="file" id="image" name="image">
                            <small>Upload images with a size of 1200x200</small>
                            <input type="hidden" name="is_image" id="is_image" value="1" >
                        </div>
                        @if(isset($slider->id)&&!empty($slider->image))
                            <div id="dvImage">
                                <div class="col-md-8 col-xs-7">
                                    <a href="{{ asset(Utility::DEFAULT_STORAGE . App\Models\Slider::FILE_DIRECTORY .  '/' . $slider->image) }}" target="_blank">
                                        <img src="{{ asset(Utility::DEFAULT_STORAGE . App\Models\Slider::FILE_DIRECTORY .  '/' . $slider->image) }}" alt="" height="50" />
                                    </a>

                                </div>
                                <div class="col-md-1 col-xs-2">
                                    <a id="testtrig" data-target="#dvImage" data-replace="#dvUploadImage" data-changevalue='[{"selector":"#is_image","value":0}]' class="icon wb-close close_data"></a>
                                </div>
                            </div>

                        @endif
                    </div>
                    <div class="form-group col-md-6">
                        <label for="name">Order</label>
                        <input type="text" class="form-control" id="order_no" name="order_no" placeholder="Enter Order" value="{{ isset($slider->order_no) ? ($slider->order_no == Utility::DEFAULT_DB_ORDER) ? '' : $slider->order_no : '' }}">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="btn-slider-add">{{ isset($slider->id) ? 'Update' : 'Create' }}</button>
            </div>
        </form>
    </div>
</div>
