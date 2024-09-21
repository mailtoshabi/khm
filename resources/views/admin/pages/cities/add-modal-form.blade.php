<div class="modal-dialog" role="document">
    <div class="modal-content">
        <form id="add-city-form" action="@if(isset($city->id)) {{ route('admin.cities.update',[$city->id]) }} @else {{ route('admin.cities.store') }} @endif" method="POST" role="form" data-plugin="ajaxForm" enctype="multipart/form-data">
            @if(isset($city->id))
                <input type="hidden" name="_method" value="PUT" />
            @endif
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><span id="pop-title">{{ isset($city->id) ? 'Edit' : 'Add New' }}</span> City</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    @if(isset($slug))
                        <div class="form-group col-sm-12">
                            <input type="text" class="form-control" value="{{ config('app.website_url') }}/{{ $slug }}" disabled>
                        </div>
                    @endif
                    <div class="form-group col-md-6">
                        <label for="name">Name<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter City Name" value="{{ isset($city->name) ? $city->name : '' }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label" for="district">District</label>
                        <select class="form-control select2" id="district_id" name="district_id" style="width: 100%;">
                            <option value="">Select District</option>
                            @foreach($districts as $id => $district)
                                <option {{ isset($city->id) && ($city->district_id == $id) ? 'selected' : '' }} value="{{ $id }}">{{ $district }}</option>
                            @endforeach
                        </select>
                        {{--<input type="text" class="form-control" id="district" name="district" placeholder="Enter District"
                               autocomplete="off" value="{{ old('district', isset($clinic->district) ? $clinic->district : null) }}">--}}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="btn-city-add">{{ isset($city->id) ? 'Update' : 'Create' }}</button>
            </div>
        </form>
    </div>
</div>