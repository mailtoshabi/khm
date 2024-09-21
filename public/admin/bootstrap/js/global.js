$(document).ready(function() {

	/*X-CSRF-TOKEN*/
	$.ajaxSetup({
		headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
	});

	/*X-CSRF-TOKEN*/

/*----------------- POPUP START -------------------*/
	//----- OPEN
	$(document).on("click", '[data-popup-open]', function(e) {
		e.preventDefault();
		var targeted_popup_class = jQuery(this).attr('data-popup-open');
		$('[data-popup="' + targeted_popup_class + '"]').fadeIn(350);
		var update_url = $(this).data('target');
		if(typeof(update_url)  === "undefined") { }else {
				var url = $(this).attr('href');
			var data = {url:url, update_url:update_url};
				/*console.log(update_url);*/
				$(this).trigger('data-popup.edit', data);
		}

	});

	//----- CLOSE
	$(document).on("click", '[data-popup-close]', function(e) {
		var targeted_popup_class = jQuery(this).attr('data-popup-close');
		$('[data-popup="' + targeted_popup_class + '"]').fadeOut(350);
		$(this).trigger('hidden.bs.modal');
		e.preventDefault();
	});
	/*----------------- POPUP END -------------------*/

	/*----------------- POPUP INFO -------------------*/
	$(document).on("focusin", '[data-popupinfo]', function(e) {
		var target = $(this).attr('data-popupinfo');
		$(target).removeClass('hidden');
		$(target).parent().addClass('make_layer');
		$(target).addClass('layer');
	});

	$(document).on("focusout", '[data-popupinfo]', function(e) {
		var target = $(this).attr('data-popupinfo');
		$(target).addClass('hidden');
	});
	/*----------------- POPUP END -------------------*/


	$(document).on("click", '[data-plugin="ajaxGetRequest"]', function(e) {

		//--  data-plugin="ajaxGetRequest"  //--  add this to work the event //--  *Required

		//--  data-conf-message="Are you sure to do the action..?"	//--  Confirm the action by this  //--  Optional

		//--  data-type="DELETE"  //--  To specify the type of the request  //--  Optional  //--  Default "GET"

		//--  data-action="id/action"  //--  TO specify the request action  //--  Optional  //--  Default in "href"

		//--  data-formdata="{'name' : 'value', 'email' : 'value'}"  //--  To specify the data of the request  //--  Optional  //--  Default ""



		e.preventDefault();
		var $this = $(this);
		var ladda = Ladda.create(this);
		var url = $(this).attr('href'); // for anchor tag
		if(typeof(url)  === "undefined" || url == '') {
			url = $(this).data('action'); // if not anchor tag
		}

		var type = $(this).data('type');
		if(typeof(type)  === "undefined") {
			type = "GET";
		}

		var formData1 = $(this).data('formdata');
		if(typeof formData1 == "object") {
			var formData = formData1[0];
		}
		else {
			formData = {'value' : formData1};
		}

		var confdata = $(this).data('confdata');

		//var formData = {'value' : formData1};
		var dataType = 'json';
		if(typeof(formData1)  === "undefined") {
			formData = "";
			dataType = '';
			confdata = '';
		}

		var message = $this.attr('data-conf-message');
		if(typeof(message)  === "undefined" || message=='') { // if no confirmation message needed

			ladda.start();
			$.ajax({
				type: type,
				url: url,
				data 		: formData,
				dataType 	: dataType,
				success: function (data) {
					$this.trigger('ajax-get-request.success', data);
					/*setTimeout(function() {*/
					if((typeof (data.success) === "undefined") || data.success =='') {
					}else{
						toastr.success(data.success, null, {
							containerId: "toast-topFullWidth",
							positionClass: "toast-top-full-width",
							showMethod: "slideDown",
							closeButton: true
						});
					}
					/*}, 2100);*/
				},
				error: function (data) {
//						console.log('Error:', data);
					$this.trigger('ajax-get-request.error', data);

				}
			}).always(function () {
				ladda.stop();
			});
		}
		else { // case need to display alert

			if(typeof(confdata)  === "undefined" || confdata=='') { // case need to display alert for every value

				alertify.confirm(message, function() {
					ladda.start();
					$.ajax({
						type: type,
						url: url,
						data 		: formData,
						dataType 	: dataType,
						success: function (data) {
							$this.trigger('ajax-get-request.success', data);
							/*setTimeout(function() {*/
							if((typeof (data.success) === "undefined") || data.success !='') {
							}else{
								toastr.success(data.success, null, {
									containerId: "toast-topFullWidth",
									positionClass: "toast-top-full-width",
									showMethod: "slideDown",
									closeButton: true
								});
							}
							/*}, 2100);*/
						},
						error: function (data) {
//						console.log('Error:', data);
							$this.trigger('ajax-get-request.error', data);

						}
					}).always(function () {
						ladda.stop();
					});
				}, function () {
					// user clicked "cancel"
				});

			}
			else { //case need to display alert for particular values.

				var arr = $.map(formData, function(el) { return el });
				if(arr[0] == confdata) { // case when clicked a value, which needs to show alert
					alertify.confirm(message, function() {
						ladda.start();
						$.ajax({
							type: type,
							url: url,
							data 		: formData,
							dataType 	: dataType,
							success: function (data) {
								$this.trigger('ajax-get-request.success', data);
								/*setTimeout(function() {*/
								if((typeof (data.success) === "undefined") || data.success !='') {
								}else{
									toastr.success(data.success, null, {
										containerId: "toast-topFullWidth",
										positionClass: "toast-top-full-width",
										showMethod: "slideDown",
										closeButton: true
									});
								}
								/*}, 2100);*/
							},
							error: function (data) {
//						console.log('Error:', data);
								$this.trigger('ajax-get-request.error', data);

							}
						}).always(function () {
							ladda.stop();
						});
					}, function () {
						// user clicked "cancel"
					})
				}
				else { // case when clicked a value, which deosn't need to show alert

					ladda.start();
					$.ajax({
						type: type,
						url: url,
						data 		: formData,
						dataType 	: dataType,
						success: function (data) {
							$this.trigger('ajax-get-request.success', data);
							/*setTimeout(function() {*/
							if((typeof (data.success) === "undefined") || data.success !='') {
							}else{
								toastr.success(data.success, null, {
									containerId: "toast-topFullWidth",
									positionClass: "toast-top-full-width",
									showMethod: "slideDown",
									closeButton: true
								});
							}
							/*}, 2100);*/
						},
						error: function (data) {
//						console.log('Error:', data);
							$this.trigger('ajax-get-request.error', data);

						}
					}).always(function () {
						ladda.stop();
					});

				}

			}



		}
	});
	/*----------------- ajaxForm -------------------*/
	$(document).on("submit", 'form[data-plugin="ajaxForm"]', function (e) {
		e.preventDefault();
		var $this = $(this);
		var url = $this.attr("action");
		var method = $this.attr("method");
		var data = {};
		var processData = true;
		var contentType = "application/x-www-form-urlencoded";
		if ("POST" == method.toUpperCase() && $this.attr('enctype') == "multipart/form-data") {
			data = new FormData($this[0]);
			processData = false;
			contentType = false;
			//contentType = "multipart/form-data";
		} else {
			data = $this.serialize();
		}
		$.ajax({
			type: method,
			url: url,
			data:  data,
			dataType: 'json',
			processData : processData,
			contentType : contentType,
			success : function(data, textStatus, jqXHR) {

					if (data.success) {
						toastr.success(data.success, null, {
							containerId:"toast-topFullWidth",
							positionClass:"toast-top-full-width",
							showMethod:"slideDown",
							closeButton: true
						});
					}


				$this.trigger('af.success', data, textStatus, jqXHR);
			},
			error : function(jqXHR, textStatus, errorThrown) {
				//alert("Error : " + errorThrown);
					var validator = $this.data("validator");
					if (validator && jqXHR.status == 422) {
						var resposeJSON = $.parseJSON(jqXHR.responseText);
						var errors = {};
						$.each(resposeJSON, function (k, v) {
							if(k == 'error'){
								toastr.error(v, null, {
									containerId:"toast-topFullWidth",
									positionClass:"toast-top-full-width",
									showMethod:"slideDown",
									closeButton: true
								});
							}

							errors[k] = v[0];
						});

						if(!$.isEmptyObject(errors)) validator.showErrors(errors);
					} else {
						toastr.error("Some error occurred, Please reload the page and try again", null, {
							containerId:"toast-topFullWidth",
							positionClass:"toast-top-full-width",
							showMethod:"slideDown",
							closeButton: true
						});
					}

				$this.trigger('af.error', jqXHR.responseText, textStatus, jqXHR, errorThrown);
			},
			complete : function(jqXHR, textStatus) {

					var validator = $this.data("validator");
				$this.trigger('af.complete', jqXHR, textStatus);
			}
		});
	});
	/*----------------- ajaxForm END-------------------*/

	/*----------------- ajaxForm SMS-------------------*/
	$(document).on("submit", 'form[data-plugin="ajaxFormSms"]', function (e) {
		e.preventDefault();
		var $this = $(this);
		var url = $this.attr("action");
		var method = $this.attr("method");
		var data = {};
		var processData = true;
		var contentType = "application/x-www-form-urlencoded";
		if ("POST" == method.toUpperCase() && $this.attr('enctype') == "multipart/form-data") {
			data = new FormData($this[0]);
			processData = false;
			contentType = false;
			//contentType = "multipart/form-data";
		} else {
			data = $this.serialize();
		}
		$.ajax({
			type: method,
			url: url,
			data:  data,
			dataType: 'json',
			processData : processData,
			contentType : contentType,
			success : function(data, textStatus, jqXHR) {

				if (data.success) {
					toastr.success(data.success, null, {
						containerId:"toast-topFullWidth",
						positionClass:"toast-top-full-width",
						showMethod:"slideDown",
						closeButton: true
					});
				}


				$this.trigger('af.success', data, textStatus, jqXHR);
			},
			error : function(jqXHR, textStatus, errorThrown) {
				//alert("Error : " + errorThrown);
				var validator = $this.data("validator");
				if (validator && jqXHR.status == 422) {
					var resposeJSON = $.parseJSON(jqXHR.responseText);
					var errors = {};
					$.each(resposeJSON, function (k, v) {
						if(k == 'error'){
							toastr.error(v, null, {
								containerId:"toast-topFullWidth",
								positionClass:"toast-top-full-width",
								showMethod:"slideDown",
								closeButton: true
							});
						}

						errors[k] = v[0];
					});

					if(!$.isEmptyObject(errors)) validator.showErrors(errors);
				} else {
					/*var items = jqXHR.responseText.split('{');*/
					var arrStr = jqXHR.responseText.split(/[{}]/);
					var errorMsgJSON = jQuery.parseJSON('{'+arrStr[1]+'}');
					console.log(errorMsgJSON.error);
					toastr.error(errorMsgJSON.error, null, {
						containerId:"toast-topFullWidth",
						positionClass:"toast-top-full-width",
						showMethod:"slideDown",
						closeButton: true
					});
				}

				$this.trigger('af.error', jqXHR.responseText, textStatus, jqXHR, errorThrown);
			},
			complete : function(jqXHR, textStatus) {

				var validator = $this.data("validator");
				$this.trigger('af.complete', jqXHR, textStatus);
			}
		});
	});
	/*----------------- ajaxFormSms END-------------------*/


	/*ADD MORE - DUPLICATE START*/

	$(document).on("click", 'a[data-toggle="add-more"]', function(e) {
		e.stopPropagation();
		e.preventDefault();
		var $el = $($(this).attr("data-template")).clone();
		$el.removeClass("hidden");
		$el.attr("id", "");

		var count = $(this).data('count');
		count = typeof count == "undefined" ? 0 : count;
		count = count + 1;
		$(this).data('count', count);

		var addvalue = $(this).data("addvalue");
		if(typeof addvalue == "object") {
			$.each(addvalue, function(i, p) {
				$el.find(p.selector).val(count);
			});
		}

		var addindex = $(this).data("addindex");
		if(typeof addindex == "object") {
			$.each(addindex, function(i, p) {
				var have_child = p.have_child;
				if(typeof(have_child)  === "undefined") {
					$el.find(p.selector).attr(p.attr, $el.find(p.selector).attr(p.attr) + '[' + count + ']');
				}else {
					$el.find(p.selector).attr(p.attr, $el.find(p.selector).attr(p.attr)+'['+count+']'+'['+have_child+']' );
				}
			});
		}

		var increment = $(this).data("increment");
		if(typeof increment == "object") {
			$.each(increment, function(i, p) {
				var have_child = p.have_child;
				if(typeof(have_child)  === "undefined") {
					$el.find(p.selector).attr(p.attr, $el.find(p.selector).attr(p.attr)+"-"+count);
				}else {
					$el.find(p.selector).attr(p.attr, $el.find(p.selector).attr(p.attr)+"-"+count+"-"+have_child);
				}
			});
		}

		var plugins = $(this).data("plugins");
		$.each(plugins, function(i, p) {
			/*$el.find(p.selector).attr("data-plugin", p.plugin);*/
			/*$.components.init(p.plugin, $el);*/
			if(p.plugin=='select2') {
				$el.find(p.selector).select2();
			}

		});

		$el.find($(this).attr("data-close")).click(function(){
			// $el.on("click", $(this).attr("data-close"), function(e) {
			$el.fadeOut(function() {
				$(this).remove();
			});
		});

		$el.hide().appendTo($(this).attr("data-container")).fadeIn();
		// $($(this).attr("data-container")).append($el);

	});

	/*ADD MORE - DUPLICATE END*/


	/*CLOSE DATA START*/

	$(document).on('click','.close_data',function(e) {
		e.preventDefault();
		var $this = $(this);
		var item_container = $(this).data('target');
		var replace_container = $(this).data('replace');
		var changevalue = $(this).data('changevalue');
		alertify.confirm('Are you sure to remove this?', function() {
			if(typeof(item_container)  === "undefined") {
			}else {
				$(item_container).remove();
			}
			if(typeof(replace_container)  === "undefined") {
			}else {
				$(replace_container).removeClass('hidden');
			}
			/*CHANGE ANY VALUE*/

			console.log(changevalue);
			if(typeof changevalue == "object") {
				$.each(changevalue, function(i, p) {
					$(p.selector).val(p.value);
				});
			}

			$(replace_container).trigger('close_data.success');
		}, function () {
			// user clicked "cancel"
		});



	});
	/*CLOSE DATA START*/

	//Render Modal START
	$(document).on('click','[data-plugin="render-modal"]',function(e) {
		e.preventDefault();
		var url = $(this).data('target');
		if(typeof(url)  === "undefined") {
			url = '';
		}

		var modal_Id = $(this).data('modal');
		showModal (modal_Id,url);
	});
	//Render Modal END
});


//Render Modal Function START
function showModal (modal_Id,url) {
	if(url=='') {
		$(modal_Id).modal('show');
	}else {
		$.get(url, function (data) {
			var $el = $(data.html).clone();
			$el.find(".select2").select2();
			$el.find('input[type="checkbox"]').iCheck({
				checkboxClass: 'icheckbox_minimal-blue',
				radioClass: 'iradio_minimal-blue'
			});
			$(modal_Id).html($el);
			$(modal_Id).modal('show');
			$(modal_Id).trigger('inside_modal.validation', $el);
		});
	}
}
//Render Modal Function END
