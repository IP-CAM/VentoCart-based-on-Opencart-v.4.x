function getURLVar(key) {
    var value = [];

    var query = String(document.location).split('?');

    if (query[1]) {
        var part = query[1].split('&');

        for (i = 0; i < part.length; i++) {
            var data = part[i].split('=');

            if (data[0] && data[1]) {
                value[data[0]] = data[1];
            }
        }

        if (value[key]) {
            return value[key];
        } else {
            return '';
        }
    }
}
$(document).ready(function () {
    $('.seoinput').seoFriendlyInput();
    //file manager pointer


});
function initTinyMCE() {
    tinymce.init({
        plugins: "code image media lists link preview autolink table visualblocks emoticons",
        promotion: false,
        fontsize_formats: "8pt 10pt 12pt 14pt 18pt 24pt 36pt",
        protect: [/<script>[\s\S]*?<\/script>/g],
        extended_valid_elements: 'script[language|type|src]',
        selector: 'textarea[data-oc-toggle="ckeditor"]',
        paste_data_images: true,
        toolbar: 'paste code lists undo redo  removeformat  formatselect | blockquote  | bold italic underline strikethrough | hr | forecolor | backcolor  | alignleft aligncenter alignright alignjustify | uploadImage  media | bullist numlist | table   | link | view | emoticons',

        paste_preprocess: function (plugin, args) {

            let tempDiv = document.createElement('div');
            tempDiv.innerHTML = args.content;

            // Get all img elements within tempDiv
            let imgElements = tempDiv.querySelectorAll('img');

            // Process each img element

            imgElements.forEach(function (imgElementA) {
                let srcValue = imgElementA.getAttribute('src');

                if (!srcValue) { return; }

                let attributes = imgElementA.attributes;
                for (var i = attributes.length - 1; i >= 0; i--) {
                    if (attributes[i].name != 'src') {
                        imgElementA.removeAttribute(attributes[i].name);
                    }
                }
                let imgid = "Upl-" + Math.floor(Math.random() * 100000000100069).toString();
                imgElementA.setAttribute('id', imgid);
                args.content = tempDiv.innerHTML;
                if (srcValue.startsWith('blob:')) {

                    fetch(srcValue)
                        .then(response => response.blob())
                        .then(blob => {
                            // console.log(blob);
                            let extension = blob.name.split('.').pop();

                            let filename = "pasted_" + Math.floor(Math.random() * 100000000100069).toString() + '.' + extension;
                            // Create form data
                            var formData = new FormData();
                            formData.append('file[]', blob, filename);

                            $.ajax({
                                url: 'index.php?route=common/filemanager.upload&user_token=' + getURLVar('user_token') + '&directory=' + getUploadDirectory(),
                                type: 'post',
                                data: formData,
                                contentType: false,
                                processData: false, // important
                                dataType: 'json',
                                success: function (response) {
                                    tinymce.activeEditor.dom.setAttrib(imgid, 'src', '/image/catalog/' + getUploadDirectory() + filename);
                                    tinymce.activeEditor.dom.setAttrib(imgid, 'width', '40%');
                                },
                                error: function (xhr, status, error) {
                                    // Handle error
                                    console.error('Error uploading image:', error);
                                }
                            });
                        })
                        .catch(error => {
                            console.error('Error fetching blob:', error);
                        });

                    return;
                }


                uploadPastedImage(srcValue, function (response) {

                    tinymce.activeEditor.dom.setAttrib(imgid, 'src', response);
                    tinymce.activeEditor.dom.setAttrib(imgid, 'width', '40%');
                });
            });

        },

        setup: function (editor) {
            window.editor = editor;
            editor.on('paste', function (event) {

                var clipboardData = event.clipboardData || window.clipboardData;
                var items = clipboardData.items;
                for (var i = 0; i < items.length; i++) {

                    if (items[i].type.indexOf('image') !== -1) {
                        event.preventDefault();
                        var blob = items[i].getAsFile();
                        var reader = new FileReader();
                        reader.onload = (function (index) {
                            return function (event) {
                                // Do something with the image data
                                var imageData = event.target.result;
                                // Extract file extension from base64
                                var fileType = imageData.substring("data:image/".length, imageData.indexOf(";base64"));
                                // Create a new image element
                                var img = new Image();
                                img.src = imageData;

                                let filename = "pasted_" + Math.floor(Math.random() * 100000000100069).toString() + '.' + fileType;
                                // Create form data
                                var formData = new FormData();
                                formData.append('file[]', blob, filename);

                                $.ajax({
                                    url: 'index.php?route=common/filemanager.upload&user_token=' + getURLVar('user_token') + '&directory=' + getUploadDirectory(),
                                    type: 'post',
                                    data: formData,
                                    contentType: false,
                                    processData: false, // important
                                    dataType: 'json',
                                    success: function (response) {
                                        img.setAttribute('width', '40%');
                                        img.src = '/image/catalog/' + getUploadDirectory() + filename;
                                        editor.selection.setContent(img.outerHTML);
                                    },
                                    error: function (xhr, status, error) {
                                        // Handle error
                                        console.error('Error uploading image:', error);
                                    }
                                });
                            };
                        })(i);
                        reader.readAsDataURL(blob);
                    }
                }
            });
            editor.ui.registry.addButton('uploadImage', {
                icon: 'image', // Font Awesome icon for image
                tooltip: 'Insert Image', // Tooltip for the button
                onAction: function () {
                    window.activeEditor = true;
                    $('#modal-image').remove();
                    $.ajax({
                        url: 'index.php?route=common/filemanager&user_token=' + getURLVar('user_token'),
                        dataType: 'html',
                        success: function (html) {
                            $('body').append(html);

                            $('#modal-image').modal('show');
                        }
                    });
                }
            });
        }
    });

}
function getUploadDirectory() {
    let directory = '/';
    let urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('route')) {
        if (urlParams.get('route') === 'catalog/category.form') {
            directory = 'categories/';
        } else if (urlParams.get('route') === 'design/banner.form') {
            directory = 'banners/';
        } else if (urlParams.get('route') === 'catalog/manufacturer.form') {
            directory = 'manufacturers/';
        } else if (urlParams.get('route') === 'cms/topic.form' || urlParams.get('route') === 'cms/article.form') {
            directory = 'blog/';
        } else if (urlParams.get('route') === 'catalog/product.form') {
            directory = 'products/';
            if ($("#input-product-id")) {
                if ($("#input-product-id") && $("#input-product-id").val() != 0) {
                    directory += $("#input-product-id").val() + '/';
                }
            }
        }
    }
    return directory;

}
function uploadPastedImage(imgUrl, callback) {

    $.ajax({
        url: 'index.php?route=common/filemanager.uploadFromURL&user_token=' + getURLVar('user_token'),
        data: { imgUrl: imgUrl, directory: getUploadDirectory() },
        type: 'post',
        dataType: 'json',
        success: function (response) {
            // Invoke the callback function with the response
            callback('/image/catalog/' + getUploadDirectory() + response.filename);
        },
        error: function (xhr, status, error) {
            // Handle error
            console.error('Error uploading image:', error);
        }
    });
}


//Seo conversion jquery plugin
(function ($) {
    $.fn.seoFriendlyInput = function () {
        return this.each(function () {
            $(this).on('input', function () {
                var inputValue = $(this).val();

                // Convert to lowercase
                var seoFriendlyText = inputValue.toLowerCase();

                // Replace spaces with dashes
                seoFriendlyText = seoFriendlyText.replace(/\s+/g, '-');

                // Remove symbols and replace spaces with dashes
                seoFriendlyText = seoFriendlyText.replace(/[^\p{L}\d-]+/gu, '');

                // Update the input value
                $(this).val(seoFriendlyText);
            });
        });
    };
})(jQuery);

// On August 17 2021, Internet Explorer 11 (IE11) will no longer be supported by Microsoft's 365 applications and services.
function isIE() {
    if (!!window.ActiveXObject || "ActiveXObject" in window) return true;
}

// Header
$(document).ready(function () {
    // Header
    $('#header-notification [data-bs-toggle=\'modal\']').on('click', function (e) {
        e.preventDefault();

        var element = this;

        $('#modal-notification').remove();

        $.ajax({
            url: $(element).attr('href'),
            dataType: 'html',
            success: function (html) {
                $('body').append(html);

                $('#modal-notification').modal('show');
            }
        });
    });
});

// Menu
$(document).ready(function () {
    $('#button-menu').on('click', function (e) {
        e.preventDefault();

        $('#column-left').toggleClass('active');
    });

    // Set last page opened on the menu
    $('#menu a[href]').on('click', function () {
        sessionStorage.setItem('menu', $(this).attr('href'));
    });

    if (!sessionStorage.getItem('menu')) {
        $('#menu #menu-dashboard').addClass('active');
    } else {
        // Sets active and open to selected page in the left column menu.
        $('#menu a[href=\'' + sessionStorage.getItem('menu') + '\']').parent().addClass('active');
    }

    $('#menu a[href=\'' + sessionStorage.getItem('menu') + '\']').parents('li').children('a').removeClass('collapsed');

    $('#menu a[href=\'' + sessionStorage.getItem('menu') + '\']').parents('ul').addClass('show');

    $('#menu a[href=\'' + sessionStorage.getItem('menu') + '\']').parents('li').addClass('active');
});

$(document).ready(function () {
    // Tooltip
    var oc_tooltip = function () {
        // Get tooltip instance
        tooltip = bootstrap.Tooltip.getOrCreateInstance(this);

        if (!tooltip) {
            // Apply to current element
            tooltip.show();
        }
    }

    $(document).on('mouseenter', '[data-bs-toggle=\'tooltip\']', oc_tooltip);

    $(document).on('click', 'button', function () {
        $('.tooltip').remove();
    });

    // Date
    var oc_datetimepicker = function () {
        $(this).daterangepicker({
            singleDatePicker: true,
            autoApply: true,
            autoUpdateInput: false,
            locale: {
                format: 'YYYY-MM-DD'
            }
        }, function (start, end) {
            $(this.element).val(start.format('YYYY-MM-DD'));
        });
    }

    $(document).on('focus', '.date', oc_datetimepicker);

    // Time
    var oc_datetimepicker = function () {
        $(this).daterangepicker({
            singleDatePicker: true,
            datePicker: false,
            autoApply: true,
            autoUpdateInput: false,
            timePicker: true,
            timePicker24Hour: true,
            locale: {
                format: 'HH:mm'
            }
        }, function (start, end) {
            $(this.element).val(start.format('HH:mm'));
        }).on('show.daterangepicker', function (ev, picker) {
            picker.container.find('.calendar-table').hide();
        });
    }

    $(document).on('focus', '.time', oc_datetimepicker);

    // Date Time
    var oc_datetimepicker = function () {
        $(this).daterangepicker({
            singleDatePicker: true,
            autoApply: true,
            autoUpdateInput: false,
            timePicker: true,
            timePicker24Hour: true,
            locale: {
                format: 'YYYY-MM-DD HH:mm'
            }
        }, function (start, end) {
            $(this.element).val(start.format('YYYY-MM-DD HH:mm'));
        });
    }

    $(document).on('focus', '.datetime', oc_datetimepicker);

    // Alert Fade
    var oc_alert = function () {
        window.setTimeout(function () {
            $('.alert-dismissible').fadeTo(1000, 0, function () {
                $(this).remove();
            });
        }, 6000);
    }

    $(document).on('click', 'button', oc_alert);
});


// Forms
$(document).on('submit', 'form', function (e) {
    var element = this;

    if (e.originalEvent !== undefined && e.originalEvent.submitter !== undefined) {
        var button = e.originalEvent.submitter;
    } else {
        var button = '';
    }

    var status = false;

    var ajax = $(element).attr('data-oc-toggle');

    if (ajax == 'ajax') {
        status = true;
    }

    var ajax = $(button).attr('data-oc-toggle');

    if (ajax == 'ajax') {
        status = true;
    }

    if (status) {
        e.preventDefault();

        // Form attributes
        var form = e.target;

        var action = $(form).attr('action');

        var method = $(form).attr('method');

        if (method === undefined) {
            method = 'post';
        }

        var enctype = $(form).attr('enctype');

        if (enctype === undefined) {
            enctype = 'application/x-www-form-urlencoded';
        }

        // Form button overrides
        var formaction = $(button).attr('formaction');

        if (formaction !== undefined) {
            action = formaction;
        }

        var formmethod = $(button).attr('formmethod');

        if (formmethod !== undefined) {
            method = formmethod;
        }

        var formenctype = $(button).attr('formenctype');

        if (formenctype !== undefined) {
            enctype = formenctype;
        }

        if (button) {
            var formaction = $(button).attr('data-type');
        }






        $.ajax({
            url: action.replaceAll('&amp;', '&'),
            type: method,
            data: $(form).serialize(),
            dataType: 'json',
            contentType: enctype,
            beforeSend: function () {
                $(button).button('loading');
            },
            complete: function () {
                $(button).button('reset');
            },
            success: function (json, textStatus) {


                $('.alert-dismissible').remove();
                $(element).find('.is-invalid').removeClass('is-invalid');
                $(element).find('.invalid-feedback').removeClass('d-block');

                if (json['redirect']) {
                    location = json['redirect'];
                }

                if (typeof json['error'] == 'string') {
                    $('#alert').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa-solid fa-circle-exclamation"></i> ' + json['error'] + ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
                }

                if (typeof json['error'] == 'object') {
                    if (json['error']['warning']) {
                        $('#alert').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa-solid fa-circle-exclamation"></i> ' + json['error']['warning'] + ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
                    }

                    for (key in json['error']) {
                        $('#input-' + key.replaceAll('_', '-')).addClass('is-invalid').find('.form-control, .form-select, .form-check-input, .form-check-label').addClass('is-invalid');
                        $('#error-' + key.replaceAll('_', '-')).html(json['error'][key]).addClass('d-block');
                    }
                }

                if (json['success']) {
                    $('#alert').prepend('<div class="alert alert-success alert-dismissible"><i class="fa-solid fa-circle-check"></i> ' + json['success'] + ' <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');

                    // Refresh
                    var url = $(form).attr('data-oc-load');
                    var target = $(form).attr('data-oc-target');

                    if (url !== undefined && target !== undefined) {
                        $(target).load(url);
                    }
                }

                // Replace any form values that correspond to form names.
                for (key in json) {
                    $(element).find('[name=\'' + key + '\']').val(json[key]);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }
});

// Upload
$(document).on('click', '[data-oc-toggle=\'upload\']', function () {
    var element = this;

    if (!$(element).prop('disabled')) {
        $('#form-upload').remove();

        $('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" value=""/></form>');

        $('#form-upload input[name=\'file\']').trigger('click');

        $('#form-upload input[name=\'file\']').on('change', function (e) {
            if ((this.files[0].size / 1024) > $(element).attr('data-oc-size-max')) {
                alert($(element).attr('data-oc-size-error'));

                $(this).val('');
            }
        });

        if (typeof timer != 'undefined') {
            clearInterval(timer);
        }

        var timer = setInterval(function () {
            if ($('#form-upload input[name=\'file\']').val() != '') {
                clearInterval(timer);

                $.ajax({
                    url: $(element).attr('data-oc-url'),
                    type: 'post',
                    data: new FormData($('#form-upload')[0]),
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function () {
                        $(element).button('loading');
                    },
                    complete: function () {
                        $(element).button('reset');
                    },
                    success: function (json) {

                        if (json['error']) {
                            alert(json['error']);
                        }

                        if (json['success']) {
                            alert(json['success']);
                        }

                        if (json['code']) {
                            $($(element).attr('data-oc-target')).val(json['code']);

                            $(element).parent().find('[data-oc-toggle=\'download\'], [data-oc-toggle=\'clear\']').prop('disabled', false);
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                });
            }
        }, 500);
    }
});

$(document).on('click', '[data-oc-toggle=\'download\']', function (e) {
    var element = this;

    var value = $($(element).attr('data-oc-target')).val();

    if (value != '') {
        location = 'index.php?route=tool/upload.download&user_token=' + getURLVar('user_token') + '&code=' + value;
    }
});

$(document).on('click', '[data-oc-toggle=\'clear\']', function () {
    var element = this;

    // Images
    var thumb = $(this).attr('data-oc-thumb');

    if (thumb !== undefined) {
        $(thumb).attr('src', $(thumb).attr('data-oc-placeholder'));
    }

    // Custom fields
    var download = $(element).parent().find('[data-oc-toggle=\'download\']');

    if (download.length) {
        $(element).parent().find('[data-oc-toggle=\'download\'], [data-oc-toggle=\'clear\']').prop('disabled', true);
    }

    $($(this).attr('data-oc-target')).val('');
});

// Image Manager
$(document).on('click', '[data-oc-toggle=\'image\']', function (e) {
    var element = this;

    $('#modal-image').remove();

    $.ajax({
        url: 'index.php?route=common/filemanager&user_token=' + getURLVar('user_token') + '&target=' + encodeURIComponent($(element).attr('data-oc-target')) + '&thumb=' + encodeURIComponent($(element).attr('data-oc-thumb')),
        dataType: 'html',
        beforeSend: function () {
            $(element).button('loading');
        },
        complete: function () {
            $(element).button('reset');
        },
        success: function (html) {
            $('body').append(html);

            var element = document.querySelector('#modal-image');

            var modal = new bootstrap.Modal(element);

            modal.show();
        }
    });
});

// Chain ajax calls.
class Chain {
    constructor() {
        this.start = false;
        this.data = [];
    }

    attach(call) {
        this.data.push(call);

        if (!this.start) {
            this.execute();
        }
    }

    execute() {
        if (this.data.length) {
            this.start = true;

            var call = this.data.shift();

            var jqxhr = call();

            jqxhr.done(function () {
                chain.execute();
            });
        } else {
            this.start = false;
        }
    }
}

var chain = new Chain();

// Autocomplete
+function ($) {
    $.fn.autocomplete = function (option) {
        return this.each(function () {
            var element = this;
            var $dropdown = $('#' + $(element).attr('data-oc-target'));

            this.timer = null;
            this.items = [];

            $.extend(this, option);

            // Focus in
            $(element).on('focusin', function () {
                element.request();
            });

            // Focus out
            $(element).on('focusout', function (e) {
                if (!e.relatedTarget || !$(e.relatedTarget).hasClass('dropdown-item')) {
                    $dropdown.removeClass('show');
                }
            });

            // Input
            $(element).on('input', function (e) {
                element.request();
            });

            // Click
            $dropdown.on('mousedown', 'a', function (e) {
                e.preventDefault();

                var value = $(this).attr('href');

                if (element.items[value] !== undefined) {
                    element.select(element.items[value]);

                    $dropdown.removeClass('show');
                }
            });

            // Request
            this.request = function () {
                clearTimeout(this.timer);

                $('#autocomplete-loading').remove();
                $dropdown.find('li').remove();

                $dropdown.prepend('<li id="autocomplete-loading"><span class="dropdown-item text-center disabled"><i class="fa-solid fa-circle-notch fa-spin"></i></span></li>');
                $dropdown.addClass('show');

                this.timer = setTimeout(function (object) {
                    object.source($(object).val(), $.proxy(object.response, object));
                }, 50, this);
            }

            // Response
            this.response = function (json) {
                var html = '';
                var category = {};
                var name;
                var i = 0, j = 0;

                if (json.length) {
                    for (i = 0; i < json.length; i++) {
                        // update element items
                        this.items[json[i]['value']] = json[i];

                        if (!json[i]['category']) {
                            // ungrouped items
                            html += '<li><a href="' + json[i]['value'] + '" class="dropdown-item">' + json[i]['label'] + '</a></li>';
                        } else {
                            // grouped items
                            name = json[i]['category'];

                            if (!category[name]) {
                                category[name] = [];
                            }

                            category[name].push(json[i]);
                        }
                    }

                    for (name in category) {
                        html += '<li><h6 class="dropdown-header">' + name + '</h6></li>';

                        for (j = 0; j < category[name].length; j++) {
                            html += '<li><a href="' + category[name][j]['value'] + '" class="dropdown-item">' + category[name][j]['label'] + '</a></li>';
                        }
                    }

                    $dropdown.html(html);
                } else {
                    $dropdown.removeClass('show');
                }
            }
        });
    }
}(jQuery);

// Button
$(document).ready(function () {
    +function ($) {
        $.fn.button = function (state) {
            return this.each(function () {
                var element = this;

                if (state == 'loading') {
                    this.html = $(element).html();
                    this.state = $(element).prop('disabled');

                    $(element).prop('disabled', true).width($(element).width()).html('<i class="fa-solid fa-circle-notch fa-spin text-light"></i>');
                }

                if (state == 'reset') {
                    $(element).prop('disabled', this.state).width('').html(this.html);
                }
            });
        }
    }(jQuery);
});