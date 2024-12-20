<template>
    <v-card flat>
        <v-card-title class="d-flex align-center pe-2">
            <v-icon icon="mdi-tag"></v-icon> &nbsp;
            Promociones

            <v-spacer></v-spacer>

        </v-card-title>

        <v-divider></v-divider>
        <v-data-table v-model:search="search" :filter-keys="['nombre']" :items="datosOrdenados" :headers="headers"
            :items-per-page="5" item-value="nombre">

            <template v-slot:item.estado="{ item }">
                <v-chip :color="item.estado === 1 ? 'green' : 'red'" label>
                    {{ item.estado === 1 ? 'Disponible' : 'No disponible' }}
                </v-chip>
            </template>



            <template v-slot:[`item.precioOriginal`]="{ item }">
                <div>
                    <span class="text-success font-weight-bold">
                        ${{ item.precioOriginal }}USD
                    </span>
                </div>
            </template>

            <template v-slot:[`item.precioDescuento`]="{ item }">
                <div>
                    <span class="text-success font-weight-bold">
                        ${{ item.precioDescuento }} USD
                    </span>
                </div>
            </template>

            <template v-slot:[`item.descuentoAplicado`]="{ item }">
                <div>
                    <span class="text-success font-weight-bold">
                        {{ item.descuentoAplicado }}%
                    </span>
                </div>
            </template>

  

                       




                     <template v-slot:[`item.actions`]="{ item }">
              
            
        <v-tooltip location="top">
            <template v-slot:activator="{ props }">
              <v-col cols="auto" class="pa-0">
                <v-btn v-bind="props" @click="seleccionaritems(item)" icon="mdi-tag-plus" color="primary"
                  small dense class="mx-1">
                </v-btn>
              </v-col>
            </template>
            <span>Edita y Crea tu promocion </span>
          </v-tooltip>
      
                <v-col cols="auto" class="pa-0" v-if="item.descuentoAplicado > 0 ">
            <v-tooltip location="top">
              <template v-slot:activator="{ props }">
                <v-btn v-bind="props" :color="item.estado === 1 ? 'red' : 'green'" @click="CambiarEstado(item)"
                  rounded class="mx-1"
                  style="width: 45px; height: 45px; min-width: 40px; display: flex; justify-content: center; align-items: center;">
                  <v-icon>{{ item.estado === 1 ? 'mdi-tag-off' : 'mdi-tag' }}</v-icon>
                </v-btn>
              </template>
              <span>
                {{ item.disponibilidad === 1 ? 'Desactivar la promocion de esta comida' : 'Volver a activar la promocion de esta comida' }}
              </span>
            </v-tooltip>
          </v-col>

              
              
              
                
            </template>

        </v-data-table>




        <div class="pa-4 text-center">
            <v-dialog transition="dialog-top-transition" v-model="AbrirDialog" max-width="600">
                <v-card prepend-icon="mdi-tag-faces" title="Aplicar Promoción">
                    <v-card-text>
                        <v-row dense>
                            <!-- Fila 1: Dos campos en la misma fila -->
                            <v-col cols="12" md="6" sm="6">
                                <v-text-field v-model="itemSeleccionado.precioOriginal"
                                    prepend-inner-icon="mdi-currency-usd" :readonly="true" label="Precio Original"
                                    required>
                                </v-text-field>
                            </v-col>

                            <v-col cols="12" md="6" sm="6">
                                <v-text-field
                                    hint="Descuento que será aplicado a sus productos, Si necesita 20% escribir 20.00"
                                    v-model="itemSeleccionado.descuentoAplicado" prepend-inner-icon="mdi-tag-outline"
                                    label="Descuento a Aplicar">
                                </v-text-field>
                            </v-col>

                            <!-- Fila 2: Dos campos en una nueva fila -->
                            <v-col cols="12" md="6" sm="6">
                                <v-text-field label="Precio Con Descuento" persistent-hint required
                                    prepend-inner-icon="mdi-cash" :value="precioDescuento"
                                    v-model="itemSeleccionado.precioDescuento" :readonly="true">
                                </v-text-field>
                            </v-col>


                            <v-col cols="12" md="6" sm="6">
                                <v-text-field label="Dinero Descontado" persistent-hint required
                                    prepend-inner-icon="mdi-cash" v-model="dineroAhorrado" :value="dineroAhorrado"
                                    :readonly="true">
                                </v-text-field>
                            </v-col>
                        </v-row>


                    </v-card-text>

                    <v-divider></v-divider>

                    <v-card-actions>
                        <v-spacer></v-spacer>

                        <v-btn text="Cerrar" variant="outlined" @click="AbrirDialog = false"></v-btn>

                        <v-btn color="primary" text="Aplicar Descuento" variant="outlined"
                            @click="AplicarDescuento"></v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </div>

    </v-card>
</template>

<script>
import Swal from 'sweetalert2';

export default {

    data: () => {
        return {

            dineroDescontado: null,
            itemSeleccionado: null,
            AbrirDialog: false,
            search: '',
            comidas: [],
            datosOrdenados: [],
            headers: [
                { title: 'Estado de la Promocion', key: 'estado' },
                { title: 'Nombre', key: 'nombre' },
                { title: 'Precio Original', key: 'precioOriginal' },
                { title: 'Precio con Descuento ', key: 'precioDescuento', sortable: false },
                { title: 'Descuento Aplicado ', key: 'descuentoAplicado', sortable: false },
                { title: 'Inicio de la promocion', key: 'fechaInicio', sortable: false },
                { title: 'Fin de la Promocion ', key: 'fechaFinal', sortable: false },
                { title: 'Acciones', key: 'actions', sortable: false },
            ]
        }
    },
    methods: {


        async CambiarEstado(item){

          item.estado = item.estado === 1 ? 0 :1
          const response = await this.axios.put(`/admin/rest/comida/${item.id}/promocion/estado`,{
            estado : item.estado
          })

        },


        async AplicarDescuento() {
            try {
                const response = await this.axios.put(`/admin/rest/comida/${this.itemSeleccionado.id}/promocion/anadir`, {
                    estado: true,
                    descuento: this.itemSeleccionado.descuentoAplicado,
                    precioDescuento: this.itemSeleccionado.precioDescuento
                })

                this.obtenerPromociones();
                this.AbrirDialog = false
                Swal.fire({
                    toast: true,
                    background: 'black',
                    position: 'top-end',
                    icon: 'success',
                    title: 'Se activado la promocion para la comida',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'my-popup',
                        timerProgressBar: 'my-progress-bar'
                    }
                });
            } catch (error) {

            }

        },



        seleccionaritems(item) {

            this.itemSeleccionado = { ...item }
            this.AbrirDialog = true;

        },

        async obtenerPromociones() {
            try {
                const response = await this.axios.get('/admin/rest/comida/promocion/listar');

                this.comidas = response.data.comidas;
                this.ordernarDatos();
            } catch (error) {

                console.error("Ocurrió un error al realizar la solicitud:", error.message);
            }
        },



        ordernarDatos() {
            this.datosOrdenados = []
            this.comidas.forEach(categoria => {
                categoria.comidas.forEach(comida => {
                    this.datosOrdenados.push({
                        id: comida.id,
                        estado: comida.estadoPromocion,
                        nombre: comida.nombre,
                        precioOriginal: comida.precioOriginal,
                        precioDescuento: comida.precioDescuento,
                        descuentoAplicado: comida.descuento,
                        fechaInicio: comida.fechaInicio || 'Sin fecha establecida',
                        fechaFinal: comida.fechaFin || 'Sin fecha  establecida',
                        categoria: categoria.categoria // Aquí añadimos la categoría
                    });
                });
            });

        }
    },


    computed: {
        precioDescuento() {
            const precioOriginal = parseFloat(this.itemSeleccionado?.precioOriginal) || 0;
            const descuentoAplicado = parseFloat(this.itemSeleccionado?.descuentoAplicado) || 0;

            if (precioOriginal && descuentoAplicado) {
                const precioConDescuento = precioOriginal - (precioOriginal * descuentoAplicado) / 100;
                this.itemSeleccionado.precioDescuento = precioConDescuento;
                return precioConDescuento;
            }
            return null;
        },


        dineroAhorrado() {
            const precioOriginal = parseFloat(this.itemSeleccionado?.precioOriginal) || 0;
            const descuentoAplicado = parseFloat(this.itemSeleccionado?.descuentoAplicado) || 0;

            if (precioOriginal && descuentoAplicado) {
                return (precioOriginal * descuentoAplicado) / 100;
            }
            return null;
        },
    },




    mounted() {
        this.obtenerPromociones();

    }
}
</script>
