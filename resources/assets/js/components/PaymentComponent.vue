<template>

    <form method="post" v-on:submit.prevent="onSubmit">
        <input type="hidden" v-model="data.contract_id">
        <input type="hidden" v-model="data.hirer_email">
        <input type="hidden" v-model="data.freelancer_email">
        <input class="btn btn-default" type="submit" value="Pay">
    </form>

</template>

<script>

    export default {

        props: ['contractId', 'hirerEmail', 'freelancerEmail'],

        data() {
            return  {
                data : {
                    contract_id : "",
                    hirer_email : "",
                    freelancer_email : "",
                }
            }
        },


        mounted() {
            this.data.contract_id = this.contractId;
            this.data.hirer_email = this.hirerEmail;
            this.data.freelancer_email = this.freelancerEmail;
        },

        methods: {

            onSubmit() {
                var vm = this;

                axios
                    .post('/api/make-payment', vm.data)
                    .then(function(response) {
                        console.log(response);
                        alert('Message from server: ' + response.data.message);
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

                    window.location.reload();

                return false;
            }

        }
    }

</script>
