<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                
                <!-- início do card de busca -->
                <card-component titulo="Busca de Marcas">
                    <template v-slot:conteudo>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col mb-3">
                                    <input-container-component titulo="ID" id="inputId" id-help="idHelp" texto-ajuda="Opcional. Informe o ID da marca">
                                        <input type="number" class="form-control" id="inputId" aria-describedby="idHelp" placeholder="ID" />
                                    </input-container-component>
                                </div>

                                <div class="col mb-3">
                                    <input-container-component titulo="Nome" id="inputNome" id-help="nomeHelp" texto-ajuda="Opcional. Informe o nome da marca">
                                        <input type="number" class="form-control" id="inputNome" aria-describedby="nomeHelp" placeholder="Nome da marca" />
                                    </input-container-component>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template v-slot:rodape>
                        <button type="submit" class="btn btn-primary btn-sm float-right">Pesquisar</button>
                    </template>
                </card-component>
                <!-- fim -->
                
                <!-- início do card de listagem de marcas -->
                <card-component titulo="Relação de Marcas">
                    <template v-slot:conteudo>
                        <table-component></table-component>   
                    </template>

                    <template v-slot:rodape>
                        <button type="button" class="btn btn-primary btn-sm float-right"  data-toggle="modal" data-target="#modalMarca">
                             Adicionar
                        </button>
                    </template>
                </card-component>
                <!-- fim -->

            </div>
        </div>

        <modal-component id="modalMarca" titulo="Adicionar Marca">
            <!-- Alertas  -->
            <template v-slot:alertas>
                <alert-component tipo="success" :detalhes="transacaoDetalhes" titulo="Cadastro realizado com sucesso" v-if="transacaoStatus == 'adicionado'"></alert-component>
                <alert-component tipo="danger" :detalhes="transacaoDetalhes" titulo="Erro ao tentacar cadastrar a marca" v-if="transacaoStatus == 'erro'"></alert-component>
            </template>

            <template v-slot:conteudo>
                <div class="form-group">
                    <input-container-component titulo="Nome da marca" id="novoNome" id-help="novoNomeHelp" texto-ajuda="Opcional. Informe o nome da marca">
                        <input type="text" class="form-control" id="novoNome" aria-describedby="novoNomeHelp" placeholder="Nome da marca" v-model="nomeMarca" />
                    </input-container-component>  
                </div>

                <div class="form-group">
                    <input-container-component titulo="Imagem" id="novoImagem" id-help="novoImagemHelp" texto-ajuda="Selecione um imagem no formato png">
                        <input type="file" class="form-control-file" id="novoImagem" aria-describedby="novoImagemHelp" placeholder="Selecione uma imagem" @change="carregarImagem($event)"/>
                    </input-container-component>
                </div>
            </template>

            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" @click="salvar()">Salvar</button>
            </template>
        </modal-component>
    </div>
</template>

<script>
    export default {
        computed: {
            token() {
                let token = document.cookie.split(';').find(indice => {
                    return indice.includes('token=');
                })
                .split('=')[1]; 
                // split('=')[1] separa o token no caractér '=' e pega a segundo posição (no caso [1]) do array retornado
                // recuperando apenas o token para posteriormente adicionar a palavra Bearer na propriedade Authorization 
                // das requisições junto do token. 
                return 'Bearer ' + token ;
            }
        },
        data()  {
            return {
                urlBase: 'http://localhost:8000/api/v1/marca',
                nomeMarca: '',
                arquivoImagem: [],
                transacaoStatus: '',
                transacaoDetalhes: []
            }
        },
        methods: {
            carregarImagem(e) {
                this.arquivoImagem = e.target.files;
            },
            salvar() {
                let formData = new FormData();
                formData.append('nome', this.nomeMarca);
                formData.append('imagem', this.arquivoImagem[0]);

                let configuracao = {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'Accept': 'application/json',
                        'Authorization': this.token
                    }
                }
    
                axios.post(this.urlBase, formData, configuracao)
                    .then(response => {
                        this.transacaoStatus = 'adicionado';
                        this.transacaoDetalhes = response
                    })
                    .catch(error => {
                        this.transacaoStatus = 'erro';
                        this.transacaoDetalhes = error.response
                        // console.log(error.response.data.message)
                    })
            }
        }
    }
</script>