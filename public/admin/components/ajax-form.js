/*!
 * remark (http://getbootstrapadmin.com/remark)
 * Copyright 2016 amazingsurge
 * Licensed under the Themeforest Standard Licenses
 */
$.components.register("ajaxForm", {
    defaults: {
        defaultSuccessAction: true, //To override use data attribute (eg: data-default-success-action="false")
        defaultErrorAction: true, //To override use data attribute (eg: data-default-error-action="false")
        defaultCompleteAction: true //To override use data attribute (eg: data-default-complete-action="false")
    },
    api: function () {
        $(document).on("submit", 'form[data-plugin="ajaxForm"]', function (e) {
            e.preventDefault();

            var send = $(this).triggerHandler("af.send");

            if ( typeof send !== 'undefined' && send == false) {
                return;
            }

            var $this = $(this);
            var url = $this.attr("action");
            var method = $this.attr("method");
            var data = {};
            var processData = true;
            var contentType = "application/x-www-form-urlencoded";

            var defaults = $.components.getDefaults("ajaxForm");
            var options = $.extend(true, {}, defaults, $(this).data());
            console.log(options);
            //var defaultSuccessAction = $this.data('defaultSuccessAction');
            //var defaultErrorAction = $this.data('defaultErrorAction');
            //var defaultCompleteAction = $this.data('defaultCompleteAction');

            if ("POST" == method.toUpperCase() && $this.attr('enctype') == "multipart/form-data") {
                data = new FormData($this[0]);
                processData = false;
                contentType = false;
                //contentType = "multipart/form-data";
            } else {
                data = $this.serializeJSON();
            }

            $.ajax({
                type: method,
                url: url,
                data:  data,
                dataType: 'json',
                processData : processData,
                contentType : contentType,
                success : function(data, textStatus, jqXHR) {
                    if(options.defaultSuccessAction === true) {
                        if (data.success) {
                            window.Site.toast.success(data.success);
                        }
                    }

                    $this.trigger('af.success', data, textStatus, jqXHR);
                },
                error : function(jqXHR, textStatus, errorThrown) {
                    //alert("Error : " + errorThrown);
                    if(options.defaultErrorAction === true) {
                        var validator = $this.data("validator");
                        if (validator && jqXHR.status == 422) {
                            var resposeJSON = $.parseJSON(jqXHR.responseText);
                            var errors = {};
                            $.each(resposeJSON, function (k, v) {
                                if(k == 'error'){
                                    window.Site.toast.error(v);
                                }

                                errors[k] = v[0];
                            });

                            if(!$.isEmptyObject(errors)) validator.showErrors(errors);
                        } else {
                            window.Site.toast.error("Some error occurred, Please reload the page and try again");
                        }
                    }

                    $this.trigger('af.error', jqXHR.responseText, textStatus, jqXHR, errorThrown);
                },
                complete : function(jqXHR, textStatus) {
                    if(options.defaultErrorAction === true) {
                        var validator = $this.data("validator");
                    }
                    $this.trigger('af.complete', jqXHR, textStatus);
                }
            });
        });
    }
});
