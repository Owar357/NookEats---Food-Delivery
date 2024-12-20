<template>
    <v-container>
        <v-row>
            <!-- Iterate over each pedido -->
            <v-col v-for="pedido in pedidos.filter(p => p.estado_pedido !== 'A').slice(0, 3)" :key="pedido.id" cols="12"
                sm="4">
                <v-card hover variant="flat">
                    <v-card-title>
                        Pedido N°: {{ pedido.numero_pedido }}
                    </v-card-title>

                    <v-card-subtitle>
                        Fecha: {{ pedido.fecha_hora_pedido }}
                    </v-card-subtitle>

                    <v-card-text>


                        <v-row align="center" justify="space-between">
                            <!-- Nombre del usuario -->
                            <v-col cols="6">
                                <v-chip color="primary" text-color="white" class="mb-2">
                                    Usuario: {{ pedido.user.name }}
                                </v-chip>
                            </v-col>

                            <!-- Método de pago -->
                            <v-col cols="6" class="text-right">
                                <v-chip :color="pedido.metodo_pago === 'T' ? 'green' : 'blue'" text-color="white"
                                    class="mb-2">
                                    Pago: {{ pedido.metodo_pago === 'T' ? 'Tarjeta' : 'Presencial' }}
                                </v-chip>
                            </v-col>
                        </v-row>

                        <v-divider class="my-2 custom-divider" :thickness="2"></v-divider>
                        <v-title>DETALLES:</v-title>
                        <v-list dense>
                            <v-list-item-group v-if="pedido.pedido_comidas.length">
                                <v-list-item v-for="(comida, index) in pedido.pedido_comidas" :key="index" class="ml-0">
                                    <v-list-item-content>
                                        <v-list-item-title>{{ comida.comida_restaurante.nombre }}</v-list-item-title>
                                        <v-list-item-subtitle>Cantidad: {{ comida.cantidad }}</v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-list-item-group>
                        </v-list>

                        <div class="mt-2" style="font-weight: bold; color: blueviolet; font-size: 18px;">
                            Total: ${{ pedido.total }}
                        </div>




                    </v-card-text>

                    <v-card-actions>
                        <!-- Botón para cambiar el estado del pedido -->
                        <v-btn @click="aceptarPedido(pedido)" color="primary" variant="outlined" class="ml-auto">
                            Confirmar Pedido
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
export default {

    data: () => {

        return {

            pedidos: [],
            pedidoInterval: null,


        }

    },

    methods: {

     async aceptarPedido(pedido) {

        try {
     
     const response =   await this.axios.patch(`/admin/rest/comida/pedido/${pedido.id}/estado`, 
        { estado_pedido: 'A' 

        });

        if (response.data.status === 'ok') {
         
            pedido.estado_pedido = 'A';

            
            this.ListarPedidos();

            
            console.log(response.data.message);
        }

    } catch (error) {
        console.error('Error al actualizar el estado del pedido:', error);
    }

            
        },


        async ListarPedidos() {

            const response = await this.axios.get('/admin/rest/comida/pedidos/listar');

            this.pedidos = response.data.data

            console.log(this.pedidos)

        },


    },
    mounted() {

        this.ListarPedidos();
    }
}
</script>


<style scoped>
.custom-divider {
    border-color: blue;
    opacity: 1;
}

.v-card:hover {
    box-shadow: 0 4px 12px rgba(238, 130, 238, 0.2);
    transform: scale(1.03);
    transition: all 0.3s ease;
}
</style>