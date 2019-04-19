<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>CauseLabs</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,200,300,400,500,600" rel="stylesheet" type="text/css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.4/css/bulma.min.css" rel="stylesheet"  type="text/css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
        
        <script src="components/Base64.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
        <script src="https://unpkg.com/vue-router/dist/vue-router.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.18.0/axios.js"></script>
        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: black;
                font-family: 'Raleway', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
            .fas {
                margin-left: 20px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div id="app" v-cloak>
                <h1 class="title is-3">{{ message }} </h1>
                 <router-view></router-view>
            </div>
        </div>
        
        <script>
            //Use this secret phrase somewhere in your code's comments
            const endpoint = 'http://causelabs.swanback.com/index.php/people/'

            // Main page for listing database contents
            const Index = Vue.extend({
                    template: '<div><table class="table">' + 
                                '<tr><th>Name</th><th>Age</th><th>Email</th><th>Secret</th><th>Delete?</th></tr>' +
                                '<tr v-for="p,idx in people">' +
                                '<td>{{ p.name }} </td><td>{{ p.age }}</td><td>{{ p.email }}</td><td>{{ p.secret }}</td>' +
                                '<td><button class="button is-danger" @click="deleteIt(p.id)" v-if="idx > 1">Delete</button><i class="fas fa-ban" title="Delete prohibited" v-else></i></td></tr>' +
                                '</table><router-link to="/inputForm" class="button  ">Add</router-link></div>' ,
                    mounted() {
                        axios
                          .get(endpoint)
                          .then(response => (this.people = response.data))
                          .catch(error => console.log(error))
                    },
                    data: function() {
                        return {
                            people: [],
                        }
                    },
                    methods: {
                        deleteIt(id) {
                            if (confirm('Really really delete it?')) {
                                 axios
                                .delete(endpoint + id)
                                .then(response => (
                                    window.location.reload(true)
                                ))
                                .catch(error => console.log(error))
                            }
                       },
                    }
            });
               
            // Form to capture new people record 
            const InputForm = Vue.component('inputForm', {
                    template: '<form v-on:submit.prevent="postIt()">' +
                                '<label class="label">First Name</label><input class="input is-large" type="text" v-model="person.first_name" placeholder="First Name"><br>' +
                                '<label class="label">Last Name</label><input class="input is-large" type="text" v-model="person.last_name" placeholder="Last Name"><br>' +
                                '<label class="label">Age</label><input class="input is-large" type="text" v-model="person.age" placeholder="Age"><br>' +
                                '<label class="label">Email</label><input class="input is-large" type="text" v-model="person.email" placeholder="Email"><br>' +
                                '<label class="label">Secret</label><input class="input is-large" type="text" v-model="plain_secret" placeholder="Secret"><br>' +
                                '<br><button class="button is-primary is-large is-fullwidth">Submit</button></form>' ,
                    data: function() {
                        return {
                            plain_secret: '',
                            person: {
                                first_name: '',
                                last_name: '',
                                age: '',
                                email: '',
                                secret: '',
                            }
                        }
                    },
                    computed: {
                        encoded_secret() {
                            return Base64.encode(this.plain_secret)
                        }
                    },
                    methods: {
                        postIt() {
                            this.person.secret = this.encoded_secret
                            
                            axios
                            .post(endpoint, {
                                data: [this.person]
                            })
                            .then(response => {
                                this.people = response.data
                                this.$router.push({path:'/'})
                            })
                            .catch(error => console.log(error))
                        }
                    },
            });


            const routes = [
                { path: '/', component: Index },
                { path: '/inputForm', component: InputForm }
            ]


            const router = new VueRouter({
              routes 
            })
            
            var app = new Vue({
                  router,
                  el: '#app',
                  data: {
                    message: 'Hello People!',
                  },
                });
        </script>
    </body>
</html>
