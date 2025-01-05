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
      <v-card
    max-width="200"
  >
  
    <v-img
      height="130"
      :src="restaurante.imagen"
      cover
    ></v-img>

    <v-card-item>
      <v-card-title>{{ restaurante.nombre }}</v-card-title>

     <v-card-subtitle>
        <span class="me-1">{{restaurante.tipoNegocio.nombre}}</span>
      </v-card-subtitle> 

    </v-card-item>

    <v-card-text style="overflow: hidden; text-overflow: ellipsis; height: 100px;">
      <div>
       {{restaurante.descripcion}}
      </div>
    </v-card-text>
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
  
  async fetchRestaurantes(){
    try {
       const response = await this.axios.get('restaurantes/recientes');
       console.log("Restaurantes recibidos:", response.data.restaurante || []);
       this.restaurantes = response.data.restaurante || [];

    } catch (error) {
      console.error("Error fetching modelos: " ,error);
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