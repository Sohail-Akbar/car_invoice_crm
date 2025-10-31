tc._file = {
    files: [],
    fileId: 0,
    fileInputId: 0,
    get: function (type, data) {
        switch (type) {
            case "singleFile":
                return this.files.find(file => file.id === data);
                break
            case "files":
                data = data.toString();
                let filesIds = data.split(","),
                    files = [];
                filesIds.forEach(id => {
                    this.files.forEach(file => {
                        if (file.id == id) {
                            files.push(file);
                        }
                    })
                });
                return files;
                break;
            case "inputFiles":
                let inputId = data,
                    inputFiles = [];
                this.files.forEach(file => {
                    if (file.inputId == inputId)
                        inputFiles.push(file);
                });
                return inputFiles;
                break;
            case "fileIcon":
                let filename = data,
                    ext = filename.split(".").pop().toLowerCase(),
                    icon = ext in this.icons ? this.icons[ext] : this.icons['default'],
                    imageExt = ['jpg', 'jpeg', 'png', 'gif'];
                index = imageExt.indexOf(ext);
                if (index > -1) {
                    if (imageExt[index]) {
                        icon = "image";
                    }
                }

                return icon;
                break;
            default:
                break;
        }
    },
    icons: {
        'pdf': '<i class="far fa-file-pdf"></i>',
        'docx': '<i class="far fa-file-alt"></i>',
        'zip': '<i class="far fa-file-archive"></i>',
        'mp4': '<i class="far fa-file-video"></i>',
        'default': '<i class="far fa-file"></i>',
    },
    set: function (type, data = {}) {
        if (type === "filesData") {
            let $input = $(data),
                $parent = $input.parents(".tc-file-input-group"),
                txt = "Drop/Paste Files here to upload",
                files = this.get("inputFiles", $input.attr("tc-input-id")),
                filesLength = files.length;

            if (filesLength) {
                txt = `${filesLength} file${filesLength > 1 ? "s" : ""} selected`;
                if (!$input.hasAttr("multiple"))
                    txt = files[0].name;
            }
            $parent.find(".tc-files-name").val(txt);
            // Set Files names
            let $filesDropdown = $parent.find(".tc-files-data");
            if (!$filesDropdown.length) return true;
            // $filesDropdown.html(``);
            files.forEach(file => {
                let isFileExists = false;
                if ($filesDropdown.length) {
                    $filesDropdown.find("p").remove()
                }
                $filesDropdown.find(`.tc-file[data-id="${file.id}"]`).each(function () {
                    isFileExists = true;
                })
                if (isFileExists) return true;

                let filename = file.name,
                    icon = this.get("fileIcon", file.name),
                    size = bytesToSize(file.size),
                    maxFileName = $parent.data("maxFileName"),
                    fileName = file.name.length > maxFileName ? file.name.substring(0, maxFileName) + "..." : file.name;

                if (icon == "image") {
                    let imageSrc = URL.createObjectURL(file);
                    icon = `<div class="icon-image" style="background-image: url('${imageSrc}')"></div>`;
                }
                $filesDropdown.append(`
                <div class="tc-file" data-id="${file.id}">
                <div class="tc-file-content">
                <span class="tc-file-name">${fileName}</span>
                    <div class="icon mt-3">
                        ${icon}
                    </div>
                    <i class="fas fa-times tc-file-remove-btn"></i>
                    <a class="download-btn btn">Download</a>
                    </div>
                </div>`);
            });
            if (!filesLength) $filesDropdown.html(`<p class="p-2">No File Selected</p>`);
        } else if (type === "files") {
            // Some Variables
            let { element, files } = data,
                $parent = $(element).parents(".tc-file-input-group"),
                $fileInput = $parent.find(".tc-file-input"),
                fileInputId = $fileInput.attr("tc-input-id");
            if (!$parent.length) return false;
            // Delete old files
            if (!$fileInput.hasAttr("multiple"))
                this.delete("inputFiles", fileInputId);
            // Append new files
            for (let i = 0; i < files.length; i++) {
                let file = files[i];
                file.id = ++tc._file.fileId;
                file.inputId = fileInputId;
                tc._file.files.push(file);
            }
            // Show files data on element
            this.set("filesData", $fileInput);
            tc.fn._handle($fileInput, null, "change");
        }
    },
    // Delete
    delete: function (type, data = {}) {
        if (type === "inputFiles") {
            let inputsIds = [];
            if (typeof data === "string")
                inputsIds = [data];
            else if ($(data).length) {
                let $element = $(data);
                if ($element.tagName() !== "input")
                    $element = $element.find(".tc-file-input");
                $element.each(function () {
                    if ($element.hasClass("tc-file-input"))
                        inputsIds.push($(this).attr("tc-input-id"));
                });

            }

            // Get file id for delete
            let deletedFiles = [];
            inputsIds.forEach(inputId => {
                this.files.forEach((file, i) => {
                    if (file.inputId == inputId)
                        deletedFiles.push(file.id);
                });
            });

            deletedFiles.forEach(fileId => {
                this.delete("file", fileId);
            });
            inputsIds.forEach(function (inputId) {
                $(`.tc-file-input[tc-input-id="${inputId}"]`).each(function () {
                    tc._file.set("filesData", $(this));
                });
            });
        } else if (type === "file") {
            let fileId = data;
            this.files.forEach((file, i) => {
                if (file.id == fileId) {
                    this.files.splice(i, 1);
                }
            });
        }
    },
    initFileSystem: function () {
        $(`input[type="file"].tc-file-input:not([tc-input-id])`).each(function () {
            // Settings
            let settings = $(this).dataVal("settings", "{}"),
                settingsData = [];
            if (isJson(settings)) {
                settings = JSON.parse(settings);
                for (let key in settings) {
                    let value = settings[key];
                    value = JSON.stringify(value);
                    key = key.replace(/([A-Z])/g, "-$1").toLowerCase();
                    settingsData.push({
                        key: key,
                        value: value
                    });
                }
            }
            // If HTML is already there
            let $parent = $(this).parents(".tc-file-input-group"),
                fileInput = $(this);
            if ($parent.length) {
                settingsData.forEach(item => {
                    item.key = item.key.replace(/\_/g, "-");
                    $parent.attr(`data-${item.key}`, item.value);
                });
            } else {
                // Clone input if html is not there
                fileInput = $(this).clone();
            }
            // Set id
            fileInput.attr("tc-input-id", ++tc._file.fileInputId);
            // Required Attributes
            if (this.hasAttribute("required")) {
                fileInput.removeAttr("required");
                fileInput.attr("data-required", true);
            }
            // If html already there
            if ($parent.length) return true;
            // Set New HTML of tc file input
            let tcFilesInfoHtml = `<div class="tc-files-data"><p class="p-2">No File Selected</p></div>`,
                tcFilesLength = "s";

            if (!this.hasAttribute("multiple")) {
                tcFilesInfoHtml = ``;
                tcFilesLength = ``;
            }
            fileInput = fileInput.get(0).outerHTML;

            settingsData = settingsData.map(item => `data-${item.key}="${item.value}"`).join(' ');
            $(`<div class="tc-input-group tc-file-input-group" ${settingsData}>
                <input class="form-control tc-files-name" title="Drop/Paste File${tcFilesLength} here to upload" placeholder="Upload File${tcFilesLength}" readonly>
                <label class="tc-input-group-txt" title="Click here to select file${tcFilesLength}">
                    <span><i class="fas fa-folder"></i></span>
                    ${fileInput}
                </label>
                ${tcFilesInfoHtml}
            </div>`).insertBefore($(this));
            $(this).remove();
        });
    }
};
tc._file.initFileSystem();
// show files numbers
$(document).on("change", ".tc-file-input", function () {
    tc._file.set('files', {
        element: this,
        files: this.files,
    });
    $(this).val('');
});
// Remove Tc File
$(document).on("click", '.tc-file-input-group .tc-file .tc-file-remove-btn', function () {
    let $parent = $(this).parents(".tc-file-input-group"),
        fileId = $(this).parents(".tc-file").data("id"),
        $fileInput = $parent.find(".tc-file-input");
    tc._file.delete("file", fileId);
    $(this).parents(".tc-file").remove();
    tc._file.set("filesData", $fileInput);
});
// On Paste
$(document).on("paste", ".tc-file-input-group .tc-files-name", function (e) {
    let inputGroup = $(this).parents(".tc-file-input-group"),
        items = e.originalEvent.clipboardData.items,
        fileInput = inputGroup.find(`input[type='file'].tc-file-input`);
    if (fileInput.length < 1) return true;
    let files = [];
    if (items.length > 0) {
        for (let i = 0; i < items.length; i++) {
            let file = items[i].getAsFile();
            if (file) files.push(file);
        }
    }
    tc._file.set('files', {
        element: fileInput,
        files: files
    })
});
// On Drag over/start
$(document).on("dragenter dragover", ".tc-file-input-group", function (e) {
    e.preventDefault();
    let data = e.originalEvent.dataTransfer;
    if (data.types && (data.types.indexOf ? data.types.indexOf('Files') != -1 : data.types.contains('Files'))) {
        $(this).addClass("tc-dragover");
    }
});
$(document).on("drop", ".tc-file-input-group", function (e) {
    e.preventDefault();
    $(this).removeClass("tc-dragover");
    let dt = e.originalEvent.dataTransfer;
    let files = dt.files;

    let inputGroup = $(this),
        fileInput = inputGroup.find(`input[type='file'].tc-file-input`);
    if (fileInput.length < 1) return true;

    tc._file.set("files", {
        element: fileInput,
        files: files
    });
});
// On Drag leave
$(document).on("dragleave", ".tc-file-input-group", function (e) {
    e.preventDefault();
    $(this).removeClass("tc-dragover");
});