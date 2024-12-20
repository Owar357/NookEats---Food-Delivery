<template>
    <v-card
      title="Historial de ventas"
      flat
    >
      <template v-slot:text>
        <v-text-field
          v-model="search"
          label="Search"
          prepend-inner-icon="mdi-magnify"
          variant="outlined"
          hide-details
          single-line
        ></v-text-field>
      </template>
  
      <v-data-table
        :headers="headers"
        :items="historial"
        :search="search"
      >
        
        <template v-slot:item.accion ={item} >

            <td class="d-flex justify-center align-center">
      <v-btn  block color="light-blue" @click="AbrirDialog(item)">
        <v-icon>mdi-eye</v-icon>
      </v-btn>
    </td>
            
        </template>


    </v-data-table>

    <template>
  <div class="text-center pa-4">
    <v-dialog
      v-model="dialog"
      width="auto"
    >
      <v-card
        max-width="400"
        prepend-icon=" mdi-invoice-list-outline"
        text="Aqui iran los detalles del pedido "
        title="Detalles del pedido"
      >
        <template v-slot:actions>
          <v-btn
            class="ms-auto"
            text="Ok"
            @click="dialog = false"
          ></v-btn>
        </template>
      </v-card>
    </v-dialog>
  </div>
</template>

    
    </v-card>
  </template>

<script>
    export default {

        data: () =>{
         
            return{

            dialog : false,
            headers:[
            {title: 'Estado del pedido' , key:'estadoPedido'},
            {title: 'Número de Pedido ' , key:'numeroPedido'},
            {title: 'Usuario' , key:'usuario'},
            {title: 'Fecha del Pedido' , key:'fechaHoraPedido'},
            {title: 'Total' , key:'total'},
            {title: 'Metodo de Pago ' , key:'metodoPago'},
            {title: 'Ver Detalles del Pedido', key:'accion'}
            ],
            pedidosOrdenados:[],
            search: '',
            historial: [],

            }

        },

      methods:{
         

         AbrirDialog(item){
           
           this.dialog = true; 
         },
         ordernarComidas(data){
         this.pedidosOrdenados = data.map(pedido => {
           
           return pedido.pedido_comidas.map(comida => ({
            estadoPedido: pedido.estadoPedido,
            numeroPedido: pedido.numeroPedido,
            fechaHoraPedido: pedido.fechaHoraPedido,
            total: pedido.total,
            metodoPago: pedido.metodoPago,
            usuario:pedido.usuario,
        }));
    
    }).flat();

         },

        async listarHistorial(){

            const response = await this.axios.get('/admin/rest/comida/ventas/historial')
            this.historial = response.data.data;
        }
      },
      mounted() {
          
         this.listarHistorial();
         
        }
    }
</script>
