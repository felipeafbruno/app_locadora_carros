<template>
    <table class="table table-hover">
        <thead>
            <tr>
                <th scope="col" v-for="titulo, key in titulos" :key="key">
                    {{ titulo }}
                </th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="marca in dados" :key="marca.id">
                <th scope="row">{{ marca.id }}</th>
                <td>{{ marca.nome }}</td>
                <td><img :src="'/storage/'+marca.imagem" witdh="40" height="40"/></td>
            </tr>
        </tbody>
    </table> 
</template>

<script>
    export default {
        props: ['dados', 'titulos']
    }
</script>
