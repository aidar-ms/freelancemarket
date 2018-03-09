<template>
  <div class="container">
      <div class="modal fade" abindex="-1" role="dialog" aria-hidden="true">

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
                        <button type="submit" name="submit" class="btn btn-primary" v-on:click="submitEdited(contract)">Save</button>
                    </div>
                </div>
            </div>
        </div>
            
    
    </div>
</template>

<script>
    export default {

        props: ['contract'],
        
        data() {
            return {
                contract2Send: {
                    title: '',
                    description: '',
                    price: '',
                    deadline_at: ''
                }

            }
            
        },

        mounted() {
            console.log('Component mounted');
        },

        methods: {
            submitEdited(contract) {

                this.contract2Send.title = contract.title;
                this.contract2Send.description = contract.description;
                this.contract2Send.price = contract.price;
                this.contract2Send.deadline_at = contract.deadline_at;

                axios.put('/api/contracts/'+contract.id, this.contract2Send)
                    .then(function(response) {
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
                    })
                    .catch(function(error) {
                        console.log(error);
                    })

            },
        }
    }

</script>
