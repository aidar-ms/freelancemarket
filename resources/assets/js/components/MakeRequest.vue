<template>

    <form method="post" v-on:submit.prevent="onSubmit">
        <input type="hidden" v-model="data.contract_id">
        <input class="btn btn-default" type="submit" value="Request this contract">
    </form>

</template>

<script>

    export default {

        props: ['contractId'],

        data() {
            return  {
                data : {
                    contract_id : "",
                }
            }
        },


        mounted() {
            this.data.contract_id = this.contractId;
        },

        methods: {

            onSubmit() {
                var vm = this;

                axios
                    .get('/api/make-request/' + vm.data.contract_id)
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
