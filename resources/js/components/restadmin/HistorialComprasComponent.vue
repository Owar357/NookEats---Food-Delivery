<template>
  <v-card-text class="py-3" style="background-color: #f4f6f9; border-radius: 8px;">
    <v-row class="py-2" align="center" justify="start" no-gutters>

      <!-- Buscador de historial de pedidos -->
      <v-col cols="3" class="pr-2">
        <v-text-field v-model="search" label="Buscar Codigo de Pedido" prepend-inner-icon="mdi-magnify" variant="solo" dense
          placeholder="Introduce tu búsqueda"></v-text-field>
      </v-col>

      <!-- Filtro de fecha de inicio -->
      <v-col cols="3" class="pr-2">
        <v-text-field v-model="fechaInicio" label="Filtrar Fecha de Inicio" prepend-inner-icon="mdi-calendar" readonly
          variant="solo" dense @click="inicioFechaMenu = !inicioFechaMenu"
          :value="fechaInicioFormateada"></v-text-field>
        <v-menu v-model="inicioFechaMenu" :close-on-content-click="false" offset-y>
          <v-date-picker v-model="fechaInicio" @input="inicioFechaMenu = false"></v-date-picker>
        </v-menu>
      </v-col>

      <!-- Filtro de fecha de fin -->
      <v-col cols="3">
        <v-text-field v-model="fechafinal" label="Filtrar Fecha de Fin" prepend-inner-icon="mdi-calendar" readonly
          variant="solo" dense @click="finalFechaMenu = !finalFechaMenu" :value="fechaFinalFormateada"></v-text-field>
        <v-menu v-model="finalFechaMenu" :close-on-content-click="false" offset-y>
          <v-date-picker v-model="fechafinal" @input="finalFechaMenu = false"></v-date-picker>
        </v-menu>
      </v-col>

      <!-- Botón de filtrado -->
      <div class="text-center mb-5 ml-3">


        <v-btn color="primary" height="55" min-width="100" append-icon="mdi-plus">
          Filtrar por
        </v-btn>

        <v-menu activator="parent" transition="slide-x-transition" location="bottom">
          <v-list>
            <!-- Menú desplegable para opciones de filtrado -->
            <v-list-item v-for="(item, index) in items" :key="index" link>
              <v-list-item-title>{{ item.title }}
                <v-icon icon="mdi-menu-right" size="x-small"></v-icon>
              </v-list-item-title>

              <!-- Sub-menú desplegable para categorías de filtro -->
              <v-menu :open-on-focus="false" activator="parent" open-on-hover submenu>
                <v-list>
                  <v-list-item v-for="(subItem, subindex) in item.subMenu" :key="subindex" link
                    @click="seleccionarSubMenu(subItem.value)">
                    <v-list-item-title>{{ subItem.title }}</v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
            </v-list-item>
          </v-list>
        </v-menu>
      </div>

      <!-- Botón de reseteo de filtros -->
      <v-tooltip location="top">
        <template v-slot:activator="{ props }">
          <v-col cols="auto" class="mb-4 ml-2">
            <v-btn v-bind="props" color="warning" icon="mdi-refresh-circle" @click="resetFiltros">
            </v-btn>
          </v-col>
        </template>
        <span>Resetear Filtros</span>
      </v-tooltip>

    </v-row>

  </v-card-text>

  <!-- Tabla que muestra el historial de ventas -->
  <v-card   title="Historial De Ventas" class="text-center">
    <v-data-table :headers="headers" :filter-keys="['numeroPedido']" :items="filteredHistorial" :search="search"
      v-model:items-per-page="itemPerPage" :items-per-page-options="itemsPerPageOptions" class="elevation-2">
      <template v-slot:item.accion="{ item }">
        <!-- Botón para ver los detalles del pedido -->
        <td class="d-flex justify-center align-center">
          <v-btn block color="light-blue" @click="AbrirDialog(item)">
            <v-icon>mdi-eye</v-icon>
          </v-btn>
        </td>
      </template>
    </v-data-table>

    <!-- Dialog de detalles del pedido -->
    <v-dialog v-model="dialog" width="auto">
      <v-card max-width="400" prepend-icon="mdi-invoice-list-outline" title="Detalles del pedido">
        <v-card-text>
          <div v-if="pedidoSeleccionado">
            <v-list dense>
              <v-list-item v-for="(comida, index) in pedidoSeleccionado.detallePedido" :key="comida.id">
                <v-list-item-title><strong>Comida:</strong> {{ comida.nombreComida }}</v-list-item-title>
                <v-list-item-subtitle><strong>Cantidad:</strong> {{ comida.cantidad }}</v-list-item-subtitle>
                <v-list-item-subtitle><strong>Precio:</strong> {{ comida.precio }}</v-list-item-subtitle>
                <v-list-item-subtitle><strong>Precio con Descuento:</strong> {{ comida.precioDescuento
                  }}</v-list-item-subtitle>
                <v-list-item-subtitle><strong>Estado de Promoción:</strong> {{ comida.estadoPromocion
                  }}</v-list-item-subtitle>
                <v-list-item-subtitle><strong>Nota:</strong> {{ comida.nota }}</v-list-item-subtitle>
              </v-list-item>
              <v-divider></v-divider>
            </v-list>
          </div>
        </v-card-text>
        <template v-slot:actions>
          <!-- Botón para cerrar el dialog -->
          <v-btn class="ms-auto" text @click="dialog = false">Cerrar</v-btn>
        </template>
      </v-card>
    </v-dialog>
  </v-card>
</template>

<script>
export default {
  data() {
    return {
      opcionSubMenuSeleccionada: null,
      // Definir las opciones para los filtros
      items: [
        { title: 'Estado del Pedido', subMenu: [{ title: 'Aceptado', value: 'A' }, { title: 'Cancelado', value: 'C' }] },
        { title: 'Metodo Pago', subMenu: [{ title: 'Tarjeta', value: 'T' }, { title: 'Contra Entrega', value: 'P' }] },
        // Se pueden agregar más filtros aquí
      ],
      fechaInicio: null,  // Fecha de inicio para filtro
      fechafinal: null,   // Fecha de fin para filtro
      inicioFechaMenu: false, // Controlar el estado del menú de fecha de inicio
      finalFechaMenu: false,  // Controlar el estado del menú de fecha de fin
      dialog: false,  // Estado del dialog para mostrar detalles del pedido
      pedidoSeleccionado: null,  // Pedido seleccionado para mostrar en el dialog
      headers: [
        { title: "Estado del pedido", key: "estadoPedido", sortable: false },
        { title: "Número de Pedido", key: "numeroPedido" },
        { title: "Usuario", key: "usuario", sortable: false },
        { title: "Fecha del Pedido", key: "fechaHoraPedido" },
        { title: "Total", key: "total", sortable: false },
        { title: "Método de Pago", key: "metodoPago", sortable: false },
        { title: "Ver Detalles del Pedido", key: "accion", sortable: false },
      ],
      search: "",  // Variable para almacenar la búsqueda
      historial: [], // Datos de historial de ventas
      itemPerPage: 10,  // Páginas por defecto
      itemsPerPageOptions: [10, 25, 50, 100], // Opciones de número de items por página
    };
  },

  computed: {
    // Formato de fecha para mostrarla en formato 'DD/MM/YYYY'
    fechaInicioFormateada() {
      return this.fechaInicio ? new Date(this.fechaInicio).toLocaleDateString("en-GB") : "";
    },
    fechaFinalFormateada() {
      return this.fechafinal ? new Date(this.fechafinal).toLocaleDateString("en-GB") : "";
    },
    // Filtrar el historial según las fechas y el filtro seleccionado
    filteredHistorial() {
      return this.historial.filter(item => {
        const fechaPedido = new Date(item.fechaHoraPedido);
        const fechaInicio = this.fechaInicio ? new Date(this.fechaInicio) : null;
        const fechaFinal = this.fechafinal ? new Date(this.fechafinal) : null;

        const dentroDelRango =
          (!fechaInicio || fechaPedido >= fechaInicio) &&
          (!fechaFinal || fechaPedido <= fechaFinal);

        let cumpleFiltro = true;
        // Filtros adicionales según la opción seleccionada
        if (this.opcionSubMenuSeleccionada) {
          if (this.opcionSubMenuSeleccionada === "A" || this.opcionSubMenuSeleccionada === "C") {
            cumpleFiltro = item.estadoPedido === this.opcionSubMenuSeleccionada;
          } else if (this.opcionSubMenuSeleccionada === "T" || this.opcionSubMenuSeleccionada === "P") {
            cumpleFiltro = item.metodoPago === this.opcionSubMenuSeleccionada;
          }
        }

        return dentroDelRango && cumpleFiltro;
      });
    }
  },

  methods: {
    // Método para seleccionar un submenú de filtro
    seleccionarSubMenu(subMenuValue) {
      this.opcionSubMenuSeleccionada = subMenuValue
    },

    // Método para resetear los filtros
    resetFiltros() {
      this.fechaInicio = null;
      this.fechafinal = null;
      this.inicioFechaMenu = false;
      this.finalFechaMenu = false;
      this.opcionSubMenuSeleccionada = null;
      this.search = '';
    },

    // Método para abrir el dialog de detalles del pedido
    AbrirDialog(item) {
      this.pedidoSeleccionado = item;
      this.dialog = true;
    },

    // Método para listar el historial de ventas
    async listarHistorial() {
      const response = await this.axios.get("/admin/rest/comida/ventas/historial/listar");
      this.historial = response.data.data;
    },
  },

  mounted() {
    this.listarHistorial();  // Cargar el historial al montar el componente
  },
};
</script>
