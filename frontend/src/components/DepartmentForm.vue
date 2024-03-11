<template>
 <div>
   <div id="app">
     <v-app id="inspire" style="width: 1200px;">
       <v-form @keyup.native.enter="save" v-model="valid">
         <v-container>
           <v-row>
             <v-col
               cols="12"
               md="12"
             >
               <v-text-field
                 v-model="firstname"
                 :rules="nameRules"
                 :counter="255"
                 label="Department id"
                 dark
                 required
               ></v-text-field>
             </v-col>
             <v-col
               cols="12"
               md="4"
             >
               <v-btn>Send</v-btn>
             </v-col>


           </v-row>
         </v-container>
       </v-form>
     </v-app>
   </div>
 </div>
</template>

<script>
  import Vuetify from 'vuetify';
export default {
  name: 'HelloWorld',
  el: '#app',
  vuetify: new Vuetify(),
  data: () => ({
    valid: false,
    firstname: '',
    lastname: '',
    nameRules: [
      v => !!v || 'Name is required',
      v => v.length <= 255 || '255 dan kamroq bolishi kerek',
      v => v.length >= 6 || '6 tadan kam bolmasligi kerek',
    ],
    email: '',
    emailRules: [
      v => !!v || 'E-mail is required',
      v => /.+@.+/.test(v) || 'E-mail must be valid',
    ],
  })
}
</script>
