<template>
    <v-card flat>
        <v-card-title class="d-flex align-center pe-2">
            <v-icon icon="mdi-folder-open"></v-icon> &nbsp;
            Categorias Registradas

            <v-spacer></v-spacer>

            <v-text-field v-model="search" density="compact" label="Buscar categorias" prepend-inner-icon="mdi-magnify"
                variant="solo-filled" flat hide-details single-line></v-text-field>
        </v-card-title>

        <v-divider></v-divider>
        <v-data-table v-model:search="search" :filter-keys="['nombre']" :headers="headers" :items="categoriasOrdenadas"
            item-value="nombre" :loading="isLoading" items-per-page="5"
            loading-text="Cargando datos... por favor espera">



            <template v-slot:[`item.acciones`]="{ item }">
                <v-btn @click="AccionarDialog(item)" color="primary" size="small">
                    Editar Categoria
                </v-btn>

            </template>
        </v-data-table>

        <v-dialog v-model="dialogVisible" max-width="300">
      <v-card prepend-icon="mdi-format-list-checks" title="Editar Categoria">
        <v-card-text>
          <v-row dense>
            <v-col cols="12" md="12" sm="6">
              <v-text-field   v-model="categoriaSeleccionada.nombre" prepend-inner-icon="mdi-pencil" label="Nombre"></v-text-field>
            </v-col>
          </v-row>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-actions>
          <v-spacer></v-spacer>

          <v-btn text="Editar" variant="outlined" color="blue" @click="EditarCategoria" ></v-btn>
          <v-btn text="Cerrar" variant="outlined" color="red" @click="dialogVisible = false"></v-btn>

        </v-card-actions>
      </v-card>
    </v-dialog>
        
    </v-card>
</template>
<script>
import Swal from 'sweetalert2';

export default {

    data: () => {

        return {

            categoriaSeleccionada:null,
            dialogVisible: false,
            isLoading: false,
            headers: [
                { title: "Categorias", value: "nombre" },
                { title: "Acciones", value: "acciones" },
            ],
            search: '',
            categorias: [],
            categoriasOrdenadas: [],
        }

    },

    methods: {
        

        
        async EditarCategoria(){
             

           try {

            const response = await this.axios.put(`/admin/rest/comida/categoria/${this.categoriaSeleccionada.id}/editar`,{

                 nombre: this.categoriaSeleccionada.nombre

            }); 

            
            this.listarCategorias();
            this.dialogVisible=false;

            Swal.fire({
          toast: true,
          background: 'black',
          position: 'top-end',
          icon: 'success',
          title: 'Categoria editada correctamente',
          showConfirmButton: false,
          timer: 1500,
          timerProgressBar: true,
          customClass: {
            popup: 'my-popup',
            timerProgressBar: 'my-progress-bar'
          }
        });
             

           } catch (error) {
            Swal.fire({
          toast: true,
          background: 'black',
          position: 'top-end',
          icon: 'error',
          title: 'Ocurrio un error y no se pudo editar la categoria',
          showConfirmButton: false,
          timer: 1500,
          timerProgressBar: true,
          customClass: {
            popup: 'my-popup',
            timerProgressBar: 'my-progress-bar'
          }
        });
             
           }
            
        },

        AccionarDialog(item){
            this.categoriaSeleccionada = {...item}
            this.dialogVisible=true;
           

        },

        OrdenarCategoria() {
            this.categoriasOrdenadas = []

            this.categorias.forEach(categoria => {

                this.categoriasOrdenadas.push(
                    {
                        nombre: categoria.nombre
                    }
                )

            });
        },

        async listarCategorias() {
            this.isLoading = true;
            try {
                const response = await this.axios.get('/admin/rest/comida/categorias/listar');
                this.categorias = response.data.categorias;
                this.categoriasOrdenadas = this.categorias.map((categoria) => ({
                    id:categoria.id,
                    nombre: categoria.nombre,
                }));


            } catch (error) {

                console.error("Error al cargar categorías:", error);

            } finally {
                this.isLoading = false;
            }


        }

    },

    mounted() {
        this.listarCategorias();

    }
}
</script>
