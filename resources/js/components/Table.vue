<template>
    <table class="table table-hover">
        <thead>
            <tr>
                <th scope="col" v-for="t, key in titulos" :key="key" class="text-uppercase">
                    {{ t.titulo }}
                </th>
                <th v-if="visualizar.visivel"></th>
                <th v-if="atualizar.visivel"></th>
                <th v-if="remover.visivel"></th>
            </tr>
        </thead>
        <tbody>
            <!-- Minha maneira de construir as dados da tabela -->
            <!-- <tr v-for="obj in dados" :key="obj.id">
                <td v-for="valor, chave in filtrarDados(obj)" :key="chave">
                    <span v-if="chave == 'imagem'">
                        <img :src="'/storage/'+valor" width="30" heigth="30" />
                    </span>

                    <span v-else>
                        {{ valor }}
                    </span>
                </td>
            </tr> -->
            <tr v-for="obj, chave in dadosFiltrados" :key="chave">
                <td v-for="valor, chave in obj" :key="chave">
                   <span v-if="titulos[chave].tipo == 'text'">{{ valor }}</span>
                   <span v-if="titulos[chave].tipo == 'date'">{{ '...'+valor }}</span>
                   <span v-if="titulos[chave].tipo == 'image'">
                       <img :src="'/storage/'+valor" width="30" heigth="30" />
                   </span>
                </td>
                <td v-if="visualizar.visivel">
                    <button v-if="visualizar" class="btn btn-outline-primary btn-sm" :data-toggle="visualizar.data_toggle" :data-target="visualizar.data_target" @click="setStore(obj)">Visualizar</button>
                </td>
                <td v-if="atualizar.visivel">
                    <button v-if="atualizar" class="btn btn-outline-success btn-sm">Atualizar</button>
                </td>
                <td v-if="remover.visivel">
                    <button v-if="remover" class="btn btn-outline-danger btn-sm" :data-toggle="remover.data_toggle" :data-target="remover.data_target" @click="setStore(obj)">Remover</button>
                </td>
            </tr>
        </tbody>
    </table> 
</template>

<script>
    export default {
        props: ['dados', 'titulos', 'atualizar', 'visualizar', 'remover'],
        methods: {
            // Método criado por eu para filtrar dados do objeto marca do segundo v-for
            // filtrarDados(obj) { 
            //     let campos = Object.keys(this.titulos);
            //     let objetoFiltrado = 
            //     Object.fromEntries(
            //             Object.entries(obj).filter(
            //                 ([key]) => campos.find(campo => campo === key)
            //             )
            //         );
            //     return objetoFiltrado; 
            // }
            setStore(obj) {
               this.$store.state.item = obj;
               this.$store.state.transacao.status = '';
               this.$store.state.transacao.data.message = '';
            }
        },
        computed: {
            dadosFiltrados() {
                let campos = Object.keys(this.titulos);
                let dadosFiltrados = [];
                
                this.dados.map((item, chave) => {
                    let itemFiltrado = {};
                    campos.forEach(campo => {
                            itemFiltrado[campo] = item[campo];
                    });
                    dadosFiltrados.push(itemFiltrado);
                });
                return dadosFiltrados;
            }
        }
    }
</script>
