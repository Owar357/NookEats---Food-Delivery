<template>
    <v-card class="mx-auto" style="width: 100%;">
        <!-- Cabecera del Card -->
        <template v-slot:title ">
            <v-row class=" mt-3 mb-8" :style="{
                backgroundImage: 'url(' + perfil[0]?.imgPerfil + ')',
                backgroundSize: 'cover',
                backgroundPosition: 'center',
            }">
            <v-col cols="12" class="ml-12 mt-7">
                <span class="font-weight-black">{{ perfil[0]?.nombre }}</span>
                <div class="mt-2 d-flex cursor-pointer" @click="abrirDialog = true">
                    <h6 class="d-inline">Informacíon del establecimiento</h6>
                    <v-icon size="22">mdi-menu-right</v-icon>
                </div>
            </v-col>
            </v-row>
        </template>


        <!-- Contenido del Card -->
        <v-card-text>
            <v-row>
                <!-- Sección Izquierda -->
                <v-col cols="9">
                    <v-list>
                        <v-list-item v-for="(categoria, index) in perfil[0]?.categorias" :key="index">
                            <v-row class="d-flex justify-start">
                                <v-col cols="12">
                                    <v-list-item-title class="font-weight-bold">{{ categoria.nombreCategoria
                                        }}</v-list-item-title>
                                </v-col>
                            </v-row>

                            <v-row class="d-flex justify-start">
                                <v-col cols="6" md="5" v-for="(comida, index) in categoria.comidas" :key="index"
                                    no-gutters>
                                    <v-card class="custom-card" variant="outlined" @click="detallesProducto(comida)">
                                        <v-row no-gutters>
                                            <v-col cols="8">
                                                <v-card-text class="custom-card-body">
                                                    <div class="text-section">
                                                        <h5 class="card-title">{{ comida.nombreComida }}</h5>
                                                    </div>
                                                    <div class="text-section">
                                                        <p class="card-description">{{ comida.descripcionComida }}</p>
                                                    </div>

                                                    <div class="price-section">

                                                        <span class="price original-price"
                                                            :class="{ 'discounted': comida.promocionActiva }"
                                                            style="display: block;">
                                                            ${{ comida.precio }}
                                                        </span>


                                                        <span class="price discounted-price"
                                                            v-if="comida.promocionActiva" style="display: block;">
                                                            ${{ comida.precioDescuento }}
                                                        </span>
                                                    </div>


                                                </v-card-text>
                                            </v-col>
                                            <v-col cols="4" class="p-0">
                                                <v-img :src="comida.imagenRuta" alt="Card image" class="custom-img"
                                                    height="200px" contain />
                                            </v-col>
                                        </v-row>
                                    </v-card>
                                </v-col>
                            </v-row>
                        </v-list-item>
                    </v-list>
                </v-col>

                <!-- Sección Derecha -->
                <v-col cols="3">
                    <v-card class="p-1" variant="outlined"
                        style="position: fixed; right: 0; top: 23.3%; max-height: 500px; width: 23%; background-color: white; z-index: 1000; overflow-y: auto;">
                        <v-card-text>
                            <template v-if="comidas.length > 0">
                                <v-card-title class="text-h6 font-weight-bold d-flex justify-between align-center"
                                    style="position: sticky; top: -5px; background-color: white; z-index: 10; width: 100%; padding: 10px 0; border-bottom: 2px solid black;">
                                    <span>Mi Pedido</span>
                                    <v-btn class="ml-auto" v-if="editarListaPedido == false" color="green"
                                        @click="toggleEdit">Editar</v-btn>
                                    <v-btn class="ml-auto" v-else-if="editarListaPedido === true" color="green"
                                        @click="toggleEdit">Guardar</v-btn>
                                </v-card-title>
                                <br>
                                <div v-for="(pedido, index) in comidas" :key="index">
                                    <div class="d-flex justify-between align-center">
                                        <!-- Mostrar el checkbox solo si estamos en modo de edición -->
                                        <v-checkbox v-if="editarListaPedido" v-model="pedido.selected"
                                            class="mr-4"></v-checkbox>
                                        <p class="mr-2">{{ pedido.cantidad }}</p>
                                        <p class="flex-grow-1">{{ pedido.nombre }}</p>
                                        <p>
                                            ${{ pedido.promocionActiva === 1 ? pedido.precioDescuento : pedido.precio }}
                                        </p>

                                    </div>
                                </div>
                                <div class="my-2" style="width: 100%; height: 1px; background-color: black"></div>
                                <div style="display: flex; justify-content: space-between;">
                                    <h4>Total:</h4>
                                    <h4>${{ precioFinalPedido }}</h4>
                                </div>
                                <div style="width: 100%; height: 1px; background-color: black"></div>


                                <v-btn v-if="editarListaPedido" class="my-2" color="primary"
                                    @click="eliminarDatosListaPedido">Eliminar Seleccionados</v-btn>
                                <v-btn v-else class="my-2" color="primary" @click="enviarPedido">Realizar Pedido</v-btn>
                                <v-row v-if="editarListaPedido" class="d-flex justify-space-between align-center"
                                    style="padding: 16px;">
                                    <v-col cols="8">
                                        <span style="font-size: 18px; font-weight: bolder;">Editar Cantidades</span>
                                    </v-col>
                                    <v-col cols="4">
                                        <div class="btn-group">
                                            <button class="btn btn-dark" @click="decrementarSeleccionados"
                                                style="font-weight: bolder;color:white;">-</button>
                                            <button class="btn btn-dark" @click="incrementarSeleccionados"
                                                style="font-weight: bolder;color:white;">+</button>
                                        </div>
                                    </v-col>
                                </v-row>
                            </template>

                            <!-- Mensaje si no hay comidas -->
                            <template v-else>
                                <v-card-title class="text-h6 font-weight-bold d-flex justify-between align-center"
                                    style="position: sticky; top: -19px; background-color: white; z-index: 10; width: 100%; padding: 10px 0; border-bottom: 2px solid black;">
                                    <span>Mi Pedido</span>
                                </v-card-title>
                                <div class="d-flex flex-column justify-center align-center text-center"
                                    style="height: 100%;">
                                    <v-icon class="mb-1" color="grey darken-1" size="100">mdi-cart-outline</v-icon>
                                    <h5>Tu orden está vacía</h5>
                                </div>
                            </template>
                        </v-card-text>
                    </v-card>

                </v-col>


            </v-row>
        </v-card-text>
    </v-card>

    <!-- Dialog de horarios -->
    <v-dialog v-model="abrirDialog" max-width="450px">
        <v-card>
            <v-card-title class="headline text-center">Información</v-card-title>
            <v-card-text>
                <div v-if="perfil[0]?.horarios?.length > 0">
                    <v-list>
                        <v-list-item v-for="(horario, index) in perfil[0].horarios" :key="index">
                            <v-row class="d-flex justify-space-between align-center">
                                <v-col cols="6">
                                    <v-list-item-title>{{ horario.dia }}</v-list-item-title>
                                </v-col>
                                <v-col cols="6" class="text-right">
                                    <v-list-item-title>{{ horario.entrada }} a {{ horario.salida }}
                                        hs</v-list-item-title>
                                </v-col>
                            </v-row>
                        </v-list-item>
                    </v-list>
                </div>
            </v-card-text>
            <v-card-actions>
                <v-btn color="primary" @click="abrirDialog = false">Cerrar</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <!-- Dialog de detalles del producto -->
    <v-dialog v-model="dialogPedido" max-width="510px">
        <v-card>
            <v-card-text class="pb-0">
                <v-list>
                    <v-list-item>
                        <v-row class="d-flex justify-center align-center" style="width: 100%; height: 140px;">
                            <v-col cols="10" class="d-flex justify-center" style="padding-left: 10%;">
                                <v-img :src="comidaSeleccionada.imagenRuta" alt="Imagen de producto" height="120px"
                                    contain />
                            </v-col>
                        </v-row>
                    </v-list-item>

                    <v-list-item>
                        <v-row class="d-flex justify-space-between align-center">
                            <v-col cols="9">
                                <v-list-item-title style="font-size:20px; font-weight: bolder;">{{
                                    comidaSeleccionada.nombreComida }}</v-list-item-title>
                            </v-col>
                        </v-row>
                    </v-list-item>

                    <v-list-item>
                        <v-row class="d-flex justify-space-between align-center">
                            <v-col cols="9">
                                <v-list-item-subtitle style="font-size: 16px; color:#333333">{{
                                    comidaSeleccionada.descripcionComida }}</v-list-item-subtitle>
                            </v-col>
                            <v-col cols="1.5" class="text-right">
                                <!-- Si la promoción está activa (promocionActiva es igual a 1), muestra el precio con descuento -->
                                <v-list-item-title style="font-size: 19px; font-weight: bolder;"
                                    v-if="comidaSeleccionada.promocionActiva == true">
                                    ${{ comidaSeleccionada.precioDescuento }}
                                </v-list-item-title>

                                <!-- Si la promoción no está activa, muestra el precio original -->
                                <v-list-item-title style="font-size: 19px; font-weight: bolder;" v-else>
                                    ${{ comidaSeleccionada.precio }}
                                </v-list-item-title>
                            </v-col>

                        </v-row>
                    </v-list-item>
                </v-list>
            </v-card-text>

            <v-divider></v-divider>

            <v-card-text style="max-height: 300px; overflow-y: auto;">
                <v-row gap="8">
                    <v-col cols="12">
                        <v-list-item variant="outlined" elevation="4" style="border-color: red;">
                            <v-row class="d-flex justify-space-between align-center" style="padding: 16px;">
                                <v-col cols="6">
                                    <span style="font-size: 18px; font-weight: bolder;">Cantidad a llevar</span>
                                </v-col>
                                <v-col cols="4">
                                    <div class="btn-group">
                                        <button class="btn btn-dark" @click="decremento"
                                            style="font-weight: bolder;color:white;">-</button>
                                        <button class="btn btn-dark" style="font-weight: bolder;color:white;">{{ counter
                                            }}</button>
                                        <button class="btn btn-dark" @click="incremento"
                                            style="font-weight: bolder;color:white;">+</button>
                                    </div>
                                </v-col>
                            </v-row>
                        </v-list-item>
                    </v-col>

                    <v-col cols="12">
                        <span style="font-size: 18px; font-weight: bold;">¿Algo más que debamos saber?</span>
                        <v-list-item-subtitle style="font-size: 15px; color: black; font-weight: bolder;">Escribe aquí
                            tus
                            notas, como preferencias de la comida o alergías</v-list-item-subtitle>
                        <v-list-item>
                            <v-textarea v-model="notaUsuario" label="Añade cualquier detalle importante sobre tu pedido"
                                variant="filled" style="border: 2px solid red; border-radius: 4px;"></v-textarea>
                        </v-list-item>
                    </v-col>
                </v-row>
            </v-card-text>

            <v-card-actions class="d-flex justify-center">
                <v-btn block color="primary" large @click="AñadirCarrrito">Agregar Orden - ${{ precioFinalComida
                    }}</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>


export default {
    data: () => {
        return {

            editarListaPedido: false,
            precioFinalPedido: 0,
            notaUsuario: " ",
            comidas: [],
            precioFinalComida: 0,
            counter: 1,
            comidaSeleccionada: {},  // Guarda la comida seleccionada
            dialogPedido: false,  // Controla la apertura del diálogo
            abrirDialog: false,  // Controla la apertura del diálogo de información
            perfil: [],  // Datos del perfil del restaurante
        }
    },

    methods: {

        enviarPedido() {

            console.log('datoas guardados en el carrito : ', this.comidas);

        },


        decrementarSeleccionados() {
            this.comidas.forEach(pedido => {
                if (pedido.selected && pedido.cantidad > 1) {
                    pedido.cantidad--;



                    const precioUnitarioActual = pedido.promocionActiva
                        ? pedido.precioDescuento
                        : pedido.precioUnitario;

                    pedido.precio = parseFloat((pedido.cantidad * precioUnitarioActual).toFixed(2)),

                        this.precioFinalPedido = parseFloat((this.precioFinalPedido - precioUnitarioActual).toFixed(2));

                }
            })

        },
        incrementarSeleccionados() {
            this.comidas.forEach(pedido => {
                if (pedido.selected) {

                    pedido.cantidad++;
                    const precioAntiguo = pedido.precio;

                    const precioUnitarioActual = pedido.promocionActiva
                        ? pedido.precioDescuento
                        : pedido.precioUnitario;

                    pedido.precio = parseFloat((pedido.cantidad * precioUnitarioActual).toFixed(2))

                    this.precioFinalPedido = parseFloat((this.precioFinalPedido - precioAntiguo + pedido.precio).toFixed(2));

                }
            })

        },

        eliminarDatosListaPedido() {
            // Filtrar los pedidos eliminando los seleccionados
            this.comidas = this.comidas.filter(pedido => !pedido.selected);

            // Si no hay pedidos restantes, desactivar el modo edición
            if (this.comidas.length === 0) {
                this.editarListaPedido = false;
                this.precioFinalPedido = 0;

            } else {
                this.precioFinalPedido = parseFloat(this.comidas.reduce((total, pedido) => total + pedido.precio, 0)).toFixed(2);
            }
        },

        toggleEdit() {
            this.editarListaPedido = !this.editarListaPedido; // Cambia el estado de edición
        },



        calcularTotalPagar() {
            const precio = this.comidaSeleccionada.promocionActiva ? this.comidaSeleccionada.precioDescuento : this.comidaSeleccionada.precio;
            this.precioFinalComida = parseFloat((precio * this.counter).toFixed(2));
        },

        decremento() {
            if (this.counter > 1) {
                this.counter--;  // Disminuye el contador
                this.calcularTotalPagar();  // Recalcula el precio
            }
        },

        incremento() {
            this.counter++;
            this.calcularTotalPagar();
        },

        async AñadirCarrrito() {
            this.dialogPedido = false;

            const precioDecimal = parseFloat(this.precioFinalComida.toFixed(2));  // Redondea antes de agregar
            this.comidas.push({
                nombre: this.comidaSeleccionada.nombreComida,
                precio: precioDecimal,
                cantidad: this.counter,
                nota: this.notaUsuario,
                precioUnitario: parseFloat(this.comidaSeleccionada.precio).toFixed(2),
                selected: false,
                precioDescuento: parseFloat(this.comidaSeleccionada.precioDescuento).toFixed(2),
                promocionActiva: this.comidaSeleccionada.promocionActiva
            });

            this.precioFinalPedido = parseFloat((this.precioFinalPedido + precioDecimal).toFixed(2));  // Asegúrate de redondear al agregar al total

            this.counter = 1;
            this.notaUsuario = " ";

            console.log('datos de comida seleccionada:', this.comidaSeleccionada);
        },

        detallesProducto(item) {
            this.comidaSeleccionada = item;  // Asigna el producto seleccionado
            this.calcularTotalPagar();
            this.dialogPedido = true;  // Muestra el diálogo de detalles

        },

        async ListarPerfil() {
            try {
                const response = await this.axios.get(`/${1}/menu/`);
                this.perfil = response.data.perfil;
                console.log('Los datos guardados son: ', this.perfil);
            } catch (error) {
                console.error("Error al cargar el perfil del restaurante:", error);
            }
        }
    },
    mounted() {
        this.ListarPerfil();  // Carga los datos cuando el componente se monta
    }
}
</script>


<style scoped>


.v-row.mt-3.mb-8 {
    min-height: 150px; /* Ajusta según lo que necesites */
    margin-top: 0 !important;
    padding-top: 0 !important;
}

.custom-card {
    display: flex;
    flex-direction: column;
    max-width: 30em;
    height: 100%;

}

.custom-img {
    width: 100%;
    /* Asegura que ocupe todo el ancho del contenedor */
    height: 200px;
    /* Establece la altura fija de la imagen */
    object-fit: cover;
    /* Asegura que la imagen se recorte adecuadamente */
    border-radius: 0.7em;
    /* Bordes redondeados */
}

.custom-card-body {
    padding: 10px;
}

.text-section {
    max-width: 85%;

    padding: 5px;
    margin-bottom: 8px;
}

.card-title {
    font-size: 16px;
    font-weight: bold;
}

.card-description {
    font-size: 14px;
    color: #555;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.5em;
    max-height: 3em;
}

.price-section {
    align-self: flex-end;
}



main.py-4 {
    min-height: 100vh;
    /* Asegura que ocupe toda la altura */
}


.price-section {
    margin-top: 10px;
    /* Ajusta según sea necesario */
}

.original-price {
    font-size: 1.3rem;
    color: blue;
    /* Gris neutro */
    text-decoration: none;
    /* Sin tachar por defecto */
    font-weight: bold;
}

.original-price.discounted {
    text-decoration: line-through;
    /* Tachado si hay promoción */
    color: #888;
    /* Más claro si está tachado */
    font-size: 0.9rem;
    /* Tamaño reducido */
}

.discounted-price {
    font-size: 1.3rem;
    /* Más grande para destacar */
    color: red;
    /* Rojo para precios con descuento */
    font-weight: bold;
    /* Destacado */
}
</style>
