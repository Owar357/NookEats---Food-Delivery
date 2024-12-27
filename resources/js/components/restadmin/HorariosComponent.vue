<template>
    <v-card flat>
        <v-card-title class="d-flex align-center pe-2">
            <v-icon icon="mdi-clock"></v-icon> &nbsp;
            Horarios del Restaurante
            <v-spacer></v-spacer>
            <v-dialog max-width="500" v-model="isAdding">
                <template v-slot:activator="{ props: activatorProps }">
                    <v-btn v-bind="activatorProps" color="success" text="Añadir Nuevo Horario"
                        append-icon="mdi-clock-plus" variant="flat"></v-btn>
                </template>
                <template v-slot:default="{ isActive }">
                    <v-card>
                        <v-card-title>
                            <span class="text-h6">Añadir Horario</span>
                        </v-card-title>
                        <v-card-text>
                            <v-container>
                                <v-row>
                                    <v-col>
                                        <v-autocomplete label="Seleccionar Día" :items="diasSemana" item-text="title"
                                            item-value="value" variant="outlined"
                                            v-model="diaSeleccionado"></v-autocomplete>
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col>
                                        <v-text-field v-model="horaEntrada" :active="menu1" :focus="menu1"
                                            label="Hora de Apertura" prepend-inner-icon="mdi-clock-time-four-outline"
                                            readonly>
                                            <v-menu v-model="menu1" :close-on-content-click="false" activator="parent"
                                                transition="scale-transition">
                                                <v-card>
                                                    <v-time-picker format="24hr" title="Selecciona la Hora de Apertura"
                                                        v-if="menu1" v-model="horaEntrada" full-width></v-time-picker>
                                                    <v-card-actions>
                                                        <v-spacer></v-spacer>
                                                        <v-btn text color="primary" variant="outlined"
                                                            @click="menu1 = false">
                                                            Cerrar
                                                        </v-btn>
                                                    </v-card-actions>
                                                </v-card>
                                            </v-menu>
                                        </v-text-field>
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col>
                                        <v-text-field v-model="horaSalida" :active="menu2" :focus="menu2"
                                            label="Hora de Cierre" prepend-inner-icon="mdi-clock-time-four-outline"
                                            readonly>
                                            <v-menu v-model="menu2" :close-on-content-click="false" activator="parent"
                                                transition="scale-transition">
                                                <v-card>
                                                    <v-time-picker format="24hr" title="Selecciona la Hora de Cierre"
                                                        v-if="menu2" v-model="horaSalida" full-width></v-time-picker>
                                                    <v-card-actions>
                                                        <v-spacer></v-spacer>
                                                        <v-btn text color="primary" variant="outlined"
                                                            @click="menu2 = false">
                                                            Cerrar
                                                        </v-btn>
                                                    </v-card-actions>
                                                </v-card>
                                            </v-menu>
                                        </v-text-field>
                                    </v-col>
                                </v-row>
                            </v-container>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn text="Cerrar" color="light-blue" variant="outlined" @click="agregarHorarios">
                                Añadir
                            </v-btn>
                            <v-btn text="Cerrar" color="red" variant="outlined" @click="isAdding = false">
                                Cerrar
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </template>
            </v-dialog>
        </v-card-title>
        <v-data-table :items="this.horarios" :headers="headers" :items-per-page="-1" hide-default-footer>
            <template v-slot:item.acciones="{ item }">
                <v-dialog max-width="500" v-model="isEditing">
                    <template v-slot:activator="{ props: activatorProps }">
                        <v-btn v-bind="activatorProps" @click="seleccionarHorario(item)" color="success"
                            text="Editar Horario" append-icon="mdi-clock-plus" variant="flat"></v-btn>
                    </template>
                    <template v-slot:default="{ isActive }">
                        <v-card>
                            <v-card-title>
                                <span class="text-h6">Editar Horario</span>
                            </v-card-title>
                            <v-card-text>
                                <v-container>
                                    <v-row>
                                        <v-col>
                                            <v-autocomplete label="Seleccionar Día" :items="diasSemana"
                                                item-text="title" item-value="value" variant="outlined"
                                                v-model="horarioSeleccionado.dia"></v-autocomplete>
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col>
                                            <v-text-field v-model="horarioSeleccionado.entrada" :active="menu1"
                                                :focus="menu1" label="Hora de Apertura"
                                                prepend-inner-icon="mdi-clock-time-four-outline" readonly>
                                                <v-menu v-model="menu1" :close-on-content-click="false"
                                                    activator="parent" transition="scale-transition">
                                                    <v-card>
                                                        <v-time-picker format="24hr"
                                                            title="Selecciona la Hora de Apertura" v-if="menu1"
                                                            v-model="horarioSeleccionado.entrada"
                                                            full-width></v-time-picker>
                                                        <v-card-actions>
                                                            <v-spacer></v-spacer>
                                                            <v-btn text color="primary" variant="outlined"
                                                                @click="menu1 = false">
                                                                Cerrar
                                                            </v-btn>
                                                        </v-card-actions>
                                                    </v-card>
                                                </v-menu>
                                            </v-text-field>
                                        </v-col>
                                    </v-row>
                                    <v-row>
                                        <v-col>
                                            <v-text-field v-model="horarioSeleccionado.salida" :active="menu2"
                                                :focus="menu2" label="Hora de Cierre"
                                                prepend-inner-icon="mdi-clock-time-four-outline" readonly>
                                                <v-menu v-model="menu2" :close-on-content-click="false"
                                                    activator="parent" transition="scale-transition">
                                                    <v-card>
                                                        <v-time-picker format="24hr"
                                                            title="Selecciona la Hora de Cierre" v-if="menu2"
                                                            v-model="horarioSeleccionado.salida"
                                                            full-width></v-time-picker>
                                                        <v-card-actions>
                                                            <v-spacer></v-spacer>
                                                            <v-btn text color="primary" variant="outlined"
                                                                @click="menu2 = false">
                                                                Cerrar
                                                            </v-btn>
                                                        </v-card-actions>
                                                    </v-card>
                                                </v-menu>
                                            </v-text-field>
                                        </v-col>
                                    </v-row>
                                </v-container>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn text="Cerrar" color="light-blue" variant="outlined" @click="editarHorario">
                                    Guardar
                                </v-btn>
                                <v-btn text="Cerrar" color="red" variant="outlined" @click="isEditing = false">
                                    Cerrar
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </template>
                </v-dialog>
                <v-btn color="red" append-icon="mdi-delete-empty" @click="eliminarHorario(item)"
                    style="margin-left: 20px;">Eliminar</v-btn>
            </template>
        </v-data-table>
    </v-card>
</template>


<script>
import Swal from 'sweetalert2';

export default {

    data: () => {
        return {
            horarioSeleccionado: {},
            isAdding: false,
            isEditing: false,
            diaSeleccionado: null,
            headers: [
                { title: 'Dia', key: 'horario_dia' },
                { title: 'Horario de Apertura', key: 'hora_apertura' },
                { title: 'Horario de Cierre', key: 'hora_cierre' },
                { title: 'Acciones', key: 'acciones' },

            ],
            diasSemana: [
                { title: 'Lunes', value: 'Lunes' },
                { title: 'Martes', value: 'Martes' },
                { title: 'Miércoles', value: 'Miércoles' },
                { title: 'Jueves', value: 'Jueves' },
                { title: 'Viernes', value: 'Viernes' },
                { title: 'Sábado', value: 'Sabado' },
                { title: 'Domingo', value: 'Domingo' },
                { title: 'Lunes-Viernes', value: 'Lunes-Viernes' },
                { title: 'Lunes-Sabado', value: 'Lunes-Sabado' },
                { title: 'Lunes-Domingo', value: 'Lunes-Domingo' }
            ],
            horaSalida: null,
            menu2: false,
            horaEntrada: null,
            menu1: false,
            horarios: [],
        }
    },

    methods: {
        seleccionarHorario(item) {
            this.horarioSeleccionado = {

                id: item.id,
                dia: item.horario_dia,
                entrada: item.hora_apertura,
                salida: item.hora_cierre,
            }
        },
        async editarHorario() {

            try {

                await this.axios.put(`/admin/rest/comida/horario/config/editar/${this.horarioSeleccionado.id}`, {

                    dia: this.horarioSeleccionado.dia,
                    entrada: this.horarioSeleccionado.entrada,
                    salida: this.horarioSeleccionado.salida

                })



                this.listarHorarios();
                this.isEditing = false;

                Swal.fire({
                    toast: true,
                    background: 'black',
                    position: 'top-end',
                    icon: 'success',
                    title: 'El Horario se Editó Correctamente',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                });
            } catch (error) {
                Swal.fire({
                    toast: true,
                    background: 'black',
                    position: 'top-end',
                    icon: 'error',
                    title: 'Ocurrió un al editar el Horario',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true,
                });

            }



        },


        async eliminarHorario(item) {

            try {

                await this.axios.delete(`/admin/rest/comida/horario/config/eliminar/${item.id}`)

            
                console.log('Eliminando horario con ID:', item.id);
                this.listarHorarios();
                console.log('Horario eliminado correctamente');
        

          

                    Swal.fire({
                        toast: true,
                        background: 'black',
                        position: 'top-end',
                        icon: 'success',
                        title: 'El Horario se Elimino Correctamente',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true,
                        customClass: {
                            timerProgressBar: 'swal-success-progress-bar'
                        },
                    });


            } catch (error) {

                this.isActive = false;

                Swal.fire({
                    toast: true,
                    background: 'black',
                    position: 'top-end',
                    icon: 'error',
                    title: 'Error al intentar eliminar el Horario',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true,
                    customClass: {
                        timerProgressBar: 'swal-error-progress-bar'
                    },
                });

            }

        },

        async agregarHorarios() {
            try {
                await this.axios.post('/admin/rest/comida/horario/config/anadir', {
                    'Dia': this.diaSeleccionado,
                    'horaApertura': this.horaEntrada,
                    'horaCierre': this.horaSalida
                });

                this.listarHorarios();


                    Swal.fire({
                        toast: true,
                        background: 'black',
                        position: 'top-end',
                        icon: 'success',
                        title: 'El Horario se Añadió Correctamente',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true,
                        customClass: {
                            timerProgressBar: 'swal-success-progress-bar'
                        },
                    });

                
                this.isAdding = false,
                this.diaSeleccionado = null;
                this.horaEntrada = null;
                this.horaSalida = null;


            } catch (error) {


                this.isAdding = false

                Swal.fire({
                    toast: true,
                    background: 'black',
                    position: 'top-end',
                    icon: 'error',
                    title: 'Error al Añadir el Horario',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true,
                    customClass: {
                        timerProgressBar: 'swal-error-progress-bar'
                    },
                });
            }
        },


        async listarHorarios() {

            const response = await this.axios.get('/admin/rest/comida/horario/config');
            this.horarios = response.data.horarios
        }
    },

    mounted() {
        this.listarHorarios();
    }
}
</script>


<style>
.swal-success-progress-bar {
    background-color: #00ff00 !important;
    /* Verde brillante */
}

.swal-error-progress-bar {
    background-color: #ff0000 !important;
    /* Rojo */
}
</style>