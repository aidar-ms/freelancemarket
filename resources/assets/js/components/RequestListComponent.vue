<template>
    <div>
        <div class="card card-body" v-for="(request, index) in requests" v-bind:key="index">

            <div class="row">
                <div class="col-md-10 fl-info">

                    <p> From freelancer: {{request.freelancer_name}} </p>
                    <p> Contact email: {{request.freelancer_email}} </p>

                </div>

                <form method="post">
                    <input type="hidden">
                </form>

                <div class="col-md-2">
                    <form method="get" action="/api/enter-contract/1">



                    </form>

                    <button class="btn btn-warning" v-on:click="acceptRequest(request)">
                        Accept
                    </button>
                    <button class="btn btn-danger" v-on:click="rejectRequest(request)">
                        Reject
                    </button>

                </div>
            </div>

        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                requests: [],
                freelancer: {
                    id: '',
                    name: '',
                    email: '',
                }
            }
            
        },

        created() {
            var vm = this;

            axios.get('/api/list-requests')
                    .then(function(response) {
                        console.log(response);
                        vm.requests = response.data.data;
                    })
                    .catch(function(error) {
                        console.log(error);
                    });

        },

        methods : {

            acceptRequest(request) {

                axios.put('/api/accept-request/'+request.id)
                    .then(function(response) {
                        console.log(response);
                    })
                    .catch(function(error) {
                        //console.log(error);
                        alert('Error at request');
                    });
                
                this.freelancer.id = request.freelancer_id;
                this.freelancer.name = request.freelancer_name;
                this.freelancer.email = request.freelancer_email;

                axios.put('/api/enter-contract/' + request.contract_id, this.freelancer)
                    .then(function(response) {
                        console.log(response);
                    })
                    .catch(function(error) {
                        alert('Error at enter-contract')
                    })

                alert('Request accepted');

            },

            rejectRequest(request) {

                axios.put('/api/reject-request/'+request.id)
                    .then(function(response) {
                        console.log(response);
                        alert('Request has been rejected')
                    })
                    .catch(function(error) {
                        //console.log(error);
                        alert('Error at reject request');
                    });
            }

        }

    }

</script>
