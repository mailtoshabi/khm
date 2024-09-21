@if (Session::has('success'))

    toastr.success('{{ Session::get('success') }}', null, {
    containerId:"toast-topFullWidth",
    positionClass:"toast-top-full-width",
    showMethod:"slideDown",
    closeButton: true
    });

@endif
@if (Session::has('error'))

    toastr.error('{{ Session::get('error') }}', null, {
    containerId:"toast-topFullWidth",
    positionClass:"toast-top-full-width",
    showMethod:"slideDown",
    closeButton: true
    });

@endif


{{--
toastr.success('Update succesfully', "Success", {
            containerId:"toast-topFullWidth",
            positionClass:"toast-top-full-width",
            showMethod:"slideDown",
            closeButton: true
    });--}}
		
	