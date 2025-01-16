<template>

    <v-sheet class="header" height="75px">
        <h3 class="header-title">NOOKEATS</h3>
    </v-sheet>


    <!-- Card Principal -->
    <v-card class="mx-auto" style="width: 100%;">



        <!-- Tarjeta de fondo -->
        <v-sheet class="background-img" cover>
            <v-img :src="perfil[0]?.imgBanner" cover height="100%" width="100%"></v-img>
        </v-sheet>

        <!-- Tarjeta flotante -->
        <v-sheet class="info-rest" elevation="2">
            <v-card-text>
                <h4 style="margin-top: 17px;margin-left: 1%;">{{ perfil[0]?.nombre }}</h4>
                <h6 style="margin-top: 17px;margin-left: 1%;">{{ perfil[0]?.descripcion }}</h6>
            </v-card-text>
            <v-btn color="red" @click="abrirDialog = true" append-icon="mdi-arrow-right" class="btn-rest" size="regular"
                icon-size="24px">
                Información
            </v-btn>
        </v-sheet>


        <v-sheet class="img-container">
            <v-img :src="perfil[0]?.imgPerfil"></v-img>
        </v-sheet>





        <!-- Contenido del Card -->
        <v-card-text style="margin-top: 2%;">
            <v-row>
                <!-- Sección Izquierda: Lista de Comidas -->
                <v-col cols="9">
                    <v-list>
                        <v-list-item v-for="(categoria, index) in perfil[0]?.categorias" :key="index">
                            <v-row class="d-flex justify-start">
                                <v-col cols="12">
                                    <v-list-item-title class="font-weight-bold">{{ categoria.nombreCategoria
                                        }}</v-list-item-title>
                                </v-col>
                            </v-row>

                            <!-- Lista de Productos dentro de cada Categoría -->
                            <v-row class="d-grid"
                                style="grid-template-columns: repeat(auto-fiLL, minmax(315px, 1fr)); gap: 20px;">
                                <v-col v-for="(comida, index) in categoria.comidas" :key="index">
                                    <!-- Card -->
                                    <v-card class="card-food" outlined @click="detallesProducto(comida)">
                                        <!-- Primera sección: Nombre y Imagen -->
                                        <v-row no-gutters>
                                            <v-col cols="7">
                                                <v-card-title class="title-card">
                                                    {{ comida.nombreComida }}
                                                </v-card-title>
                                                <p class="description-card">
                                                    {{ comida.descripcionComida }}
                                                </p>
                                            </v-col>

                                            <v-col cols="5" class="image-container">
                                                <v-img :src="comida.imagenRuta" alt="Imagen del platillo"
                                                    class="image-card" contain />
                                            </v-col>

                                        </v-row>
                                        <!-- Tercera sección: Precio -->
                                        <v-card-actions>
                                            <v-row class="d-flex justify-content-end">
                                                <v-col cols="auto" class="text-right">
                                                    <template v-if="comida.promocionActiva">
                                                        <div>
                                                            <!-- Precio con descuento -->
                                                            <span class="original-price">${{ comida.precio }}</span>
                                                            <span class="discounted-price">${{ comida.precioDescuento
                                                                }}</span>
                                                        </div>
                                                    </template>
                                                    <template v-else>
                                                        <!-- Precio original sin descuento -->
                                                        <span class="original-price-card">${{ comida.precio }}</span>
                                                    </template>
                                                </v-col>
                                            </v-row>
                                        </v-card-actions>
                                    </v-card>
                                </v-col>
                            </v-row>

                        </v-list-item>
                    </v-list>
                </v-col>


                <!-- Sección Derecha: Carrito de Pedido -->
                <v-col cols="3">
                    <v-card class="p-1 custom-card-order">
                        <v-card-text>
                            <!-- Si hay comidas en el carrito -->
                            <template v-if="comidas.length > 0">
                                <v-card-title class="card-title-order  d-flex justify-between align-center">
                                    <span>Mi Pedido</span>
                                    <v-btn class="ml-auto" v-if="editarListaPedido == false" color="green"
                                        @click="toggleEdit">Editar</v-btn>
                                    <v-btn class="ml-auto" v-else-if="editarListaPedido === true" color="green"
                                        @click="toggleEdit">Guardar</v-btn>
                                </v-card-title>
                                <br>
                                <!-- Listado de productos en el carrito -->
                                <div v-for="(pedido, index) in comidas" :key="index">
                                    <div class="d-flex justify-between align-center">
                                        <v-checkbox v-if="editarListaPedido" v-model="pedido.selected"
                                            class="mr-4"></v-checkbox>
                                        <p class="mr-2">{{ pedido.cantidad }}</p>
                                        <p class="flex-grow-1">{{ pedido.nombre }}</p>
                                        <p>
                                            ${{ pedido.precioFinalCantidadComida }}
                                        </p>
                                    </div>
                                </div>
                                <div class="divider"></div>
                                <div class="total-price">
                                    <h4>Total:</h4>
                                    <h4>${{ precioFinalPedido }}</h4>
                                </div>
                                <div class="divider"></div>



                                <div class="footer-card-order">
                                    <!-- Botones de acción en el carrito -->
                                    <v-btn v-if="editarListaPedido" class="my-2" color="primary"
                                        @click="eliminarDatosListaPedido">Eliminar Seleccionados</v-btn>
                                    <v-btn v-else class="my-2" color="primary" @click="enviarPedido" block>Realizar
                                        Pedido</v-btn>
                                    <v-row v-if="editarListaPedido" class="d-flex justify-space-between align-center"
                                        style="padding: 16px;">
                                        <v-col cols="8">
                                            <span class="edit-quantity-title">Editar Cantidades</span>
                                        </v-col>
                                        <v-col cols="4">
                                            <div class="btn-group">
                                                <button class="btn btn-dark"
                                                    @click="decrementarSeleccionados">-</button>
                                                <button class="btn btn-dark"
                                                    @click="incrementarSeleccionados">+</button>
                                            </div>
                                        </v-col>
                                    </v-row>
                                </div>
                            </template>

                            <!-- Mensaje si no hay comidas en el carrito -->
                            <template v-else>
                                <v-card-title class="card-title-order d-flex justify-between align-center"
                                    style="padding-left: 32%;">
                                    <span>Mi Pedido</span>
                                </v-card-title>
                                <div class="empty-order-message">
                                    <v-icon class="mb-1" color="grey darken-1" size="100">mdi-magnify</v-icon>
                                    <h5>¡Está vacío! </h5>
                                </div>
                            </template>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-card-text>
    </v-card>

    <!-- Dialog de información de restaurante -->

    <v-dialog v-model="abrirDialog" max-width="450px">
        <v-card>
            <v-card-title class="dialog-title">Información del
                Restaurante</v-card-title>
            <v-card-text>
                <!-- Información del restaurante: dirección y teléfonos -->
                <div v-if="perfil[0]">
                    <v-row>
                        <v-col>
                            <h4 class="card-section-header">Dirección</h4>
                            <p class="direccion">{{ perfil[0].direccion }}</p>
                        </v-col>
                    </v-row>
                </div>

                <v-divider color="#B013D8" class="border-opacity-100" thickness="2px"></v-divider>

                <div v-if="perfil[0]">
                    <v-row>
                        <v-col>
                            <h4 class="card-section-header">Contacto</h4>
                            <p class="telefono">Teléfono: {{ perfil[0].telefono }}</p>
                            <p class="telefono" v-if="perfil[0].telefonoSecundario">Teléfono Secundario: {{
                                perfil[0].telefonoSecundario }}</p>

                        </v-col>
                    </v-row>
                </div>

                <v-divider color="#B013D8" class="border-opacity-100" thickness="2px"></v-divider>


                <!-- Horarios -->
                <div v-if="perfil[0]?.horarios?.length > 0">
                    <v-list>

                        <h4 class="card-section-header">Horarios</h4>
                        <v-list-item v-for="(horario, index) in perfil[0].horarios" :key="index">
                            <v-row class="d-flex justify-space-between align-center">
                                <v-col cols="6">
                                    <v-list-item-title>{{ horario.dia
                                        }}</v-list-item-title>
                                </v-col>
                                <v-col cols="6" class="text-right">
                                    <v-list-item-title>{{ horario.entrada }} a {{
                                        horario.salida }} hs</v-list-item-title>
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
                    <!-- Imagen del producto -->
                    <v-list-item>
                        <v-row class="image-container">
                            <v-col cols="10" class="d-flex justify-center image-column">
                                <v-img :src="comidaSeleccionada.imagenRuta" alt="Imagen de producto" height="120px"
                                    contain />
                            </v-col>
                        </v-row>
                    </v-list-item>

                    <!-- Nombre y descripción del producto -->
                    <v-list-item>
                        <v-row class="d-flex justify-space-between align-center">
                            <v-col cols="9">
                                <v-list-item-title class="product-name">{{ comidaSeleccionada.nombreComida
                                    }}</v-list-item-title>
                            </v-col>
                        </v-row>
                    </v-list-item>

                    <!-- descripcion del producto -->
                    <v-list-item>
                        <v-row class="d-flex justify-space-between align-center">
                            <v-col cols="9">
                                <v-list-item-subtitle class="product-description">{{
                                    comidaSeleccionada.descripcionComida
                                    }}</v-list-item-subtitle>
                            </v-col>
                            <v-col cols="1.5" class="text-right">
                                <v-list-item-title class="price" v-if="comidaSeleccionada.promocionActiva == true">
                                    ${{ comidaSeleccionada.precioDescuento }}
                                </v-list-item-title>
                                <v-list-item-title class="price" v-else>
                                    ${{ comidaSeleccionada.precio }}
                                </v-list-item-title>
                            </v-col>
                        </v-row>
                    </v-list-item>
                </v-list>
            </v-card-text>

            <v-divider></v-divider>

            <!-- Sección de cantidad y notas adicionales -->
            <v-card-text class="select-amount-product">
                <v-row gap="8">
                    <v-col cols="12">
                        <v-list-item variant="outlined" elevation="4" class="custom-list-item">
                            <v-row class="d-flex justify-space-between align-center custom-row">
                                <v-col cols="6">
                                    <span class="quantity-label">Cantidad a llevar</span>
                                </v-col>
                                <v-col cols="4">
                                    <div class="btn-group">
                                        <button class="btn btn-dark" @click="decremento">-</button>
                                        <button class="btn btn-dark">{{ counter }}</button>
                                        <button class="btn btn-dark" @click="incremento">+</button>
                                    </div>
                                </v-col>
                            </v-row>
                        </v-list-item>
                    </v-col>

                    <v-col cols="12">
                        <span class="additional-info-label">¿Algo más que debamos saber?</span>
                        <v-list-item-subtitle class="additional-info-text">Escribe aquí tus notas, como preferencias
                            de
                            comida o alergías</v-list-item-subtitle>
                        <v-list-item>
                            <v-textarea v-model="notaUsuario" label="Añade cualquier detalle importante sobre tu pedido"
                                variant="filled" class="custom-textarea"></v-textarea>
                        </v-list-item>
                    </v-col>
                </v-row>
            </v-card-text>

            <!-- Botón de agregar al carrito -->
            <v-card-actions class="d-flex justify-center">
                <v-btn block color="primary" large @click="AñadirCarrrito">Agregar Orden - ${{ precioFinalComida
                    }}</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>


    <!-- Dialog de login  y register -->
    <v-dialog v-model="dialogLogin" width="auto">
        <v-card max-width="450" prepend-icon="mdi-alert" text="¡No te preocupes, será rápido!"
            title="¿Ansioso por comer?" subText="">

            <v-card-actions>
                <v-btn @click="redirectToLogin" style="background-color: #00BFFF; color: white; cursor: pointer;">
                    Iniciar Sesión
                </v-btn>

                <v-btn @click="redirectToRegister" style="background-color: #00BFFF;color:white;">Registrarse</v-btn>
            </v-card-actions>


            <template v-slot:actions>
                <v-btn class="ms-auto" text="Cerrar" @click="dialogLogin = false"></v-btn>
            </template>
        </v-card>
    </v-dialog>


</template>


<script>
import Swal from 'sweetalert2';


export default {

    props: ['restauranteId'],

    data: () => {
        return {


            copiaPrecioFinalComida: 0,
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
            dialogLogin: false,
        }
    },

    methods: {

        redirectToLogin() {
            window.location.href = '/login'; // Esto redirige a la página de login
        },

        redirectToRegister() {
            window.location.href = '/register'; // Esto redirige a la página de  registro de laravel
        },

        async enviarPedido() {

            try {

                const response = await this.axios.post('usuario/pedido', {
                    total: this.precioFinalPedido,
                    metodoPago: 'P',
                    pedido_comida: this.comidas,
                });

                this.comidas = [];
                this.precioFinalPedido = 0;


                this.$swal({
            title: 'Pedido Realizado',
            text: 'Tu pedido llegara pronto',
            icon: 'success',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000, 
            timerProgressBar: true, 
        });

            } catch (error) {
                if (error.response && error.response.status === 401) {

                    this.dialogLogin = true;
                }
                else {
           
                    this.$swal({
            title: 'Error',
            text: 'Hubo un problema al realizar el pedido.',
            icon: 'error',
            toast: true,
            position: 'top-end', 
            showConfirmButton: false,
            timer: 3000, 
            timerProgressBar: true, 
            customClass: {
                popup: 'bg-danger' 
            }
        });
                }
            }
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
            this.copiaPrecioFinalComida = this.precioFinalComida;
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
                precioFinalCantidadComida: this.copiaPrecioFinalComida,
                nombre: this.comidaSeleccionada.nombreComida,
                cantidad: this.counter,
                nota: this.notaUsuario,
                precioOriginal: parseFloat(parseFloat(this.comidaSeleccionada.precio).toFixed(2)),
                precioDescuento: parseFloat(parseFloat(this.comidaSeleccionada.precioDescuento).toFixed(2)),
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
                const response = await this.axios.get(`/${this.restauranteId}/menu/`);
                this.perfil = response.data.perfil;
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
/*estilo para la seccion  del header */
.header {
    position: sticky;
    top: 0;
    z-index: 10;
    border-bottom: 1px solid #5D0B7D;
}

/*Estilo para el titulo del header */
.header-title {
    font-size: 25;
    color: #5D0B7D;
    font-weight: bold;
    margin: 0;
    padding: 10px;
    padding-left: 2%;
    transform: translateY(15px);
}

/*Estilo para la imagen de fondo */
.background-img {
    height: 120px;
    position: relative;
    z-index: 0;
}

/*estilo para los datos del perfil */
.info-rest {
    position: absolute;
    top: 75px;
    /* Para que desborde parcialmente del fondo */
    left: 0;
    z-index: 1;
    background-color: white;
    width: 71%;
    border: 1px solid #B013D8;
    border-top-right-radius: 10px;
    border-bottom-right-radius: 10px;
    display: flex;
}


/*estilo para el boton de inforamcion */
.btn-rest {
    position: absolute;
    right: 15px;
    top: 25px;
    border-radius: 24px;
}


.img-container {
    border: 1px solid #B013D8;
    height: 80px;
    width: 80px;
    z-index: 3;
    position: relative;
    top: -100px;
    left: 25px;
}


/* Estilos para las tarjetas */
.card-food {

    border: 1px solid #B013D8;
    margin-left: 20px;
    margin-right: 20px;
    border-top-right-radius: 20px;
    border-bottom-right-radius: 20px;
}

.custom-card-body {
    padding: 10px;
}

.text-section {
    max-width: 85%;
    padding: 5px;
    margin-bottom: 8px;
}


/* Estilos para el card de pedido */
.custom-card-order {
    position: fixed;
    right: 0;
    top: 30.3%;
    max-height: 450px;
    width: 23%;
    background-color: white;
    z-index: 1000;
    overflow-y: auto;
    border: 1px solid #B013D8;
}


/*Grid en la que se encuentra las card */
.d-grid {
    display: grid;
    gap: 16px;
}

/*card de comida */
.card-food {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
}

/*titulo de la card de comida */
.title-card {
    white-space: normal;
    /* Permite que el texto se ajuste en varias líneas */
    overflow: visible;
    /* Asegura que no se oculte el contenido */
    text-overflow: unset;
    /* Desactiva el uso de elipsis (...) */
    line-height: 1.4;
    /* Ajusta la altura de línea para un espaciado adecuado */
    padding-top: 4.5%;
    /* Elimina cualquier relleno adicional */
    padding-left: 6.5%;
    font-size: 15px;
    font-weight: bold;
}


/*descripcion de la card de comida */
.description-card {
    padding-left: 6.5%;
    /* Espaciado a la izquierda */
    margin: 0;
    /* Elimina márgenes adicionales */
    font-size: 14px;
    /* Tamaño de fuente adecuado para una descripción */
    line-height: 1.6;
    /* Mejora la legibilidad con un espaciado entre líneas */
    color: #6b6b6b;
    /* Color de texto suave */
    text-align: justify;
    /* Justifica el texto*/
    display: -webkit-box;
    /* Flexbox optimizado para truncar */
    -webkit-line-clamp: 3;
    /* Número de líneas visibles antes del truncado */
    -webkit-box-orient: vertical;
    overflow: hidden;
    /* Oculta el contenido que desborda */
}


.image-container {
    display: flex;
    justify-content: center;
    /* Centra horizontalmente (si es necesario) */
    align-items: center;
    /* Centra verticalmente */
    height: 150px;
    /* Asegura que el contenedor use toda la altura disponible */

}



.image-card {
    margin-top: -3px;
    /* Baja la imagen */
    height: 100px;
    max-width: 160px;
    width: 100%;

}


/* Estilos para el precio del producto */
.price {
    font-size: 19px;
    font-weight: bolder;
    padding-right: 25px;
}

/* Estilos para el mensaje de orden vacía */
.empty-order-message {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    height: 100%;
}

/* Estilos para el título de editar cantidades */
.edit-quantity-title {
    font-size: 18px;
    font-weight: bolder;
}

/* Estilos para los botones de incremento y decremento */
.btn-group button {
    font-weight: bolder;
    color: white;
}

/* Estilos para el título del dialog */
.dialog-title {
    text-align: center;
    font-size: 1.25rem;
    font-weight: bold;
}

/* Estilos generales para los horarios dentro del dialog */
.v-list-item-title {
    font-size: 1rem;
    font-weight: 500;
}

/* Alineación de los elementos dentro del row */
.d-flex.justify-space-between.align-center {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Alineación de los horarios a la derecha */
.text-right {
    text-align: right;
}

/* Estilos para los botones dentro de los actions */
.v-btn {
    padding: 8px 16px;
}

/* Columna que contiene la imagen */
.image-column {
    padding-left: 10%;
    display: flex;
    justify-content: center;
}

/* Estilos para el nombre del producto */
.product-name {
    font-size: 20px;
    font-weight: bolder;
    white-space: normal;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

/* Estilos para la descripción del producto */
.product-description {
    font-size: 16px;
    color: #333333;
    white-space: normal;
    word-wrap: break-word;
    overflow-wrap: break-word;
    overflow: visible;
    display: block;
}

/* Alineación a la derecha de los precios */
.text-right {
    text-align: right;
}

/*Estilo para precio oringal en descuento*/
.original-price {
    text-decoration: line-through;
    color: #999;
    font-size: 1em;
    margin-right: 8px;
}

/*precio de valor en descuento */
.discounted-price {
    color: #4CAF50;
    font-weight: bold;
    font-size: 1.4em;
    padding-right: 1.2em;
}


/*precio original cuando no hay descuento */
.original-price-card {
    font-size: 1.4em;
    font-weight: bold;
    color: #100038;
    padding-right: 2.5em;
}

/* Contenedor del texto de la tarjeta */
.custom-card-text {
    max-height: 300px;
    overflow-y: auto;
}

/* Estilo de la cantidadq]]q de producto a elegir*/
.select-amount-product {
    border-color: #B013D8;
}

/* Estilo para la nota del producto*/
.custom-textarea {
    border: 2px solid #B013D8;
    border-radius: 4px;
}

/* Fila con espaciado personalizado */
.custom-row {
    padding: 16px;
}

/* Etiqueta de cantidad con negrita */
.quantity-label {
    font-size: 18px;
    font-weight: bolder;
}

/* Estilo de los botones */
.btn-dark-action {
    font-weight: bolder;
    color: white;
}

/* Estilo para el texto adicional */
.additional-info-label {
    font-size: 18px;
    font-weight: bold;
}

/* Estilo para el subtítulo de la información adicional */
.additional-info-text {
    font-size: 15px;
    color: black;
    font-weight: bolder;
}



/*Estilo para el header del carrito */
.card-title-order {
    position: sticky;
    top: -8px;
    background-color: white;
    border-bottom: 2px solid #B013D8;
    z-index: 5;
}

/*estilo para el footer del carrito */
.footer-card-order {
    position: sticky;
    bottom: -5px;

    background-color: white;
    border-top: 2px solid #B013D8;
}

/*estilo para el header de la secciones en la card de info*/
.card-section-header {
    font-size: 20px;
    font-weight: bold;
}

/* estilo para el contenido de la direccion */
.direccion {
    font-size: 15px;
    font-weight: 100;
}

/*estilo para la seccion telefonica */
.telefono {
    font-size: 15px;
    font-weight: 100;
}
</style>