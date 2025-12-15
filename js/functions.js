const spinner = '<span class="spinner"></span>',
    l = console.log.bind(this),
    logError = console.error.bind(this);
function isFloat(n) {
    return Number(n) === n && n % 1 !== 0;
}
// is json
function isJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}    // Get Number
function toNumber(str) {
    if (typeof (str) == "number" || typeof (str) == "float") return str;
    if (str) {
        str = str.replace(/[^\d.]/g, "");
        if (str.length > 0) {
            str = parseFloat(str);
        }
    }
    str = parseFloat(str);
    if (isNaN(str)) {
        return false;
    } else {
        return str;
    }
}
// To boolean
function toBoolean(data) {
    if (typeof data === "boolean") return data;
    if (isJson(data)) {
        data = JSON.parse(data);
    }

    return data ? true : false;
}
// Alert Fuction
function sAlert(text, heading, options = {}) {
    let type = ("type" in options) ? options.type : false,
        html = ("html" in options) ? options.html : false;

    if (!type)
        type = heading.toLowerCase();

    let icons = ["success", "error", "warning", "info", "question"];
    if (!icons.includes(type)) type = '';
    let msgOptions = {
        type: type,
        title: heading,
        text: text,
        timer: 1500,
        showConfirmButton: false,
    };
    if (html) {
        delete msgOptions.text;
        msgOptions.html = text;
    }
    Swal.fire(msgOptions);
}
// Handle Alert
function handleAlert(res, showSuccessAlert = true) {
    let success = false;
    if (typeof res === "string") {
        if (!isJson(res)) return false;
        else res = JSON.parse(res);
    }
    if (res.status == "success") success = true;
    let heading = ("heading" in res) ? res.heading : res.status;
    if (!success || showSuccessAlert)
        sAlert(res.data, heading, {
            type: res.status,
            ...res
        });
    return success;
}
// Error
function makeError(error = 'Something went wrong! Please try again') {
    if (typeof error !== "string")
        error = 'Something went wrong! Please try again';
    Swal.fire({
        type: 'error',
        title: 'Oops...',
        text: error,
        timer: 1500,
        showConfirmButton: false,
    });
}
// Disaled button
function disableBtn(btn) {
    btn = $(btn);
    btn.html(spinner);
    btn.addClass('disabled');
    btn.prop('disabled', true);
}
// Enable button
function enableBtn(btn, text) {
    btn = $(btn);
    btn.html(text);
    btn.removeClass('disabled');
    btn.prop('disabled', false);
}
function isObject(obj) {
    if (obj.__proto__.toString) {
        return (obj.toString() == "[object Object]")
    }
    return false;
}
// Loader
function getLoader(text) {
    let loader = '';
    loader += '<div class="loader">';
    loader += '<span class="load"></span>';
    if (text)
        loader += '<span class="text">Loading</span>';
    loader += '</div>';
    return loader;
}
const loader = getLoader(false);
function convertObjToQueryStr(Obj) {
    return Object.keys(Obj).map(function (key) {
        return encodeURIComponent(key) + '=' +
            encodeURIComponent(Obj[key]);
    }).join('&');
}
// Scroll to element
function scrollTo_($elem, duration = 1000, direction = 'top', scrollFrom = 'html, body') {
    if (!$elem.length) return false;
    let scrollDir = "scroll" + capitalize(direction, true),
        offset = $elem.offset(),
        totalOffset = (direction in offset) ? offset[direction] : 0;

    let data = {};
    data[scrollDir] = totalOffset;
    $(scrollFrom).animate(totalOffset, duration);
}
// check if image file
function isImageFile(file) {
    let allowedExt = ['jpg', 'png', 'jpeg', 'gif', 'jfif'];
    let ext = file.name.split('.').pop().toLowerCase();
    if (allowedExt.includes(ext)) {
        return true;
    } else {
        return false;
    }
}
function tcFns() {
    // Set Height to other element height
    $('[data-js-height]').each(function () {
        let $elem = $($(this).dataVal("js-height"));
        if ($elem.length) {
            $(this).height($elem.height());
        }
        $(this).removeAttr("data-js-height");
    });
}
// TC REsponsive image
function tcResImage() {
    $(".tc-res-img[src]").each(function () {
        $(this).css("background-image", `url(${$(this).attr("src")})`);
        $(this).removeAttr("src");
        $(this).removeClass("tc-res-img");
        $(this).addClass("tc-res-img-div");
    });
}
// Refresh Functions
function refreshFns() {
    bsTooltips(); // bootstrap tooltips & Popover
    tcCheckbox(); // Checkbox
    initTcJxElements('.tc-jx-element'); // Jx Elements
    tcFns(); // Custom Functions
    tcResImage(); // TC Responsive Image
    select2();
}
$(document).ready(refreshFns);


function select2() {
    if (!$(".select2-list").length) return false
    $('.select2-list').select2({
        tags: $('.select2-list').data("tags") ? true : false,
        placeholder: '--- Select ---',
        allowClear: true
    });
}

// Custom Select list
$(function () {
    // <div class="custom-select-list-container">
    //         <label class="form-label fw-semibold mb-2">Select a customer or add a new one:</label>
    //         <div class="custom-select-wrapper">
    //             <div class="custom-select" id="custom-select" tabindex="0">
    //                 <span class="custom-select-placeholder">--- Select Customer ---</span>
    //                 <span class="custom-select-arrow"><i class="fas fa-chevron-down"></i></span>
    //             </div>
    //             <ul class="custom-select-dropdown">
    //                 <li class="search-container">
    //                     <input type="text" class="search-input" placeholder="Search customers...">
    //                 </li>

    //                 <!-- Customers are pre-rendered -->
    //                 <li role="option" data-id="1" data-name="John Doe" data-email="john.doe@example.com">
    //                     <div class="customer-avatar">JD</div>
    //                     <div class="customer-info">
    //                         <div class="customer-name">John Doe</div>
    //                         <div class="customer-email">john.doe@example.com</div>
    //                     </div>
    //                 </li>
    //                 <li role="option" data-id="2" data-name="Jane Smith" data-email="jane.smith@example.com">
    //                     <div class="customer-avatar">JS</div>
    //                     <div class="customer-info">
    //                         <div class="customer-name">Jane Smith</div>
    //                         <div class="customer-email">jane.smith@example.com</div>
    //                     </div>
    //                 </li>
    //                 <li role="option" data-id="3" data-name="Robert Johnson" data-email="robert.j@example.com">
    //                     <div class="customer-avatar">RJ</div>
    //                     <div class="customer-info">
    //                         <div class="customer-name">Robert Johnson</div>
    //                         <div class="customer-email">robert.j@example.com</div>
    //                     </div>
    //                 </li>
    //                 <li role="option" data-id="4" data-name="Emily Davis" data-email="emily.davis@example.com">
    //                     <div class="customer-avatar">ED</div>
    //                     <div class="customer-info">
    //                         <div class="customer-name">Emily Davis</div>
    //                         <div class="customer-email">emily.davis@example.com</div>
    //                     </div>
    //                 </li>
    //                 <li role="option" data-id="5" data-name="Michael Wilson" data-email="m.wilson@example.com">
    //                     <div class="customer-avatar">MW</div>
    //                     <div class="customer-info">
    //                         <div class="customer-name">Michael Wilson</div>
    //                         <div class="customer-email">m.wilson@example.com</div>
    //                     </div>
    //                 </li>
    //                 <li role="option" data-id="6" data-name="Sarah Brown" data-email="sarah.brown@example.com">
    //                     <div class="customer-avatar">SB</div>
    //                     <div class="customer-info">
    //                         <div class="customer-name">Sarah Brown</div>
    //                         <div class="customer-email">sarah.brown@example.com</div>
    //                     </div>
    //                 </li>

    //                 <!-- Add new & No results -->
    //                 <li class="add-new-option"><i class="fas fa-plus-circle me-1"></i> Add as new customer</li>
    //                 <li class="no-results">No customers found</li>
    //             </ul>
    //         </div>

    //         <div class="selected-info" id="selected-info">
    //             <div class="selected-label">Currently Selected:</div>
    //             <div class="selected-value" id="selected-value"></div>
    //         </div>
    //     </div>
    let selectedItem = null;

    function filterCustomers($parent, query) {
        let anyVisible = false;
        $parent.find(".custom-select-dropdown").find('li[role="option"]').each(function () {
            const $li = $(this);
            const name = $li.data('name').toLowerCase();
            const email = $li.data('email') || "";
            if (name.includes(query) || email.includes(query)) {
                $li.show();
                anyVisible = true;
            } else {
                $li.hide();
            }
        });

        if (query.trim()) {
            $parent.find(".add-new-option").show();
        } else {
            // $parent.find(".add-new-option").hide();
        }

        $parent.find(".no-results").toggle(!anyVisible && query.trim() ? true : false);
    }

    function selectCustomer($parent, customer) {
        selectedItem = customer;
        $parent.find('.custom-select-placeholder').text(customer.name).addClass('edit-text');
        $parent.find(".selected_id").val(customer.id);
        // --- Run callback if defined ---
        let cb = $parent.data("callback");
        if (cb && typeof window[cb] === "function") {
            window[cb](customer); // pass selected customer object
        }
    }

    // Toggle dropdown
    $(document).on("click", "#custom-select", function (e) {
        e.stopPropagation(); // prevent the click from bubbling up
        let $parent = $(this).parents(".custom-select-list-container");
        $(this).toggleClass('active');
        $parent.find(".custom-select-dropdown").toggle();
        if ($parent.find(".custom-select-dropdown").is(':visible')) $parent.find(".search-input").focus();

        if (!$parent.find(`[role="option"]`).length) {
            $parent.find(".add-new-option").show();
            $parent.find(".no-results").show();
        }
    });

    // Option click
    $(document).on("click", ".custom-select-dropdown li", function (e) {
        e.stopPropagation(); // prevent the click from closing immediately
        let $li = $(this);
        let $parent = $(this).parents(".custom-select-list-container");

        if ($li.hasClass('add-new-option')) {
            let $popup = $li.data("popup");
            $($popup).modal("show");
            $parent.find(".custom-select-dropdown").hide();
            $parent.find("#custom-select").removeClass('active');
            return;
        }
        if ($li.attr('role') === 'option') {
            selectCustomer($parent, {
                name: $li.data('name'),
                email: $li.data('email') || "",
                id: $li.data('id')
            });
            $parent.find(".custom-select-dropdown").hide();
            $parent.find("#custom-select").removeClass('active');
        }
    });

    // Search input
    $(document).on("input", ".custom-select-list-container .search-input", function () {
        let query = $(this).val().toLowerCase();
        let $parent = $(this).parents(".custom-select-list-container");
        filterCustomers($parent, query);
    });

    // Click outside to close
    $(document).on("click", function () {
        $(".custom-select-dropdown").hide();
        $(".custom-select").removeClass("active");
    });
});

// date picker
$(document).ready(function () {
    $(function () {
        $(".bs-datepicker").each(function () {
            let phpDate = $(this).data("date"); // PHP date in mm-dd-yyyy format
            let format = $(this).data("date-format"); // Read format if needed
            console.log(phpDate);

            $(this).datepicker({
                autoclose: true,
                todayHighlight: true,
                format: format   // Ensure same format
            }).datepicker('update', phpDate); // <-- pass date in same format
        });

    });
});