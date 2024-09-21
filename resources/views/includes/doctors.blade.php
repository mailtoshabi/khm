<span>
    <div class="clearfix"></div>

    @if(!empty($doctor->images))
        <div class="col-md-12">
            <div class="row">
                {{--<div id="doctor-slider" class="content-slider">--}}
                <div id="doctor-slider" class="flexslider" style="width: 100%;">
                    <ul class="slides">
                    @foreach($doctor->images as $orginal_img)
                        <li>
                            <img class="hasborder" src="{{ asset(Utility::DEFAULT_STORAGE . $orginal_img['original']) }}" alt=""/>
                        </li>
                    @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif
    <div class="doctor_list" style="margin-bottom: 10px;">
        <div class="row">
            <div class="col-md-12">
                <div class="doctor_data" style="width: 100%;">
                    <div class="row">
                    <div class="col-md-6 col-xs-6" >
                        <p class="upload_prescrip" style=""><a data-toggle="modal" href="#doctorModal"><i class="fa fa-user"></i> View Profile</a></p>
                    </div>
                    <div class="col-md-6 col-xs-6" >
                        <p class="upload_prescrip" style=""><a href="tel:{{ str_replace(' ','',$clinic_phone) }}" target="_blank"><i class="fa fa-phone"></i> Book Appointment</a></p>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</span>

<!-- Modal -->
<div id="doctorModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ $doctor->name }}
                    @if(!empty($doctor->avail_time))
                        <br><small style="margin:0 ">{{ $doctor->avail_time }}</small>
                    @endif
                </h4>
            </div>
            <div class="modal-body">
                @if(!empty($doctor->description))
                <h5 class="modal-title">About</h5>
                {!! $doctor->description !!}
                @endif
                <h5 class="modal-title">Treatments</h5>
                <p>
                    <?php $count =1; ?>
                    @foreach($doctor->treatments as $treatment)
                        {{ $treatment->name }}
                        {{ $count!=count($doctor->treatments) ? ',' : '' }}
                            <?php $count++; ?>
                    @endforeach
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
