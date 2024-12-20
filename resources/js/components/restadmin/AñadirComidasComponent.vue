<template>

  <head>

    <meta name="csrf-token" content="{{ csrf_token() }}">
  </head>


  <v-container>
    <v-card class="elevation-10 " style="padding: 50px;">

      <v-form ref="form">

        <v-row>

          <v-col cols="12" sm="6">
            <v-file-input v-model="formdata.imagen" label="Seleccionar imagen" variant="solo" accept="image/*"
              append-inner-icon="mdi-image" prepend-icon="" :rules="[rules.required]" @change="mostrarPrevisualizacion"
              bg-color="deep-purple-darken-2" show-size counter></v-file-input>
          </v-col>

          <!-- Previsualización de la imagen en la mitad derecha -->
          <v-col cols="12" sm="6" class="d-flex align-center">
            <v-img v-if="previewUrl" :src="previewUrl" max-height="150" max-width="150" class="rounded"></v-img>
          </v-col>
        </v-row>


        <v-row justify="space-between">
          <v-col cols="12" sm="6">
            <v-text-field label="nombre" v-model="formdata.nombre" :rules="[rules.required]" variant="outlined"
              append-inner-icon="mdi-pencil-outline"></v-text-field>
          </v-col>

          <v-col cols="12" sm="6">
            <v-text-field label="precio" v-model="formdata.precio" :rules="[rules.required, rules.isNumber]" prefix="$"
              variant="solo" append-inner-icon=" mdi-currency-usd"></v-text-field>,
          </v-col>
        </v-row>


        <v-row justify="start" align="center" class="g-0">



          <v-col>
            <v-autocomplete v-model="formdata.categoria" :items="categorias" item-title="nombre" item-value="id"
              label="Seleccionar categoría" return-object variant="solo"></v-autocomplete>
          </v-col>


          <v-col cols="4" sm="3" class="d-flex align-center" style="margin-left: -12px;">
            <v-btn color="green" style="font-weight: bold" variant="outlined" text append-icon="mdi-plus"
              @click="dialog = true">
              Categoria
            </v-btn>
          </v-col>



          <v-col cols="4" sm="3" class="d-flex align-center" style="margin-left: -6rem;">
            <v-tooltip text="Crea y diseña tus categorias personalizadas">
              <template v-slot:activator="{ props }">
                <v-btn icon="mdi-information-slab-symbol" size="" v-bind="props" variant="outlined"
                  color="blue-lighten-1" />
              </template>
            </v-tooltip>
          </v-col>

          <v-dialog v-model="dialog" max-width="600px" height="300px">
            <v-card>
              <v-card-title class="headline">Añadir Categoría Personalizada</v-card-title>

              <v-container>
                <v-row>
                  <v-col cols="12" class="d-flex">
                    <v-text-field v-model="nuevaCategoria" append-inner-icon="mdi-tag" label="Categoria" variant="solo"
                      style="width: 100%;"></v-text-field>
                  </v-col>
                </v-row>
              </v-container>



              <v-card-actions class="d-flex jusfify-space-between">

                <v-btn color="red" append-icon="mdi-close" @click="dialog = false" variant="outlined">Cerrar</v-btn>

                <v-btn color=blue variant="outlined" @click="añadirCategoria" append-icon="mdi-send"> CREAR </v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>


        </v-row>

        <v-row>
          <v-col cols="12">
            <v-textarea label="descripcion" :rules="[rules.required, rules.maxLength]" :counter="300"
              v-model="formdata.descripcion" counter variant="solo" append-inner-icon="mdi-text"></v-textarea>
          </v-col>
        </v-row>


        <v-row justify="center">
          <v-col cols="auto" class="text-center">
            <v-btn color="#0D47A1" variant="outlined" append-icon="mdi-send" @click="añadirComida">Añadir</v-btn>
          </v-col>
        </v-row>
      </v-form>

    </v-card>

  </v-container>
</template>

<script>
import Swal from 'sweetalert2';

export default {
  data() {
    return {

      imagenSeleccionada: null,
      previewUrl: null,
      dialog: false,
      categorias: [],
      valid: false,

      formdata: {
        imagen: {},
        nombre: '',
        precio: null,
        categoria: {
          id: '',
          nombre: null
        },
        descripcion: ''
      },
      nuevaCategoria: '',
      rules: {


        required: (value) => (value !== null && value !== '') || 'Este campo es obligatorio',
        isNumber: (value) => {
          if (value === null || value === '') {
            return 'Este campo es obligatorio';
          }
          return !isNaN(parseFloat(value)) || 'Debe ser un precio válido';
        },
        maxLength: value => {
          return value.length <= 300 || 'La descripción no debe exceder los 300 caracteres.';
        }
      }
    };
  },


  methods: {
    async añadirComida() {



      const { valid } = await this.$refs.form.validate();


      if (valid) {
        try {
          const formData = new FormData();

          const jsonData = {
            nombre: this.formdata.nombre,
            precio: this.formdata.precio,
            categoria: this.formdata.categoria.id,
            descripcion: this.formdata.descripcion,
          }

          formData.append("data", JSON.stringify(jsonData));

          if (this.formdata.imagen) {
            formData.append("imagen", this.formdata.imagen);  
          }


          const response = await this.axios.post('/admin/rest/comida/crear', formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
          })


          console.log(response);

          Swal.fire({
            toast: true,
            background: 'black',
            position: 'top-end',
            icon: 'success',
            title: 'Comida Añadida Exitosamente',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
            customClass: {
              popup: 'my-popup',
              timerProgressBar: 'my-progress-bar'
            }
          });

          this.$refs.form.reset();

        } catch (error) {

          Swal.fire({
            toast: true,
            background: 'black',
            position: 'top-end',
            icon: 'error',
            title: 'Ocurrio un error y no se añadio la comida',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
            customClass: {
              popup: 'my-popup',
              timerProgressBar: 'my-progress-bar-error'
            }
          });


        }

      }
    },


    mostrarPrevisualizacion(event) {
      const file = event.target.files[0];
      if (file) {
        this.previewUrl = URL.createObjectURL(file);
      }
    },

  





    async añadirCategoria() {
      const jsonData = {
        nombre: this.nuevaCategoria,
      };

      try {

        const response = await this.axios.post('/admin/rest/comida/categorias/crear', jsonData)

        this.dialog = false;

        this.categorias.push(response.data.categoria)


        Swal.fire({
          toast: true,
          background: 'black',
          position: 'top-end',
          icon: 'success',
          title: 'Categoria añadida con exito',
          showConfirmButton: false,
          timer: 1500,
          timerProgressBar: true,
          customClass: {
            popup: 'my-popup',
            timerProgressBar: 'my-progress-bar'
          }
        });
      } catch (error) {

        if (error.response && error.response.status === 409) {
          Swal.fire({
            toast: true,
            background: 'black',
            position: 'top-end',
            icon: 'warning',
            title: 'La categoria ya existe',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
            customClass: {
              popup: 'my-popup',
              timerProgressBar: 'my-progress-bar-warning'
            }
          });
        } else {

          Swal.fire({
            toast: true,
            background: 'black',
            position: 'top-end',
            icon: 'error',
            title: 'Error al crear  la categoria',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
            customClass: {
              popup: 'my-popup',
              timerProgressBar: 'my-progress-bar-error'
            }
          });

        }
      }
    },

    async listarCategorias() {
      const response = await this.axios.get('/admin/rest/comida/categorias/listar');
      this.categorias = response.data.categorias;
      console.log(this.categorias)
    },
  },


  mounted() {
    this.listarCategorias();
  },
};
</script>


<style>
.my-popup {
  background-color: black;
  /* Fondo de la notificación */
  color: white;
  /* Color del texto */
}

.my-progress-bar {
  background-color: #76FF03 !important;
  /* Barra de progreso verde */
}

.my-progress-bar-error {
  background-color: #FF0000 !important;
  /* Barra de progreso verde */
}

.swal2-icon-success {
  border-color: #76FF03 !important;
  /* Borde del icono */
}

/* Personalización del icono (lo hace del mismo color que la barra de progreso) */
.swal2-icon-success .swal2-icon-path {
  stroke: #76FF03 !important;
  /* Color del trazo del icono (verde) */
}
</style>
