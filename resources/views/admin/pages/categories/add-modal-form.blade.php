<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form id="add-category-form" action="@if(isset($category->id)) {{ route('admin.categories.update',[$category->id]) }} @else {{ route('admin.categories.store') }} @endif" method="POST" role="form" data-plugin="ajaxForm" enctype="multipart/form-data">
            @if(isset($category->id))
                <input type="hidden" name="_method" value="PUT" />
            @endif
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><span id="pop-title">{{ isset($category->id) ? 'Edit' : 'Add New' }}</span> Category</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    @if(isset($slug))
                        <div class="form-group col-sm-12">
                            <input type="text" class="form-control" value="{{ config('app.website_url') }}/{{ $slug }}" disabled>
                        </div>
                    @endif
                    <div class="form-group col-md-12">
                        <label for="name">Name<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter Category Name" value="{{ isset($category->name) ? $category->name : '' }}">
                    </div>
                    {{-- @if(isset($category->id) && $category->have_parent())
                    @else
                        <div class="form-group col-md-12">
                            <label for="customer_id">Select Parent<span class="text-danger">*</span></label>
                            <select class="form-control select2" id="parent" name="parent[]" style="width: 100%;" multiple>
                                <option value="">Select</option>
                                @foreach($categoryLists as $categoryList)
                                    <option @if(isset($category->name) && ( in_array($categoryList->id,$category->parent_ids) )) selected @endif value="{{ $categoryList->id }}">{{ $categoryList->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif --}}
                    <div class="form-group col-md-6">
                        <label for="image">Image</label>
                        <input type="hidden" name="is_image" id="is_image" value="1" >
                        <div id="dvUploadImage" class="{{ isset($category->id)&&!empty($category->image) ? 'hidden' : '' }}">
                            <input type="file" id="image" name="image">
                            <small>Upload images with a size of 423x460</small>
                        </div>
                        @if(isset($category->id)&&!empty($category->image))
                            <div id="dvImage">
                                <div class="col-md-8 col-xs-7">
                                    <a href="{{ asset(Utility::DEFAULT_STORAGE . App\Models\Category::FILE_DIRECTORY .  '/' . $category->image) }}" target="_blank">
                                    <img src="{{ asset(Utility::DEFAULT_STORAGE . App\Models\Category::FILE_DIRECTORY .  '/' . $category->image) }}" alt="" height="50" />
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
                        <input type="text" class="form-control" id="order_no" name="order_no" placeholder="Enter Order" value="{{ isset($category->order_no) ? ($category->order_no == Utility::DEFAULT_DB_ORDER) ? '' : $category->order_no : '' }}">
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
                                               autocomplete="off" value="{{ old('site_title', isset($category->id) ? $category->site_title : null) }}">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="control-label">Site Keywords</label>
                                        <textarea class="form-control" id="site_keywords" name="site_keywords" placeholder="Enter Site Keywords">{{ old('site_keywords', isset($category->site_keywords) ? $category->site_keywords : null) }}</textarea>
                                        @if ($errors->has('site_keywords'))
                                            <span class="help-block">
                                        {{ $errors->first('site_keywords') }}
                                    </span>
                                        @endif
                                    </div>
                                    <div class="col-md-12">
                                        <label class="control-label">Site Description</label>
                                        <textarea class="form-control" id="site_description" name="site_description" placeholder="Enter Site Description">{{ old('site_description', isset($category->site_description) ? $category->site_description : null) }}</textarea>
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
                <button type="submit" class="btn btn-primary" id="btn-category-add">{{ isset($category->id) ? 'Update' : 'Create' }}</button>
            </div>
        </form>
    </div>
</div>
