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

                    <button dusk="accept-request" class="btn btn-warning" v-on:click="acceptRequest(request)">
                        Accept
                    </button>
                    <button dusk="reject-request" class="btn btn-danger" v-on:click="rejectRequest(request)">
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

            axios.get('/api/requests')
                    .then(function(response) {
                        console.log(response);
                        vm.requests = response.data;
                    })
                    .catch(function(error) {
                        console.log(error);
                    });

        },

        methods : {

            acceptRequest(request) {

                axios.get('/api/requests/'+request.id+'/accept')
                    .then(function(response) {
                        console.log(response);
                    })
                    .catch(function(error) {
                        //console.log(error);
                        if(error.response.status === 403) {
                            alert('Message from server: ' + error.response.data);
                        }
                    });
                
                this.freelancer.id = request.freelancer_id;
                this.freelancer.name = request.freelancer_name;
                this.freelancer.email = request.freelancer_email;

                axios.put('/api/contracts/' + request.contract_id + '/enter', this.freelancer)
                    .then(function(response) {
                        if (response.status === 200) 
                            alert('Contract assigned');
                        console.log(response);
                    })
                    .catch(function(error) {
                        if(error.response.status === 403) {
                            alert(error.response.data)

                        }
                    })

               window.location.reload();

            },

            rejectRequest(request) {

                axios.get('/api/requests/'+request.id+'/reject')
                    .then(function(response) {
                        console.log(response);
                        window.location.reload();
                    })
                    .catch(function(error) {
                         if(error.response.status === 403) {
                            alert('Message from server: ' + error.response.data);
                        }
                    });
                    
               window.location.reload();
            }

        }

    }

</script>
