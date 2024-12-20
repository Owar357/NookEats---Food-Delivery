<template>
  <v-card flat>
    <v-card-title class="d-flex align-center pe-2">
      <v-icon icon="mdi-food-fork-drink"></v-icon> &nbsp;
      Comidas Registradas

      <v-spacer></v-spacer>

      <v-text-field v-model="search" density="compact" label="Buscar" prepend-inner-icon="mdi-magnify"
        variant="solo-filled" flat hide-details single-line></v-text-field>
    </v-card-title>

    <v-divider></v-divider>

    <v-data-table v-model:search="search" :filter-keys="['nombre']" :items="comidaOrdenada" item-value="nombre"
      :items-per-page="5" :headers="headers">

      <template v-slot:item.disponibilidad="{ item }">
        <v-chip :color="item.disponibilidad === 1 ? 'green' : 'red'" label>
          {{ item.disponibilidad === 1 ? 'Disponible' : 'No disponible' }}
        </v-chip>
      </template>

      <template v-slot:item.descripcion="{ item }">
        <v-chip style="max-width: 150px;" label>{{ item.descripcion }}</v-chip>
      </template>

      <template v-slot:item.imagen="{ item }">
        <v-card class="my-2" elevation="2" rounded>
          <v-img :src="item.imagen ? item.imagen : 'default_image.jpg'" height="64" cover></v-img>
        </v-card>
      </template>

      <template v-slot:item.actions="{ item }">
        <v-row no-gutters class="d-flex justify-start align-center">



          <v-tooltip location="top">
            <template v-slot:activator="{ props }">
              <v-col cols="auto" class="pa-0">
                <v-btn v-bind="props" @click="seleccionarComida(item)" icon="mdi-note-edit-outline" color="primary"
                  small dense class="mx-1">
                </v-btn>
              </v-col>
            </template>
            <span>Edita tu comida </span>
          </v-tooltip>


          <v-col cols="auto" class="pa-0">
            <v-tooltip location="top">
              <template v-slot:activator="{ props }">
                <v-btn v-bind="props" :color="item.disponibilidad === 1 ? 'red' : 'green'" @click="CambiarEstado(item)"
                  rounded class="mx-1"
                  style="width: 45px; height: 45px; min-width: 40px; display: flex; justify-content: center; align-items: center;">
                  <v-icon>{{ item.disponibilidad === 1 ? 'mdi-eye-off' : 'mdi-eye' }}</v-icon>
                </v-btn>
              </template>

              
              <span>
                {{ item.disponibilidad === 1 ? 'Desactivar la disponibilidad de esta comida' : 'Activar la disponibilidad de esta comida' }}
              </span>
            </v-tooltip>
          </v-col>

        </v-row>

        <v-dialog v-model="ModalAbierto" max-width="600px" :height="'600px'">
          <v-card>
            <v-card-title>
              Editar Comida
            </v-card-title>
            <v-card-text>
              <v-col>
                <v-row class="d-flex align-center" no-gutters>
                  <v-img v-if="previewUrl" :src="previewUrl" max-height="170" max-width="170" class="rounded mx-auto">
                  </v-img>
                </v-row>
                <v-row>
                  <v-file-input prepend-icon="" v-model="formImagen.imagen" label="Subir archivo"
                    @change="mostrarPrevisualizacion">
                  </v-file-input>
                </v-row>
              </v-col>

              <v-text-field v-model="ComidaSeleccionada.nombre" label="nombre"></v-text-field>
              <v-text-field v-model="ComidaSeleccionada.precio" label="precio"></v-text-field>
              <v-autocomplete v-model="ComidaSeleccionada.categoria" :items="categorias" item-title="nombre"
                item-value="id" label="categorias"></v-autocomplete>
              <v-textarea label="Descripcion" v-model="ComidaSeleccionada.descripcion" variant="outlined"></v-textarea>
            </v-card-text>

            <v-card-actions>
              <v-btn color="green" @click="editarComida" text variant="outlined">Editar</v-btn>
              <v-btn color="primary" variant="outlined" text @click="ModalAbierto = false">Cerrar</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        
      </template>
    </v-data-table>
  </v-card>
</template>


<script>
import Swal from 'sweetalert2';

export default {
  data: () => {
    return {



      categorias: [],
      formImagen: {
        imagen: null
      },
      ComidaSeleccionada: null,
      previewUrl: null,
      ModalAbierto: false,
      comidas: [],
      comidaOrdenada: [],
      search: '',
      headers: [
        { title: 'Estado de la comida', key: 'disponibilidad' },
        { title: 'Nombre', align: 'start', key: 'nombre' },
        { title: 'Precio', key: 'precio' },
        { title: 'Imagen', key: 'imagen' },
        { title: 'Categoria', key: 'categoria' },
        { title: 'Descripción', key: 'descripcion' },
        { title: 'Acciones', key: 'actions', sortable: false },
      ]

    }
  },

  methods: {


    async CambiarEstado(item) {

      item.disponibilidad = item.disponibilidad === 1 ? 0 : 1
      try {
        const response = await this.axios.put(`/admin/rest/comida/${item.id}/estado`, {
          disponibilidad: item.disponibilidad
        })

        console.log('Estado actualizado:', response.data.message);

      } catch (error) {

        console.error('Error al actualizar el estado:', error);

      }

    },

    async ListarComida() {

      try {
        const response = await this.axios.get('/admin/rest/comida/listar');
        this.comidas = response.data.data;
        this.OrdenarComidas();

      } catch (error) {
        console.error("Ocurrió un error al realizar la solicitud:", error.message);
      }

    },

    OrdenarComidas() {
      this.comidaOrdenada = [];
      this.comidas.forEach(categoria => {
        categoria.comidas.forEach(comida => {
          this.comidaOrdenada.push(
            {
              id: comida.id,
              nombre: comida.nombre,
              descripcion: comida.descripcion,
              precio: comida.precio,
              imagen: comida.imagen_hash,
              imagenoriginal: comida.imagenoriginal,
              categoria: categoria.categoria,
              categoriaid: comida.categoriaid,
              disponibilidad: comida.disponibilidad
            });
        });
      });

    },

    async seleccionarComida(item) {
      this.ComidaSeleccionada = { ...item };
      this.previewUrl = this.ComidaSeleccionada.imagen
      this.ModalAbierto = true;
      console.log(this.ComidaSeleccionada)
    },



    mostrarPrevisualizacion(event) {
      const file = event.target.files[0];

      if (file) {
        this.previewUrl = URL.createObjectURL(file);
      }


    },

    async editarComida() {
      try {
        const formData = new FormData();

        const categoriaId = typeof this.ComidaSeleccionada.categoria === 'number'
          ? this.ComidaSeleccionada.categoria
          : this.categorias.find(c => c.nombre === this.ComidaSeleccionada.categoria)?.id;

        const jsonData = {
          nombre: this.ComidaSeleccionada.nombre,
          descripcion: this.ComidaSeleccionada.descripcion,
          precio: this.ComidaSeleccionada.precio,
          categoria: categoriaId,
          disponibilidad: this.ComidaSeleccionada.disponibilidad
        };


        formData.append("data", JSON.stringify(jsonData));

        if (this.formImagen.imagen) {
          this.previewUrl = this.formImagen.imagen;
          formData.append("imagen", this.formImagen.imagen)
        } else {
          const imagenOriginal = this.ComidaSeleccionada.imagen.split('/').pop();

          formData.append("imagenhash", imagenOriginal);
        }

       
        const response = await this.axios.post(`/admin/rest/comida/${this.ComidaSeleccionada.id}/editar`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        });


    
        Swal.fire({
          toast: true,
          background: 'black',
          position: 'top-end',
          icon: 'success',
          title: 'Comida editada correctamente',
          showConfirmButton: false,
          timer: 1500,
          timerProgressBar: true,
          customClass: {
            popup: 'my-popup',
            timerProgressBar: 'my-progress-bar'
          }
        });

        this.formImagen.imagen = null;
        this.ListarComida();
        this.ModalAbierto = false;

      } catch (error) {
        if (error.response) {
          const mensajeErrorBackend = error.response?.data?.message || mensajeError;
          alert(mensajeErrorBackend);
        }
      }

    },
    async listarcategorias() {
      const response = await this.axios.get('/admin/rest/comida/categorias/listar');
      this.categorias = response.data.categorias;
    }
  },
  mounted() {
    this.ListarComida();
    this.listarcategorias();
  }
}
</script>


<style scoped>
.estado-activo {
  color: green;
}

.estado-inactivo {
  color: red;
}
</style>