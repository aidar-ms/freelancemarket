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
            var vm = this;
            this.data.contract_id = this.contractId;

        
        },

        methods: {

            onSubmit() {
                var vm = this;

                axios
                    .get('api/requests/'+ vm.data.contract_id +'/send')
                    .then(function(response) {
                        console.log(response);
                    })
                    .catch(function (error) {
                        console.log(error.response);
                        if(error.response.status === 403) {
                            alert("Contract was already requested");

                        }
                    });
        

                return false;
            }

        }
    }

</script>
