document.addEventListener('DOMContentLoaded', function () {
    let captchaLoaded = false;

    function loadRecaptcha() {
        if (captchaLoaded || typeof wpcf7_recaptcha === 'undefined' || !wpcf7_recaptcha.sitekey) {
            return;
        }

        captchaLoaded = true;

        const script = document.createElement('script');
        script.src = 'https://www.google.com/recaptcha/api.js?render=' + encodeURIComponent(wpcf7_recaptcha.sitekey) + '&ver=3.0';
        script.async = true;
        script.defer = true;
        script.onload = initRecaptcha;
        document.body.appendChild(script);
    }

    function initRecaptcha() {
        const sitekey = wpcf7_recaptcha.sitekey;
        const actions = wpcf7_recaptcha.actions || {};
        const homepageAction = actions.homepage || 'homepage';
        const contactFormAction = actions.contactform || 'contactform';

        const executeAction = function (params = {}) {
            if (typeof grecaptcha === 'undefined') {
                return;
            }

            const action = params.action || contactFormAction;
            const callback = params.callback;
            const callbackParams = params.callbackParams || [];

            grecaptcha.execute(sitekey, { action: action })
                .then(function (token) {
                    document.dispatchEvent(new CustomEvent('wpcf7grecaptchaexecuted', {
                        detail: {
                            action: action,
                            token: token,
                        },
                    }));
                })
                .then(function () {
                    if (typeof callback === 'function') {
                        callback.apply(null, callbackParams);
                    }
                })
                .catch(function (error) {
                    console.error(error);
                });
        };

        grecaptcha.ready(function () {
            executeAction({ action: homepageAction });
        });

        document.addEventListener('change', function (event) {
            if (event.target.closest('.wpcf7-form')) {
                executeAction({ action: contactFormAction });
            }
        });

        if (typeof wpcf7 !== 'undefined' && typeof wpcf7.submit === 'function') {
            const originalSubmit = wpcf7.submit;
            wpcf7.submit = function (element, options = {}) {
                executeAction({
                    action: contactFormAction,
                    callback: originalSubmit,
                    callbackParams: [element, options],
                });
            };
        }

        document.addEventListener('wpcf7grecaptchaexecuted', function (event) {
            const formInputs = document.querySelectorAll('form.wpcf7-form input[name="_wpcf7_recaptcha_response"]');

            for (let i = 0; i < formInputs.length; i++) {
                formInputs[i].setAttribute('value', event.detail.token);
            }
        });
    }

    document.addEventListener('focusin', function (event) {
        if (event.target.closest('.wpcf7-form')) {
            loadRecaptcha();
        }
    });
});
