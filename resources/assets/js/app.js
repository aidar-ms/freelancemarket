
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('post-contract', require('./components/PostContractComponent.vue'));
Vue.component('request-list', require('./components/RequestListComponent.vue'));
Vue.component('make-request', require('./components/MakeRequest.vue'));
Vue.component('contract-list', require('./components/ContractListComponent.vue'));
Vue.component('freelancer-contract-list', require('./components/FreelancerContractList.vue'));
Vue.component('browse-contracts', require('./components/BrowseContracts.vue'));
Vue.component('payment-component', require('./components/PaymentComponent.vue'));


const app = new Vue({
    el: '#app'
});
