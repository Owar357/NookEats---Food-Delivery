<template>
  <v-container>
    <v-card class="elevation-10" style="padding: 50px;">
      <h3 style="font-weight: bold; font-family: 'Arial', sans-serif; font-size: 2rem;">Formulario de registro del
        restaurante</h3>
      <v-form ref="form" v-model="valid">

        <v-container>
          <v-row>




            <v-col cols="12" sm="6">
              <v-text-field v-model="formData.nombre" append-inner-icon="mdi-pencil-outline"
                label="Nombre del Restaurante" variant="solo" :rules="rules.nombre"></v-text-field>
            </v-col>


            <v-col cols="12" sm="6">
              <v-text-field v-model="formData.ubicacion" append-inner-icon="mdi mdi-map-marker" label="Ubicacion"
                variant="solo" :rules="rules.ubicacion"></v-text-field>
            </v-col>



            <v-col cols="12" sm="6">
              <v-file-input v-model="formData.imagen" clearable label="Foto de perfil del restaurante"
                variant="solo-inverted" append-inner-icon="mdi-file-image-plus" prepend-icon=""
                accept=".webp, .png"></v-file-input>
            </v-col>




            <v-col cols="12" sm="6">
              <v-text-field v-model="formData.telefono" append-inner-icon="mdi-phone-plus" label="Teléfono"
                variant="solo" :rules="rules.telefono"></v-text-field>

            </v-col>

            <v-col cols="12" sm="6">

              <v-text-field v-model="formData.telefonoSecundario" append-inner-icon="mdi-phone-plus"
                label="Teléfono secundario" variant="solo"></v-text-field>

            </v-col>


            <v-col cols="12" sm="6">
              <v-select label="Tipo de negocio" v-model="formData.tipoNegocio" :items="tiponegocio" item-title="nombre"
                item-value="id" :rules="rules.tipoNegocio" prepend-inner-icon="mdi-storefront-plus" />
            </v-col>





            <v-radio-group v-model="formData.modeloNegocio" :rules="rules.modeloNegocio">

              <template v-slot:label>
                <div><strong>Modelo de negocio</strong></div>
              </template>

              <v-row>
                <v-col cols="12" sm="4">
                  <v-radio value="independiente" color="indigo">
                    <template v-slot:label>
                      <div><strong class="text-success">Independiente</strong></div>
                    </template>
                  </v-radio>
                </v-col>

                <v-col cols="12" sm="4">
                  <v-radio value="franquicia" color="indigo">
                    <template v-slot:label>
                      <div><strong class="text-primary">Franquicia</strong></div>
                    </template>
                  </v-radio>
                </v-col>

                <v-col cols="12" sm="4">
                  <v-radio value="cadena" color="indigo">
                    <template v-slot:label>
                      <div><strong class="text-warning">Cadena</strong></div>
                    </template>
                  </v-radio>
                </v-col>

              </v-row>
            </v-radio-group>

            <v-container fluid>
              <v-textarea v-model="formData.descripcion" :rules="rules.descripcion" label="Descripcion" :counter-value="`${formData.descripcion.length}/300`"
                prepend-inner-icon="mdi-text"></v-textarea>
            </v-container>




              <v-col class="d-flex justify-center">
                <v-btn color="light-blue" style="font-weight: bold" text size="large" href="/home"
                  prepend-icon="mdi-arrow-left">
                  Regresar
                </v-btn>
              </v-col>


              <v-col class="d-flex justify-center">
                <v-btn color="green" style="font-weight: bold" text size="large" @click="enviarFormulario"  append-icon="mdi-send" >Enviar
                  Formulario</v-btn>
              </v-col>



          </v-row>
        </v-container>
      </v-form>
    </v-card>
  </v-container>
</template>



<script>
import Swal from 'sweetalert2';

export default {
  data: () => ({


    tiponegocio: [],
    valid: false,
    rules: {
      nombre: [v => !!v || 'Este campo es obligatorio'],
      ubicacion: [v => !!v || 'Este campo es obligatorio'],
      telefono: [v => !!v || 'Este campo es obligatorio'],
      tipoNegocio: [v => !!v || 'Este campo es obligatorio'],
      descripcion: [v => !!v || 'Este campo es obligatorio', v => v.length <= 300 || 'El límite de caracteres es 300'],
    },

    radios: 'Independiente',



    formData: {
      nombre: '',
      ubicacion: '',
      telefono: '',
      telefonoSecundario: '',
      tipoNegocio: {
        id: null,
        nombre: ''
      },
      descripcion: '',
      modeloNegocio: '',
      imagen: {},
    }
  }),

  methods: {
    async enviarFormulario() {
      if (!this.valid) {
        console.log("Formulario inválido. Por favor, complete todos los campos.");

        Swal.fire({
          position: 'top-end',
          icon: 'error',
          title: 'Formulario no  valido',
          text: 'Por favor complete los campos obligatorios',
          showConfirmButton: false,
          timer: 3000,
          toast: true,
          timerProgressBar: true,
          background: '#f8d7da',
          color: '#721c24',
        })
        return;
      }

      const formData = new FormData();
      
      const jsonData = {
        nombre: this.formData.nombre,
        ubicacion: this.formData.ubicacion,
        telefono: this.formData.telefono,
        telefonoSecundario: this.formData.telefonoSecundario,
        tipoNegocio: this.formData.tipoNegocio,
        descripcion: this.formData.descripcion,
        modeloNegocio: this.formData.modeloNegocio
      };

      formData.append("data", JSON.stringify(jsonData));
      formData.append("imagen", this.formData.imagen);


      try {

        const response = await axios.post('/registro', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });


        Swal.fire({
          position: 'top-end',
          icon: 'success',
          title: 'Formulario enviado',
          text: 'Los datos del restaurante han sido enviados correctamente.',
          showConfirmButton: false,
          timer: 3000,
          toast: true,
          timerProgressBar: true,
          background: '#d4edda',
          color: '#155724',
        })

        this.$refs.form.reset();

      } catch (error) {

        if (error.response) {

          Swal.fire({
            position: 'top-end',
            icon: 'error',
            title: 'Error al enviar',
            text: error.response.data.message || 'Hubo un problema al procesar la solicitud.',
            showConfirmButton: true,
            background: '#f8d7da',
            color: '#721c24',
          });
        }

      }
    },

    async listarTipoNegocio() {
      try {
        const response = await this.axios.get('/seleccionar/negocio');
        this.tiponegocio = response.data.tiponegocios;
      } catch (error) {
        console.error("Error fetching modelos: ", error);
      }

    },
  },

  mounted() {
    this.listarTipoNegocio();
  }
}
</script>