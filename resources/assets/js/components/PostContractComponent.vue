<template>

    <form method="post" id="createContractForm" v-on:submit.prevent="onSubmit">
        <label for="title" class="col-md-6">Title</label>
        <input type="text" class="col-md-6" id="title" name="title" v-model="contract.title">

        <label for="description" class="col-md-6" style="">Description</label>
        <textarea rows="10" cols="80" form="createContractForm" id="description" name="description" v-model="contract.description"> 
        </textarea> 

        <label for="price" class="col-md-6">Price</label>
        <input class="col-md-6" type="number" id="price" name="price" v-model="contract.price">

        <label for="deadline" class="col-md-6">Deadline</label>
        <input class="col-md-6" type="datetime-local" id="deadline" name="deadline" v-model="contract.deadline">
    </form>

</template>

<script>

 export default {

        data() {
            return  {
                form : document.getElementById('createContractForm'),
                contract : {
                    title : '',
                    description : '',
                    price : '',
                    deadline : ''
                }
            }
        },

        methods: {

            onSubmit() {
                var vm = this;

                axios
                    .post('/api/contracts', vm.contract)
                    .then(function(response) {
                        console.log(response);
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

                return false;
            }

        }
    }

</script>