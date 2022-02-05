/**
 * Custom validation method
 */
$.validator.addMethod(
    'validPassword',
    function (value, element, param) {
        if (value != '') {
            if (value.match(/.*[a-z]+.*/i) == null) {
                return false;
            }
            if (value.match(/.*\d+.*/i) == null) {
                return false;
            }
        }
        return true;
    },
    'Must contain at least one letter and one number'
);

/**
 * Signup form validator
 */
$(document).ready(function () {
    $('#formSignup').validate({
        rules: {
            name: 'required',
            email: {
                required: true,
                email: true,
                remote: '/account/validate-email'
            },
            password: {
                required: true,
                minlength: 6,
                validPassword: true,
            },
        },
        messages: {
            email: {
                remote: 'email already taken',
            },
        },
    });
});

/**
 * Edit profile form validation
 */
const userId = $('#profileId').val()

$(document).ready(function () {
    $('#formProfile').validate({
        rules: {
            name: 'required',
            email: {
                required: true,
                email: true,
                remote: {
                    url: '/account/validate-email',
                    data: {
                        ignore_id: function () {
                            return userId;
                        },
                    },
                },
            },
            password: {
                minlength: 6,
                validPassword: true,
            },
        },
        messages: {
            email: {
                remote: 'email already taken',
            },
        },
    });
});

/**
 * Reset password form validator
 */
$(document).ready(function () {
    $('#formPassword').validate({
        rules: {
            password: {
                required: true,
                minlength: 6,
                validPassword: true,
            },
        },
    });
});

/**
 * Login form validator
 */
$(document).ready(function () {
    $('#formLogin').validate({
        rules: {
            name: 'required',
            email: {
                required: true,
                email: true,
            },
            password: {
                required: true,
            },
        },
    });
});

/**
 * Hide-show password
 */
$('#inputPassword').hideShowPassword({
    show: false,
    innerToggle: 'focus',
});
