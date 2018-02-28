<template>

    <div>

        <div class="card card-body" v-for="(contract, index) in contracts" v-bind:key="index">
            <div class="row">
                <div class="col-md-10">
                    <h1 class="card-title">
                        <a :href="'/contracts/'+contract.id">
                            {{contract.title}}
                        </a>
                    </h1>
                    
                    <p>{{contract.description}}</p>
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

            axios.get('/api/browse')
                .then(function(response) {
                    console.log(response);
                    vm.contracts = response.data.data;
                    console.log(vm.contracts);
                })
                .catch(function (error) {
                    console.log(error);
                });
        }
    }
</script>