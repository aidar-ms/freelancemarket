<template>
        
    <ul id="contractList" v-if="contracts && contracts.length" style="list-style: none">
        <li class="card" v-for="(contract, index) in contracts" :key="index">
            <h1 class="card-title">{{contract.title}}</h1>
            <div class="card-body">
                <p>{{contract.description}}</p>
            </div>
        </li> 
    </ul> 
</template>

<script>
    export default {

        name: 'contract-list',

        data: function() {
            return {
                contracts: []
            }
        },

        mounted() {
            console.log('Component mounted.')
        },

        created() {

            axios.get('/contracts')
                .then(function (response) {
                    console.log(response);
                    this.contracts = response.data;
                    console.log(this.contracts);
                })
                .catch(function (error) {
                    console.log(error);
                });
        }
    }
</script>