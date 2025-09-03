//Create Modal
$('#create').on('show.bs.modal', function () {
    var input = document.querySelector("#phone");
    var validationMsg = document.querySelector("#validation-msg");
    validateMobile(input, validationMsg);
});


//Edit Modal
$('#edit').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var modal = $(this);
    var no = document.getElementById("phone_edit").value;
    var input = document.querySelector("#phone_edit");
    var errorMsg = document.querySelector("#error-msg-edit");
    var validMsg = document.querySelector("#valid-msg-edit");
    var action = 'Edit';
    modal.find('.modal-body #id_edit').val(button.data('id'));
    modal.find('.modal-body #name_edit').val(button.data('name'));
    modal.find('.modal-body #address_edit').val(button.data('address'));
    modal.find('.modal-body #credit_input_edit').val(formatMoney(button.data('credit_limit')));
    modal.find('.modal-body #phone_edit').val(button.data('phone'));
    modal.find('.modal-body #email_edit').val(button.data('email'));
    modal.find('.modal-body #tin_edit').val(button.data('tin'));

    validateMobile(input, errorMsg, validMsg, action);
});


$('#delete').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var message = "Are you sure you want to delete '".concat(button.data('name'), "'?");
    var modal = $(this);
    modal.find('.modal-body #message').text(message);
    modal.find('.modal-body #id').val(button.data('id'))
});

//Change the input into money format with fixed 2 decimal places
$("#credit_input").on('change', function (evt) {
    if (evt.which != 110) {//not a fullstop
        var n = Math.abs((parseFloat($(this).val().replace(/\,/g, ''), 10) || 0));
        $(this).val(n.toLocaleString("en", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }));
    }
    var credit_input = (document.getElementById("credit_input").value);
    credit_limit_amount = (parseFloat(credit_input.replace(/\,/g, ''), 10) || 0);
    $('#create').find('.modal-body #credit_limit_amount').val(credit_limit_amount);
});

//Change the input into money format with fixed 2 decimal places
$("#credit_input_edit").on('change', function (evt) {
    if (evt.which != 110) {//not a fullstop
        var n = Math.abs((parseFloat($(this).val().replace(/\,/g, ''), 10) || 0));
        $(this).val(n.toLocaleString("en", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }));
    }
    var credit_input = (document.getElementById("credit_input_edit").value);
    credit_limit_amount = (parseFloat(credit_input.replace(/\,/g, ''), 10) || 0);
    $('#edit').find('.modal-body #credit_limit_amount_edit').val(credit_limit_amount);
});

function validateMobile(input, validationMsg, action) {
    const errorMap = [
        "Invalid Phone Number",
        "Invalid Country Code",
        "Too Short",
        "Too Long",
        "Invalid Phone Number"
    ];

    const reset = function () {
        validationMsg.classList.remove("text-success", "text-danger");
        validationMsg.classList.add("hide");
        validationMsg.innerHTML = "";
    };

    const itiOptions = {
        initialCountry: "tz",
        onlyCountries: ["tz", "ug", "ke", "rw", "bi", "sd"],
        separateDialCode: false,
        nationalMode: false,
        utilsScript: "../assets/plugins/intl-tel-input/js/utils.js?1562189064761"
    };

    const iti = window.intlTelInput(input, itiOptions);

    function getDialCode() {
        return "+" + iti.getSelectedCountryData().dialCode;
    }

    function ensurePrefix() {
        let dialCode = getDialCode();
        if (!input.value.startsWith(dialCode)) {
            input.value = dialCode + " ";
        }
    }

    function cleanLeadingZero() {
        let dialCode = getDialCode();
        let rest = input.value.replace(dialCode, "").trim();

        if (rest.startsWith("0")) {
            rest = rest.replace(/^0+/, "");
            input.value = dialCode + " " + rest;
        }
    }

    function liveValidate() {
        reset();
        ensurePrefix();
        cleanLeadingZero();

        let dialCode = getDialCode();
        let numberPart = input.value.replace(dialCode, "").trim();

        if (numberPart === "") {
            return;
        }

        if (iti.isValidNumber()) {
            validationMsg.classList.add("text-success");
            validationMsg.innerHTML = "✓ Valid number";
            validationMsg.classList.remove("hide");
            $("#customer_save_btn, #edit_btn").prop("disabled", false);

            document.getElementById("phone-number").value = iti.getNumber();
            if (action) {
                document.getElementById("phone_edit").value = iti.getNumber();
            }
        } else {
            validationMsg.classList.add("text-danger");
            $("#customer_save_btn, #edit_btn").prop("disabled", true);
            const errorCode = iti.getValidationError();
            validationMsg.innerHTML = errorMap[errorCode] || "Invalid number";
            validationMsg.classList.remove("hide");
        }
    }

    input.addEventListener("keydown", function (e) {
        const dialCode = getDialCode();
        const cursorPos = input.selectionStart;

        if (
            (cursorPos <= dialCode.length && (e.key === "Backspace" || e.key === "Delete")) ||
            (cursorPos < dialCode.length && e.key.length === 1)
        ) {
            e.preventDefault();
            input.setSelectionRange(dialCode.length + 1, dialCode.length + 1);
            return;
        }

        let currentNumber = input.value.replace(dialCode, "").trim();
        if (currentNumber.length >= 9 && e.key.length === 1 && /\d/.test(e.key)) {
            e.preventDefault();
        }
    });

    input.addEventListener("input", liveValidate);
    input.addEventListener("change", liveValidate);

    input.addEventListener("countrychange", function () {
        ensurePrefix();
        liveValidate();
    });

    ensurePrefix(); 
    reset(); 
}

function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
    try {
        decimalCount = Math.abs(decimalCount);
        decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

        const negativeSign = amount < 0 ? "-" : "";

        let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
        let j = (i.length > 3) ? i.length % 3 : 0;

        return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
    } catch (e) {
        console.log(e)
    }
}
