<template>

    <div>

        <div class="card card-body" v-for="(contract, index) in contracts" v-bind:key="index">
            <div class="row">
                <div class="col-md-10">
                    <h2> {{ contract.title }}</h2>
    
                    <p class="small" v-show="contract.freelancer"> Freelancer: {{contract.freelancer}}</p>
                    <p class="small"> Contract ID: <span style="vertical-align: top"> {{contract.id}} </span> </p>
                    <p class="small"> Due at: {{contract.deadline_at}}   </p>
                    <p class="small"> Price: {{contract.price}} </p>
                    <p class="small"> Status: {{contract.status}} </p>
                    <br>
                    <p> {{ contract.description }}</p>
                    <br>
                    <payment-component :contract = contract >

                    </payment-component>
                </div>

                <div class="col-md-2">

                    <button class="btn btn-warning" data-toggle="modal" :data-target="'#editForm'+index">
                        Edit
                    </button>
                    <button class="btn btn-danger" data-toggle="modal" v-on:click="deleteContract(contract)">
                        Delete
                    </button>

                    <div class="modal fade" :id="'editForm'+index" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">

                        <div class="modal-dialog modal-lg" role="document">
                            
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Edit contract</h5>

                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">

                                    <form id="editContractForm">
                                        <label for="title" class="col-md-6">Title</label>
                                        <input type="text" class="col-md-6" id="title" name="title" v-model="contract.title">

                                        <label for="description" class="col-md-6" style="">Description</label>
                                        <textarea rows="10" cols="80" form="editContractForm" id="description" name="description" v-model="contract.description"> 
                                        </textarea> 

                                        <label for="price" class="col-md-6">Price</label>
                                        <input class="col-md-6" type="number" id="price" name="price" v-model="contract.price">

                                        <label for="deadline" class="col-md-6">Deadline</label>
                                        <input class="col-md-6" type="datetime-local" id="deadline" name="deadline" v-model="contract.deadline_at">
                                    </form>

                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary" v-on:click="submitEdited(contract)">Save</button>
                                </div>
                                
                            </div>
                            
                        </div>
                    </div>

                </div>
            </div>
     
        </div> 

    </div>
</template>

<script>
    export default {
        name: 'contract-list',
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
                    console.log(vm.contracts);
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        methods: {
            submitEdited(contract) {
                this.contract.title = contract.title;
                this.contract.description = contract.description;
                this.contract.price = contract.price;
                this.contract.deadline_at = contract.deadline_at;
                axios.put('/api/contracts/'+contract.id, this.contract)
                    .then(function(response) {
                        alert('Contract edited');
                        console.log(response);
                    })
                    .catch(function(error) {
                        console.log(error);
                    })
            },
            deleteContract(contract) {
                axios.delete('/api/contracts/'+contract.id, this.contract)
                    .then(function(response) {
                        alert('Contract deleted');
                        console.log(response);
                        window.location.reload();
                    })
                    .catch(function(error) {
                        console.log(error);
                    })
            },
        }
    }
</script>
