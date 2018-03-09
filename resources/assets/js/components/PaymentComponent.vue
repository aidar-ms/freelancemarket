<template>

    <form method="post" v-on:submit.prevent="onSubmit" v-show="contract.status === 'active' ">
        <input class="btn btn-default" type="submit" value="Pay">
    </form>

</template>

<script>

    export default {

        props: ['contract'],

     /*   data() {
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
        }, */

        methods: {

            onSubmit() {
                var vm = this;

                axios
                    .get('/api/contracts/' + vm.contract.id + '/close')
                    .then(function(response) {
                        console.log(response);
                    })
                    .catch(function (error) {
                        if(error.response.status === 403) {
                            alert(error.response.data);
                        }
                    });
            }

        }
    }

</script>
