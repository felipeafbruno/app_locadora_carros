<template>
    <table class="table table-hover">
        <thead>
            <tr>
                <th scope="col" v-for="t, key in titulos" :key="key" class="text-uppercase">
                    {{ t.titulo }}
                </th>
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
            </tr>
        </tbody>
    </table> 
</template>

<script>
    export default {
        props: ['dados', 'titulos'],
        // Método criado por eu para filtrar dados do objeto marca do segundo v-for
        // methods: {
        //     filtrarDados(obj) { 
        //         let campos = Object.keys(this.titulos);
        //         let objetoFiltrado = 
        //         Object.fromEntries(
        //                 Object.entries(obj).filter(
        //                     ([key]) => campos.find(campo => campo === key)
        //                 )
        //             );
        //         return objetoFiltrado; 
        //     }
        // },
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
