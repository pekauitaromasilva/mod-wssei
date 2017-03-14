<?
require_once dirname(__FILE__).'/../../../SEI.php';

class MdWsSeiAcompanhamentoRN extends InfraRN {

    protected function inicializarObjInfraIBanco(){
        return BancoSEI::getInstance();
    }

    public function encapsulaAcompanhamento(array $post){
        $acompanhamentoDTO = new AcompanhamentoDTO();

        if (isset($post['protocolo'])){
            $acompanhamentoDTO->setDblIdProtocolo($post['protocolo']);
        }
        if (isset($post['unidade'])){
            $acompanhamentoDTO->setNumIdUnidade($post['unidade']);
        }

        if (isset($post['grupo'])){
            $acompanhamentoDTO->setNumIdGrupoAcompanhamento($post['grupo']);
        }
        if (isset($post['usuario'])){
            $acompanhamentoDTO->setNumIdUsuarioGerador($post['usuario']);
        }
            if (isset($post['observacao'])){
            $acompanhamentoDTO->setStrObservacao($post['observacao']);
        }

        return $acompanhamentoDTO;

    }

    protected function cadastrarAcompanhamentoControlado(AcompanhamentoDTO $acompanhamentoDTO){
        try{
            $acompanhamentoRN = new AcompanhamentoRN();
            $acompanhamentoDTO->setDthGeracao(InfraData::getStrDataHoraAtual());
            $pesquisaDTO = new AcompanhamentoDTO();
            $pesquisaDTO->setOrdNumIdAcompanhamento(InfraDTO::$TIPO_ORDENACAO_DESC);
            $pesquisaDTO->setNumMaxRegistrosRetorno(1);
            $pesquisaDTO->retNumIdAcompanhamento();
            $result = $acompanhamentoRN->listar($pesquisaDTO);
            $numIdAcompanhamento = 1;
            if(!empty($result)){
                $pesquisaDTO = end($result);
                $numIdAcompanhamento = $pesquisaDTO->getNumIdAcompanhamento()+1;
            }
            $acompanhamentoDTO->setNumIdAcompanhamento($numIdAcompanhamento);
            $acompanhamentoRN->cadastrar($acompanhamentoDTO);

            return array (
                "sucesso" => true,
                "mensagem" => 'Acompanhamento realizado com sucesso!'
            );
        }catch (Exception $e){
            $mensagem = $e->getMessage();
            if($e instanceof InfraException){
                if(!$e->getStrDescricao()){
                    /** @var InfraValidacaoDTO $validacaoDTO */
                    if(count($e->getArrObjInfraValidacao()) == 1){
                        $mensagem = $e->getArrObjInfraValidacao()[0]->getStrDescricao();
                    }else{
                        foreach($e->getArrObjInfraValidacao() as $validacaoDTO){
                            $mensagem[] = $validacaoDTO->getStrDescricao();
                        }
                    }
                }else{
                    $mensagem = $e->getStrDescricao();
                }

            }
            return array (
                "sucesso" => false,
                "mensagem" => $mensagem,
                "exception" => $e
            );
        }
    }
}