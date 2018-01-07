window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.$ = window.jQuery = require('jquery');

    require('bootstrap-sass');
} catch (e) {
}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-CSRF-TOKEN'] = window.Laravel.csrfToken;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.events = new Vue();

window.flash = function (message, level = 'success') {
    window.events.$emit('flash', {'message': message, 'level': level});
};
Vue.prototype.signedIn = window.Laravel.signedIn;

import InstantSearch from 'vue-instantsearch';

Vue.use(InstantSearch);

let authorization = require('./authorization');
Vue.prototype.authorize = function (...params) {
    if (!window.Laravel.signedIn) return false;

    if (typeof params[0] === 'string') {
        return authorization[params[0]](params[1]);
    }

    return params[0](window.Laravel.user);
};