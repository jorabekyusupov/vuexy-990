<script >
    $('input[type="checkbox"]').on('change', function (e) {
        let checked = $(this).prop('checked');
        $(this).val(checked ? 1 : 0);
    });
    let formatNumber = function (x) {
        if (typeof x === 'number') {
            x = x.toString()
        }
        const dollarUSLocale = Intl.NumberFormat('en-US')
        const price = dollarUSLocale.format(x)
        return price.toLocaleString().replace(/,/g, '  ')
    }
    let processing = fn => {
        $.blockUI({
            message: '<div class="spinner-border text-primary" role="status"></div>',

            css: {
                backgroundColor: 'transparent',
                border: '0',
                zIndex: '10000 !important',

            },
            overlayCSS: {
                backgroundColor: 'rgba(255, 255, 255, 0.48)',
                opacity: 0.8,
            },

        });
    }
    let processed = fn => {
        $.unblockUI();
    }
    let getErrorMessages = function (data) {
        let errors = Object.values(data.responseJSON.errors);
        if (errors.length > 0) {
            $.each(errors, function (index, value) {
                $.each(value, function (index, value) {
                    errorMessage(value)
                })
            })
        }
    }
    let successMessage = function (message) {
        toastr['success'](message, '{{__('messages.success')}}!', {
            positionClass: 'toast-bottom-right',
            closeButton: true,
            tapToDismiss: true,
            rtl: false
        });
    }
    let errorMessage = function (message) {
        toastr['error'](message, '{{__('messages.error')}}!', {
            positionClass: 'toast-top-right',
            closeButton: true,
            tapToDismiss: true,
            rtl: false
        });

    }
    let warningMessage = function (message) {
        toastr['warning'](message, '{{__('messages.warning')}}!', {
            positionClass: 'toast-top-right',
            closeButton: true,
            tapToDismiss: false,
            rtl: false
        });
    }
    let alertSuccess = function (title, text) {
        Swal.fire({
            title: title,
            text: text,
            icon: 'success',
            customClass: {
                confirmButton: 'btn btn-primary'
            },
            zIndex: 10000,
            buttonsStyling: false
        });
    }
    let alertError = function (title, text) {
        Swal.fire({
            title: title,
            text: text,
            icon: 'error',
            customClass: {
                confirmButton: 'btn btn-primary'
            },
            buttonsStyling: false
        });
    }
    let checkConfirmModal = function (title, text, confirmButtonText, cancelButtonText, callback, successModalShow = false, titleSuccess = '{{__('messages.success')}}', textSuccess = '{{__('messages.success')}}') {
        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: confirmButtonText,
            cancelButtonText: cancelButtonText,
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-danger ms-1'
            },
            buttonsStyling: false
        }).then(function (result) {
            if (result.value) {
               (new Promise(callback())).then(function (response) {
                    if (response.success) {
                        if (successModalShow) {
                            Swal.fire({
                                icon: 'success',
                                title: titleSuccess,
                                text: textSuccess,
                                customClass: {
                                    confirmButton: 'btn btn-success'
                                }
                            });
                        }
                    }
                }).catch(function (error) {
                    getErrorMessages(error);
                });
            }
        });
    }
    let getFilterValues = function (filters, type = 'params') {
        let data = null;
        if (type === 'params') {
            data = '?';
            for (let key in filters) {
                filters[key] = filters[key].val();
                if (filters[key] !== null && filters[key] !== '' && filters[key] !== undefined && filters[key].length > 0) {
                    data += key + '=' + filters[key] + '&';
                }
            }
        } else if (type === 'filter') {
            data = {};
            for (let key in filters) {
                if (key !== 'start_date' && key !== 'end_date') {
                    let val = filters[key].val();
                    let selectData = filters[key].select2('data');
                    let selectText = {};
                    selectData.map(function (item) {
                        if (!item.disabled) {
                            return selectText[item.id] = item.text;
                        }
                    })
                    if (val !== null && val !== '' && val !== undefined && val.length > 0) {
                        data[key] = selectText;
                    }
                }

            }
        } else if (type === 'data') {
            data = {};
            for (let key in filters) {
                filters[key] = filters[key].val();
                if (filters[key] !== null && filters[key] !== '' && filters[key] !== undefined && filters[key].length > 0) {
                    data[key] = filters[key];
                }
            }

        }
        return data;
    }
    let getListFilters = function (type) {
        (new Promise(function (resolve, reject) {
            return true //ajax
        }))
            .then(function (data) {
                $('#filterListBody').html(data.content);
            });
    }
    let storeFilter = function (data) {
         //ajax/
        {{--$.ajax({--}}
        {{--    url: '{{route('filters.store')}}',--}}
        {{--    type: 'POST',--}}
        {{--    headers: {--}}
        {{--        'Accept': 'application/json',--}}
        {{--    },--}}
        {{--    data: data,--}}
        {{--    success: function (response) {--}}
        {{--        if (response.success) {--}}
        {{--            $('#filterSaveModal').modal('hide');--}}
        {{--            successMessage(response.message);--}}
        {{--            activeFilter($('#filter_type_save').val())--}}
        {{--            $('#filterSaveForm').trigger('reset');--}}
        {{--        }--}}
        {{--    },--}}
        {{--    error: function (data) {--}}
        {{--        getErrorMessages(data)--}}
        {{--        errorMessage(data.responseJSON.message);--}}
        {{--    }--}}
        {{--})--}}

    }
    let putFilter = function (id, data) {
        {{--return $.ajax({--}}
        {{--    url: '{{route('filters.update_default')}}/' + id,--}}
        {{--    type: 'PUT',--}}
        {{--    headers: {--}}
        {{--        'Accept': 'application/json',--}}
        {{--    },--}}
        {{--    data: data,--}}
        {{--    success: function (response) {--}}
        {{--        if (response.success) {--}}
        {{--            activeFilter(filterType);--}}
        {{--        }--}}
        {{--    },--}}
        {{--    error: function (data) {--}}
        {{--        getErrorMessages(data)--}}
        {{--        errorMessage(data.responseJSON.message);--}}
        {{--    }--}}
        {{--})--}}
    }
    let submitFilter = function () {
        let filters = getFilters();
        let filter = getFilterValues(filters, 'params');
        filter = filter.slice(0, -1);
        crud.datatable.ajax.url(datatableUrl + filter).load();

    }
    let activeFilter = function (type) {
        (new Promise(function (resolve, reject) {
            {{--$.ajax({--}}
            {{--    url: '{{ route('filters.active') }}/' + type,--}}
            {{--    type: 'GET',--}}
            {{--    headers: {--}}
            {{--        'Accept': 'application/json',--}}
            {{--    },--}}
            {{--    success: function (response) {--}}
            {{--        if (response.success) {--}}
            {{--            resolve(response.data);--}}
            {{--        } else {--}}
            {{--            reject(response.error); // You can customize the error handling here--}}
            {{--        }--}}
            {{--    },--}}
            {{--    error: function (data) {--}}
            {{--        reject(data.responseJSON.message); // Handle error messages--}}
            {{--    }--}}
            {{--});--}}
        })).then(function (data) {
                let selectOption = new Option(data.name, data.id, true, true);
                $('#filter-select').append(selectOption);
                if (Object.keys(data.data.filters).length > 0) {
                    let filters = data.data.filters;
                    for (let key in filters) {
                        let filter = filters[key];
                        let elements = getFilters();
                        elements[key].val(null).trigger('change');
                        if (typeof filter === 'object') {
                            Object.keys(filter).map(function (item) {
                                let option = new Option(filter[item], item, true, true);
                                elements[key].append(option).trigger('change');
                            })
                        }

                    }
                    submitFilter();
                }
            }
        )
            .catch(function (error) {
                filter = error;
            });

    }
</script>