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
                                        <input type="number" class="form-control" id="inputId" aria-describedby="idHelp" placeholder="ID" v-model="busca.id" />
                                    </input-container-component>
                                </div>

                                <div class="col mb-3">
                                    <input-container-component titulo="Nome" id="inputNome" id-help="nomeHelp" texto-ajuda="Opcional. Informe o nome da marca">
                                        <input type="text" class="form-control" id="inputNome" aria-describedby="nomeHelp" placeholder="Nome da marca" v-model="busca.nome" />
                                    </input-container-component>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template v-slot:rodape>
                        <button type="submit" class="btn btn-primary btn-sm float-right" @click="pesquisar()">Pesquisar</button>
                    </template>
                </card-component>
                <!-- fim -->
                
                <!-- início do card de listagem de marcas -->
                <card-component titulo="Relação de Marcas">
                    <template v-slot:conteudo>
                        <!-- sempre que necessário interpretar uma expressão utilize o v-bind assim como em :titulos="[]" -->
                        <table-component
                            :dados="marcas.data"
                            :visualizar="{ visivel: true, data_toggle: 'modal', data_target: '#modalMarcaVisualizar' }"
                            :atualizar="true"
                            :remover="{ visivel: true, data_toggle: 'modal', data_target: '#modalMarcaRemover' }"
                            :titulos="{
                                id: {titulo: 'ID', tipo: 'text'},
                                nome: {titulo: 'Nome', tipo: 'text'},
                                imagem: {titulo: 'Imagem', tipo: 'image'},
                                created_at: {titulo: 'Data Criação', tipo: 'date'}
                            }"
                        ></table-component>   
                    </template>

                    <template v-slot:rodape>
                        <div class="row">
                            <div class="col-10">
                                <paginate-component>
                                    <li v-for="l, indice in marcas.links" :class="l.active ? 'page-item active' : 'page-item'" :key="indice" @click="pagination(l)">
                                        <!-- v-html esta interpretando qualquer caracter html ou entidade html retornado por l.label -->
                                        <a class="page-link" v-html="l.label"></a>
                                    </li>
                                </paginate-component>
                            </div>
                            <div class="col">
                                <button type="button" class="btn btn-primary btn-sm float-right"  data-toggle="modal" data-target="#modalMarca">
                                    Adicionar
                                </button>
                            </div>
                        </div>
                    </template>
                </card-component>
                <!-- fim -->

            </div>
        </div>

        <!-- início do modal de inclusão de marca -->
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
        <!-- fim do modal de inclusão de marca -->

        <!-- início do modal de visualização da marca -->
        <modal-component id="modalMarcaVisualizar" titulo="Visualizar Marca">
            <template v-slot:conteudo>

                 <input-container-component v-for="valor, indice in $store.state.item" :key="indice" :titulo="indice" >
                    <input v-if="indice != 'imagem'" type="text" class="form-control" :value="valor" disabled />
                    <img v-else :src="'storage/'+valor" />
                </input-container-component>

            </template>

            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </template>
        </modal-component>
        <!-- fim do modal de visualização da marca -->

        <!-- início do modal de remoção da marca -->
        <modal-component id="modalMarcaRemover" titulo="Remover Marca">
            <!-- Alertas  -->
            <template v-slot:alertas>
                <alert-component tipo="success" :detalhes="$store.state.transacao" titulo="Registro removido com sucesso" v-if="$store.state.transacao.status == 'sucesso'"></alert-component>
                <alert-component tipo="danger" :detalhes="$store.state.transacao" titulo="Erro ao tentar remover o registro" v-if="$store.state.transacao.status == 'erro'"></alert-component>
            </template>

            <template v-slot:conteudo v-if="$store.state.transacao.status != 'sucesso'">

                <input-container-component titulo="id" >
                    <input type="text" class="form-control" :value="$store.state.item.id" disabled />
                </input-container-component>

                <input-container-component titulo="nome" >
                    <input type="text" class="form-control" :value="$store.state.item.nome" disabled />
                </input-container-component>

            </template>

            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-danger" @click="remover()" v-if="$store.state.transacao.status != 'sucesso'">Remover</button>
            </template>
        </modal-component>
        <!-- fim do modal de remoção da marca -->
    </div>
</template>

<script>
import InputContainerComponent from './InputContainerComponent.vue';
import Paginate from './Paginate.vue';
    export default {
  components: { Paginate, InputContainerComponent },
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
                urlPaginacao: '',
                urlFiltro: '',
                nomeMarca: '',
                arquivoImagem: [],
                transacaoStatus: '',
                transacaoDetalhes: [],
                marcas: { data: [] },
                busca: { 
                    id: '',
                    nome: ''
                }
            }
        },
        methods: {
            pagination(l) {
                // A condição no if é testada para evitar erros ao tentar usar previous estando na 
                // primeiro pagína ou next estando na ultima pagína
                if(l.url) {
                    this.urlPaginacao = l.url.split('?')[1];
                    this.carregarLista();
                }
            },
            carregarLista() {
                let url = this.urlBase + '?' + this.urlPaginacao + this.urlFiltro;

                let configuracao = {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': this.token
                    }
                }

                axios.get(url, configuracao)
                    .then(response => {
                        this.marcas = response.data;    
                        // console.log(this.marcas)
                    })
                    .catch(errors => {
                        console.log(erros);
                })
            },
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
                        this.transacaoDetalhes = response;
                        this.carregarLista();
                    })
                    .catch(error => {
                        this.transacaoStatus = 'erro';
                        this.transacaoDetalhes = error.response
                        // console.log(error.response.data.message)
                    })

            },
            remover() {
                let confirmacao = confirm('Tem certeza que deseja remover esse registro?');
                
                if(!confirmacao) {
                    return false;
                }                

                let formData = new FormData();
                formData.append('_method', 'delete');

                let config = {
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': this.token 
                    }
                }

                let url = this.urlBase + '/' + this.$store.state.item.id;
                
                axios.post(url, formData, config)
                    .then(response => {
                        this.$store.state.transacao.status = 'sucesso';
                        this.$store.state.transacao.data.message = response.data.msg;
                        this.carregarLista();
                    })
                    .catch(errors => {
                        this.$store.state.transacao.status = 'erro';
                        this.$store.state.transacao.data.message = errors.response.data.erro;
                    });
            },
            pesquisar() {
                let filtro = '';

                for(let chave in this.busca) {
                    if(this.busca[chave]) {
                        if(filtro != '') {
                            filtro += ';';
                        }    

                        filtro += chave + ':like:' + this.busca[chave];
                    }
                }

                if(filtro != '') {
                    this.urlPaginacao = 'page=1';
                    this.urlFiltro = '&filtro='+filtro;
                } else {
                    this.urlFiltro = '';
                }

                this.carregarLista();
            }
        },
        mounted() {
            this.carregarLista();
        }
    }
</script>