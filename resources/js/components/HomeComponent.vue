<template>
  <v-app>
   <v-container>
  <v-row justify="center">
    <v-col cols="12" md="6">
      <v-text-field  
      v-model = "searchQuery"
      label="Buscar Restaurantes"
      prepend-inner-icon="mdi-magnify"
      outlined
      clearable
      @input="onSearch"
      >
  
      </v-text-field>
    </v-col>
  </v-row>  

<!-- Sección para las cards -->
<h1 class="display-5" style="font-weight:900;">Nuevos Restaurantes Por Descubrir</h1>
   <v-row justify="start">
    <v-col v-for="(restaurante, index) in restaurantes " :key="index" cols="12" md="2" class="mt-3" >
     
       <!-- Usamos router-link para la navegación -->

       
      <v-card
    max-width="200"
     @click="goToRestaurante(restaurante.id)"
  >
  
    <v-img
      height="150"
      width="175"
      :src="restaurante.imagen"
      cover
      style="background-color: red;object-position: center;"
      
    ></v-img>

    <v-card-item>
      <v-card-title style="align-items: center;text-align: left;padding-top: 5%;padding-left: 6%;">{{ restaurante.nombre }}</v-card-title>

     <v-card-subtitle style="align-items: center;text-align: left;padding-left: 6%;" >
        <span class="me-1">{{restaurante.tipoNegocio.nombre}}</span>
      </v-card-subtitle> 

    </v-card-item>
  </v-card>


    </v-col>

  
</v-row>
   </v-container>
  </v-app>
</template>


<script>
 export default{
  data(){
    return{
      searchQuery:'',
      restaurantes:[],
    }; 
  },
  methods:{

    goToRestaurante(id) {
      // Redirige a Laravel, activando la ruta correcta
      window.location.href = `/restaurante/${id}`;
    },
    
  async fetchRestaurantes(){
  
    try {
       const response = await this.axios.get('/restaurantes/recientes');
       console.log("Restaurantes recibidos:", response.data.restaurante || []);
       this.restaurantes = response.data.restaurante || [];

    } catch (error) {
     // console.error("Error fetching modelos: " ,error);
    }
  } , 

  onSearch(){
     console.log('Texto ingresado: ', this.searchQuery);
   } 
  },
  mounted(){
    this.fetchRestaurantes();
  }
 }
</script>