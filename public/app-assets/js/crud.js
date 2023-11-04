function Crud(options) {

    var _ = this, _btn, _default_options = {
        processing: true,
        serverSide: true,
        responsive: true,
        stateSave: true,
        deferRender: false,
        type: 'GET',
    };
    _.options = Object.assign(_default_options, options);
    _.currentItem = null;
    _.datatable = null;
    _.progressBar = $('.js-progress-bar');
    _editData = [];

    _.prepareDatatable = function () {
        return {
            deferRender: _.options.deferRender,
            pageLength: _.options.list.datatable.pageLength || 15,
            select: _.options.list.datatable.select || false,
            buttons: _.options.list.datatable.buttons || [],
            lengthMenu: [[10, 25, 50, 100,250,500], [10, 25, 50, 100,250,500]],
            dom: "<'row'<'col-sm-4'l><'col-sm-4'B><'col-sm-4'<'pull-right'f>>>rt<'row'<'col-sm-6'i><'col-sm-6'<'pull-right'p>>>",
            order: _.options.list.datatable.order || [[0, 'desc']],
            processing: _.options.processing,
            serverSide: _.options.serverSide,
            responsive: _.options.responsive,
            stateSave: _.options.stateSave,
            scrollX: _.options.list.datatable.scrollX || false,
            stateLoadParams: function (settings, data) {
                _.stateLoadParams(data);
            },
            ajax: {
                url: _.options.list.url,
                type: _.options.list.type,
                data: _.options.list.data || {},
            },
            columns: _.options.list.datatable.columns,
            columnDefs: _.options.list.datatable.columnDefs
        }
    }

    /**
     * Init datatable Filter by their column index
     */
    _.initDatableFilter = function () {
        $("#datatable thead input, #datatable thead select").on('keyup change', function () {
            _.datatable
                .column($(this).parent().index() + ':visible')
                .search(this.value)
                .draw('page');
        });
    }

    /**
     * Make a button
     *
     * @param  obj data
     * @param  string classes
     * @param  string inner
     * @param  Array dataArr
     *
     * @return string
     */
    _.makeButton = function (data, classes, inner, dataArr) {
        var dataArr = typeof dataArr !== 'undefined' ? dataArr : [],
            dataHtml = 'data-id="' + data.id + '"';

        $.each(dataArr, function (i, val) {
            if (Array.isArray(val)) {
                dataHtml += ' data-' + val[0] + '="' + val[1] + '"';
            } else {
                dataHtml += ' data-' + val + '="' + data[val] + '"';
            }
        });

        return '<button class="btn ' + classes + '"'
            + dataHtml
            + '>'
            + inner
            + '</button> ';
    }

    _.makeDeleteLink = function (data, link, classes, inner) {
        return '<a class="' + classes + '" href="' + link + '?id=' + data.id + '">' + inner + '</a>';
    };

    _.cleanError = function () {
        $('.help-block').remove();
        $('.has-error').removeClass('has-error');
    }

    _.resetProgressBar = function () {
        _.progressBar
            .addClass('hide')
            .find('.progress-bar')
            .css("width", "0%")
            .text("0%");
    }

    _.onClickAddBtn = function () {
        $('body').off('click', '.btn-add').on('click', '.btn-add', function (e) {
            let _this = $(this);
            let dataToggle = _this.data('toggle');
            let _body = $('body');

            if (dataToggle === "modal") {
                _body = $(_this.data('target'));
            }
            _body.find('input[type=text], input[type=number], input[type=email], input[type=hidden], input[type=password], textarea').not('.js-non-editable').val('');
            _body.find('input[type=checkbox], input[type=radio]').prop('checked', false);
            _body.find('input[type=file]').val('');
            _body.find('select').not('.js-non-editable').find('option').prop('selected', false).trigger("change");
            // $('select').find('option:eq(0)').prop('selected', true);
            _body.find('.js-form-image').remove();
            _.cleanError();
        });
    }

    _.onClickEditBtn = function () {
        let _type, _name, _element, _val;

        $('body').off('click', '.btn-edit').on('click', '.btn-edit', function (e) {
            _btn = $(this);
            // Data
            _.editData = _.datatable.row(_btn.closest("tr")).data();

            $('.js-form-image').remove();
            $('input[type=file]').val('');
            // Set Value
            $('#form').find(':input').each(function (i, elem) {

                _element = $(this);
                _type = this.type || this.tagName.toLowerCase();
                _name = _element.attr('name');

                // Skip to next iteration
                if (typeof _name == 'undefined') {
                    return true;
                }

                _val = _.editData[_name.replace(/\[\]/, '')];

                if ((_type == 'checkbox' || _type == 'radio')) {
                    if ($.isArray(_val)) {
                        if ($.inArray(isNaN(_element.val()) ? _element.val() : parseInt(_element.val()), _val) == -1) {

                            _element.prop('checked', false);
                        } else {

                            _element.prop('checked', true);
                        }
                    } else {
                        if (_val == _element.val()) {
                            _element.prop('checked', true);
                        } else {
                            _element.prop('checked', false);
                        }
                    }
                } else if (_type == 'select-multiple') {
                    if (_element.next().is('.select2')) {
                        _element.val(_val).trigger("change");
                    } else {
                        _element.find('option').prop('selected', false);
                        $.each(_val, function (i, el) {
                            _element.find('option[value=' + el.id + ']').prop('selected', true);
                        });
                    }

                } else if (_type == 'file') {
                    if (_val != null && _val != '') {
                        if (typeof _val == 'array') {
                            _val = _val[0];
                        }
                        _element.parent().before('<div class="form-group js-form-image"><img style="width:200px;" src="/image/400x300/' + _val + '" class="img-thumbnail" /></div>');
                    }
                } else {
                    _element.val(_val);
                }
            });
            _.cleanError();
        });
    }
    _.onClickFormBtn = function () {
        $('body').on('click', '.ajax-form', function (e) {
            e.preventDefault();
            _btn = $(this);
            _btn.prepend('<i class="fa fa-spinner fa-spin"></i> ');
            _btn.prop('disabled', true);
            _.progressBar.removeClass('hide');

            var form_data = new FormData(document.getElementById('form'));
            $.ajax({
                url: _.options.form.url,
                type: "POST",
                data: form_data,
                contentType: false,
                cache: false,
                processData: false,
                xhr: function () {

                    // Upload Progress
                    var xhr = $.ajaxSettings.xhr();
                    if (xhr.upload) {
                        xhr.upload.addEventListener('progress', function (event) {
                            var percent = 0;
                            var position = event.loaded || event.position;
                            var total = event.total;

                            if (event.lengthComputable) {
                                percent = Math.ceil(position / total * 100);
                            }

                            // Update progressbar
                            _.progressBar.find('.progress-bar').css("width", +percent + "%").text(percent + "%");
                        }, true);
                    }

                    return xhr;
                },
                mimeType: "multipart/form-data"

            }).done(function (response) {
                let res = JSON.parse(response);

                if (res.status == 'error') {
                    _.cleanError();
                    $.each(res.errors, function (elem, item) {
                        $.each(item, function (i, error) {
                            elem = elem.split('.').map(function (v, i) {
                                return !i ? v : "[" + v + "]";
                            }).join("");
                            console.log(elem);
                            $('[name="' + elem + '"]').after('<span class="help-block">' + error + '</span>').parent().addClass('has-error');
                        });
                    });
                } else {
                    _.datatable.draw('page');
                    $('#formModal').modal('hide');
                }

                // Reset
                _btn.prop('disabled', false);
                _btn.find('i').remove();
                _.resetProgressBar();

            }).fail(function () {

                // Reset
                $('#formModal').modal('hide');
                _btn.prop('disabled', false);
                _btn.find('i').remove();
                _.cleanError();
                _.resetProgressBar();

                alert('Ошибка!');
            });
        });
    }

    _.onClickRemoveBtn = function () {
        $('body').on('click', '[data-target="#removeModal"]', function (e) {
            $('#delete_id').val($(this).data('id'));
        });
    }

    _.onClickAjaxRemoveBtn = function () {
        $('body').on('click', '.ajax-remove', function (e) {
            e.preventDefault();
            _btn = $(this);
            _btn.prepend('<i class="fa fa-spinner fa-spin"></i> ');
            _btn.prop('disabled', true);
            $.ajax({
                type: "POST",
                url: _.options.remove.url,
                data: $('#delete_form').serialize(),
                async: true,
                success: function (response) {
                    if (response.status == 'success') {
                        _.datatable.draw('page');
                    } else if (response.status == 'error') {
                        alert(response.message);
                    } else {
                        alert('Пожалуйста, обновите страницу');
                    }
                    _btn.prop('disabled', false);
                    _btn.find('i').remove();
                    $('#removeModal').modal('hide');
                }
            });
        });
    },

        /**
         * Get query string value by name
         *
         * @param  string name
         * @param  string url
         * @return string
         */
        _.getParameterByName = function (name, url) {
            if (!url) url = window.location.href;
            name = name.replace(/[\[\]]/g, "\\$&");
            var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, " "));
        }

    /**
     * Go to current page
     *
     * @return void
     */
    _.stateLoadParams = function (data) {
        $("#datatable thead input, #datatable thead select").each(function (i, item) {
            $(item).val(data.columns[$(this).parent().index()].search.search);
        });
    },
        _.getDataTable = function () {
            return _.datatable;
        },

        /**
         * Intialize events
         *
         * @return void
         */
        _.init = function () {
            _.onClickAddBtn();
            _.onClickFormBtn();
            _.onClickRemoveBtn();
            _.onClickAjaxRemoveBtn();
            _.onClickEditBtn();
            _.datatable = $("#datatable").DataTable(_.prepareDatatable());
            $.fn.dataTable.ext.errMode = 'none';
            $(_.datatable.table().container()).removeClass('form-inline');
            if (_.options.filter) {
                _.initDatableFilter();
            }

        }

    _.init();

    return _;
}
