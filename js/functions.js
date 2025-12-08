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
    $('.select2-list').select2({
        tags: $('.select2-list').data("tags") ? true : false,
        placeholder: '--- Select ---',
        allowClear: true
    });
}