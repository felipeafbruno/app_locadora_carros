<template>
    <div class="form-group">
        <label :for="id" class="form-label text-capitalize">{{ titulo }}</label>
        <!-- tag slot permite exibir o conteúdo interno passado no componente -->
        <slot></slot>
        <div :id="idHelp" class="form-text text-muted">{{ textoAjuda }}</div>
    </div>
</template>

<script>
    export default {
        props: ['id', 'titulo', 'idHelp', 'textoAjuda']
    }
</script>
