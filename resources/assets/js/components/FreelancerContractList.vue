<template>

    <div>

        <div class="card card-body" v-for="(contract, index) in contracts" v-bind:key="index">
            <div class="row">
                <div class="col-md-10">
                    <h2> {{ contract.title }} </h2>
    
                    <p class="small"> Contract ID: <span style="vertical-align: top"> {{contract.id}} </span> </p>
                    <p class="small"> Due at: {{contract.deadline_at}}   </p>
                    <p class="small"> Price: {{contract.price}} </p>
                    <br>
                    <p> {{ contract.description }} </p>
                </div>

            </div>
     
        </div> 

    </div>
</template>

<script>
    export default {

        name: 'freelancer-contract-list',

        data() {
            return {
                requests: [],
                contracts: [],
                contract: {
                    title: '',
                    description: '',
                    price: '',
                    deadline_at: ''
                }
            }
        },

        mounted() {
            console.log('Component mounted.')
        },

        created() {
            var vm = this;

            axios.get('/api/contracts')
                .then(function(response) {
                    console.log(response);
                    vm.contracts = response.data;
                })
                .catch(function (error) {
                    console.log(error);
                });
        }
    }
</script>