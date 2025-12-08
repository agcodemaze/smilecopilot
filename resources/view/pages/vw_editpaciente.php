<?php
date_default_timezone_set('America/Sao_Paulo');
$dataHoraServidor = date('Y-m-d H:i:s'); 
?>


<!-- Start Content-->
<div class="container-fluid" style="max-width:95% !important; padding-left:20px; padding-right:20px;">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">        
            <div class="page-title-box">
                <div class="page-title-right">
                </div>
                <h4 class="page-title">
                  <a href="javascript:history.back()" class="text-decoration-none me-2">
                    ← Voltar
                  </a>
                  <?= \App\Core\Language::get('cadastro_paciente'); ?></h4>
            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="row">
                <div class="col-sm-12">
                    <!-- Profile -->
                    <div class="card bg-primary">
                        <div class="card-body profile-user-box">
                            <div class="row">
                                <div class="col-sm-8">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <div class="avatar-lg position-relative d-inline-block">
                                                <img src="../../../public/assets/images/users/avatar-10.jpg" alt="" class="rounded-circle img-thumbnail">
                                                <!-- Botão pequeno com ícone de câmera -->
                                                <button type="button" class="btn btn-sm btn-primary position-absolute bottom-0 start-50 translate-middle-x" 
                                                        style="font-size: 12px; padding: 4px 6px;">
                                                    <i class="ri-camera-line"></i>
                                                </button>
                                            </div>                                                                                                  </div>
                                        <div class="col">
                                            <div>
                                                <h4 class="mt-1 mb-1 text-white">Aline Medeiros dos Santos</h4>
                                                <p class="font-13 text-white-50">
                                                    <i class="ri-whatsapp-line"></i> <!-- pequeno espaço após o ícone -->
                                                    (11) 98273-4350  
                                                    <span class="ms-2"><?= \App\Core\Language::get('informacoes_basicas'); ?>: 049.967.919-93</span> <!-- leve espaço antes do CPF -->
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- end col-->
                            </div> <!-- end row -->
                        </div> <!-- end card-body/ profile-user-box-->
                    </div><!--end profile/ card -->
                </div> <!-- end col-->
    </div>
    <!-- end row -->  

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a href="#infobasica" data-bs-toggle="tab" aria-expanded="false" class="nav-link active">
                <i class="mdi mdi-home-variant d-md-none d-block"></i>
                <span class="d-none d-md-block">Informações Básicas</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="#evolucao" data-bs-toggle="tab" aria-expanded="true" class="nav-link">
                <i class="mdi mdi-rocket-launch-outline d-md-none d-block"></i>
                <span class="d-none d-md-block">Evolução Consultas</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="#prontuario" data-bs-toggle="tab" aria-expanded="true" class="nav-link">
                <i class="mdi mdi-account-circle d-md-none d-block"></i>
                <span class="d-none d-md-block">Prontuário</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="#anamnese" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                <i class="mdi mdi-clipboard-list-outline d-md-none d-block"></i>
                <span class="d-none d-md-block">Anamnese</span>
            </a>
        </li>
    </ul>
    
    <div class="tab-content">
        <!-- ------- ABA INFO BASIC -------- --> 
        <div class="tab-pane show active" id="infobasica">
            <div class="row">
                <div class="col-12">
                <form class="needs-validation" id="form" name="form" role="form" method="POST" enctype="multipart/form-data" novalidate>
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title"><?= \App\Core\Language::get('informacoes_basicas'); ?></h4>
                            <p class="text-muted font-14">
                                <?= \App\Core\Language::get('secao_cad_basico'); ?>
                            </p>                
                            <div class="tab-content">
                                <div class="tab-pane show active" id="input-types-preview"> 
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="nome" class="form-label"><?= \App\Core\Language::get('nome_completo'); ?></label>
                                                <input value="<?= htmlspecialchars($pacienteInfo['PAC_DCNOME']) ?>" type="text" id="nome" name="nome" class="form-control" style="text-transform: uppercase;">
                                            </div>
                                            <div class="mb-3">
                                                <label for="sexo" class="form-label"><?= \App\Core\Language::get('sexo'); ?></label>                                         
                                                <select class="form-select" id="sexo" name="sexo" required>
                                                    <option value=""><?= \App\Core\Language::get('selecione'); ?></label> </option>
                                                    <option value="MASCULINO"><?= \App\Core\Language::get('masculino'); ?></option>
                                                    <option value="FEMININO"><?= \App\Core\Language::get('feminino'); ?></option>
                                                </select>
                                            </div>
                                        </div> <!-- end col -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="telefone" class="form-label"><?= \App\Core\Language::get('telefone_whatsapp'); ?></label>
                                                <input value="<?= htmlspecialchars($pacienteInfo['PAC_DCTELEFONE']) ?>" type="text" id="telefone" name="nomtelefonee" class="form-control" data-toggle="input-mask" data-mask-format="(00) 00000-0000">
                                            </div>
                                            <div class="mb-3">
                                                <label for="dtnascimento" class="form-label"><?= \App\Core\Language::get('data_nascimento'); ?></label> 
                                                <input value="<?= htmlspecialchars($pacienteInfo['PAC_DTDATANASC']) ?>" type="text" id="dtnascimento" name="dtnascimento" class="form-control" data-toggle="input-mask" data-mask-format="00/00/0000">
                                            </div>
                                        </div> <!-- end col -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="email" class="form-label"><?= \App\Core\Language::get('email'); ?></label>
                                                <input value="<?= htmlspecialchars($pacienteInfo['PAC_DCEMAIL']) ?>" type="text" id="email" name="email" class="form-control" style="text-transform: uppercase;">
                                            </div>
                                            <div class="mb-3">
                                                <label for="documento" class="form-label"><?= \App\Core\Language::get('cpfrg'); ?></label></label>
                                                <input value="<?= htmlspecialchars($pacienteInfo['PAC_DCCPF']) ?>" type="text" id="documento" name="documento" class="form-control" style="text-transform: uppercase;">
                                            </div>
                                        </div> <!-- end col -->
                                    </div>
                                    <!-- end row-->
                                </div> <!-- end preview-->
                            </div> <!-- end tab-content-->
                        </div> <!-- end card-body -->
                    </div> <!-- end card -->

                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title"><?= \App\Core\Language::get('informacoes_endereco'); ?> </h4> 
                            <p class="text-muted font-14"> 
                                <?= \App\Core\Language::get('descricao_informacoes_endereco'); ?>
                            </p>
                            <div class="tab-content">
                                <div class="tab-pane show active" id="input-types-preview">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="cep" class="form-label">CEP <a class="small-text" href="https://buscacepinter.correios.com.br/app/localidade_logradouro/index.php" target="_blank"> [não sei informar]</a></label>
                                                <input value="<?= htmlspecialchars($pacienteInfo['PAC_DCENDERECO_CEP']) ?>" type="text" id="cep" name="cep" class="form-control" data-toggle="input-mask" data-mask-format="00000-000" placeholder="Digite o CEP" maxlength="9" onblur="buscarEndereco()">
                                            </div>
                                            <div class="mb-3">
                                                <label for="endereco" class="form-label">Endereço</label>
                                                <input value="<?= htmlspecialchars($pacienteInfo['PAC_DCENDERECO_RUA']) ?>" type="text" id="endereco" name="endereco" class="form-control" style="text-transform: uppercase;" placeholder="" readonly>
                                            </div>
                                        </div> <!-- end col -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="numero" class="form-label">Número</label>
                                                <input value="<?= htmlspecialchars($pacienteInfo['PAC_DCENDERECO_NUMERO']) ?>" type="text" id="numero" name="numero" class="form-control" style="text-transform: uppercase;" placeholder="Digite o número">
                                            </div>
                                            <div class="mb-3">
                                                <label for="bairro" class="form-label">Bairro</label>
                                                <input value="<?= htmlspecialchars($pacienteInfo['PAC_DCENDERECO_BAIRRO']) ?>" type="text" id="bairro" name="bairro" class="form-control" style="text-transform: uppercase;" placeholder="" readonly>
                                            </div>
                                        </div> <!-- end col -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="cidade" class="form-label">Cidade</label>
                                                <input value="<?= htmlspecialchars($pacienteInfo['PAC_DCENDERECO_CIDADE']) ?>" type="text" id="cidade" name="cidade" class="form-control" style="text-transform: uppercase;" placeholder="" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label for="estado" class="form-label">Estado</label>
                                                <input value="<?= htmlspecialchars($pacienteInfo['PAC_DCENDERECO_ESTADO']) ?>" type="text" id="estado" name="estado" class="form-control" style="text-transform: uppercase;" placeholder="" readonly>
                                            </div>
                                        </div> <!-- end col -->
                                    </div>
                                    <!-- end row-->
                                </div> <!-- end preview-->
                            </div> <!-- end tab-content-->
                        </div> <!-- end card-body -->
                    </div> <!-- end card -->
                    
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title"><?= \App\Core\Language::get('informacoes_administrativas'); ?> </h4> 
                            <p class="text-muted font-14"> 
                                <?= \App\Core\Language::get('descricao_administrativas'); ?>
                            </p>
                            <div class="tab-content">
                                <div class="tab-pane show active" id="input-types-preview">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="nomeconvenio" class="form-label"><?= \App\Core\Language::get('nome_convenio'); ?></label>                                        
                                                <select class="form-control select2" id="nomeconvenio" name="nomeconvenio" data-toggle="select2">
                                                    <option value=""><?= \App\Core\Language::get('selecione'); ?></option>
                                                    <?php foreach ($convenios as $convenio): ?>
                                                        <option value="<?= $convenio['CNV_IDCONVENIO'] ?>">
                                                            <?= strtoupper($convenio['CNV_DCCONVENIO']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="dtcadastro" class="form-label"><?= \App\Core\Language::get('cliente_desde'); ?></label>
                                                <input value="<?= htmlspecialchars($pacienteInfo['PAC_DTCADASTRO']) ?>" type="text" id="dtcadastro" name="dtcadastro" class="form-control">
                                            </div>
                                        </div> <!-- end col -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="plano" class="form-label"><?= \App\Core\Language::get('plano_produto'); ?></label>
                                                <input value="<?= htmlspecialchars($pacienteInfo['CNV_IDCONVENIO']) ?>" type="text" id="plano" name="plano" class="form-control" style="text-transform: uppercase;">
                                            </div>
                                            <div class="mb-3">
                                                <label for="comoconheceu" class="form-label"><?= \App\Core\Language::get('como_nos_conheceu'); ?></label>                                         
                                                <select class="form-select" id="comoconheceu" name="comoconheceu" required>
                                                    <option value=""><?= \App\Core\Language::get('selecione'); ?></option>
                                                    <option value="GOOGLE"><?= \App\Core\Language::get('google'); ?></option>
                                                    <option value="INSTAGRAM"><?= \App\Core\Language::get('instagram'); ?></option>
                                                    <option value="FACEBOOK"><?= \App\Core\Language::get('facebook'); ?></option>
                                                    <option value="YOUTUBE"><?= \App\Core\Language::get('youtube'); ?></option>
                                                    <option value="APP"><?= \App\Core\Language::get('app_convenio'); ?></option>
                                                    <option value="INDICACAO"><?= \App\Core\Language::get('paciente_indicou'); ?></option>
                                                    <option value="OUTRO"><?= \App\Core\Language::get('outro'); ?></option>
                                                </select>
                                            </div>
                                        </div> <!-- end col -->
                                        <div class="col-lg-4">
                                            <div class="mb-3">
                                                <label for="numerocarteirinha" class="form-label"><?= \App\Core\Language::get('numero_carteirinha'); ?></label>
                                                <input type="text" id="numerocarteirinha" name="numerocarteirinha" class="form-control" style="text-transform: uppercase;">
                                            </div>
                                            <div class="mb-3">
                                                <label for="observacoes" class="form-label"><?= \App\Core\Language::get('observações'); ?></label>
                                                <input type="text" id="observacoes" name="observacoes" class="form-control">
                                            </div>
                                        </div> <!-- end col -->
                                    </div>
                                    <!-- end row-->
                                </div> <!-- end preview-->
                            </div> <!-- end tab-content-->
                        </div> <!-- end card-body -->
                    </div> <!-- end card -->

                    <div class="tab-content">
                        <div class="tab-pane show active" id="input-types-preview">
                            <div class="row">
                                <div class="col-lg-4">
                                    <button type="button" onclick="window.history.back()" class="btn btn-danger"><i class="mdi mdi-keyboard-backspace me-1"></i> <span><?= \App\Core\Language::get('voltar'); ?></span> </button>
                                    <button type="button" class="btn btn-info"><i class="mdi mdi-content-save-outline me-1"></i> <span><?= \App\Core\Language::get('salvar'); ?></span> </button>
                                </div> <!-- end col -->
                            </div> <!-- end row-->
                        </div> <!-- end preview-->
                    </div> <!-- end tab-content-->            
                </form>                                           
                </div><!-- end col -->
            </div><!-- end row -->
        </div>
        <!-- ------- ABA INFO BASIC -------- --> 

        <!-- ------- ABA EVOLUÇÃO CONSULTA -------- -->                                                 
        <div class="tab-pane" id="evolucao">
            <div class="row">
                        <div class="col-12">
                            <div class="timeline" dir="ltr">
                            <?php $ballonDirect = "left"; ?>
                            <?php foreach ($pacienteInfoConsultas as $consultas): ?>

                                <?php                                    
                                    $dataFormatada = "";
                                    $consultaData = $consultas["CON_DTCONSULTA"];
                                    $data = new DateTime($consultaData);
                                    $meses = [
                                        1 => 'janeiro',
                                        2 => 'fevereiro',
                                        3 => 'março',
                                        4 => 'abril',
                                        5 => 'maio',
                                        6 => 'junho',
                                        7 => 'julho',
                                        8 => 'agosto',
                                        9 => 'setembro',
                                        10 => 'outubro',
                                        11 => 'novembro',
                                        12 => 'dezembro'
                                    ];

                                    $dia = $data->format('d');
                                    $mes = $meses[(int)$data->format('m')]; 
                                    $ano = $data->format('Y');

                                    $dataFormatada = "$dia de $mes, $ano";
                                    $dataFormatadaCenter = $data->format('d/m/Y');

                                    if(empty($consultas["CON_DCOBSERVACOES"])) {
                                        $consultas["CON_DCOBSERVACOES"] = \App\Core\Language::get('nenhuma_obs');
                                    }

                                    if ($consultas['CON_ENSTATUS'] == "CONCLUIDA") {
                                        $classeBadge = "secondary";
                                    } elseif ($consultas['CON_ENSTATUS'] == "CANCELADA") {
                                        $classeBadge = "warning";
                                    } elseif ($consultas['CON_ENSTATUS'] == "AGENDADA") {
                                        $classeBadge = "primary";
                                    } elseif ($consultas['CON_ENSTATUS'] == "FALTA") {
                                        $classeBadge = "danger";
                                    } elseif ($consultas['CON_ENSTATUS'] == "CONFIRMADA") {
                                        $classeBadge = "success";
                                    }

                                ?>
                                <div class="timeline-show my-3 text-center">
                                    <h5 class="m-0 time-show-name"><?= htmlspecialchars($dataFormatadaCenter) ?></h5>
                                </div>

                                <div class="timeline-lg-item timeline-item-<?= htmlspecialchars($ballonDirect) ?>">
                                    <div class="timeline-desk">
                                        <div class="timeline-box">
                                            <span class="arrow-alt"></span>
                                            <span class="timeline-icon"><i class="mdi mdi-adjust"></i></span>

                                            <p class="text-muted"><small><?= htmlspecialchars($dataFormatada) ?></small></p>
                                            <p><?= htmlspecialchars($consultas["CON_DCOBSERVACOES"]) ?></p>
                                            <div class="d-flex">                                               
                                                <div>
                                                    <h5 class="mt-1 font-14 mb-0">
                                                        Dentista: <small> <?= htmlspecialchars($consultas["DEN_DCNOME"]) ?></small> 
                                                    </h5>
                                                </div>
                                            </div>
                                            <span class="badge badge-<?= $classeBadge; ?>-lighten"><?= htmlspecialchars(ucwords(strtolower((string)$consultas['CON_ENSTATUS'])), ENT_QUOTES, 'UTF-8') ?></span>
                                        </div>
                                    </div>
                                </div>
                                <?php $ballonDirect = ($ballonDirect === "left") ? "right" : "left"; ?>
                            <?php endforeach; ?>



                            </div>
                            <!-- end timeline -->
                        </div> <!-- end col -->
            </div><!-- end row -->
        </div>
         <!-- ------- ABA EVOLUÇÃO CONSULTA -------- -->     

        <!-- ------- ABA PRONTUÁRIO -------- -->                                                 
        <div class="tab-pane" id="prontuario">
            <p>...</p>
        </div>
        <!-- ------- ABA PRONTUÁRIO -------- -->   
        
        <!-- ------- ABA ANAMNESE -------- -->   
        <div class="tab-pane" id="anamnese">
            <div class="row">
                <div class="col-12">

                
<form class="needs-validation" id="form" name="form" role="form" method="POST" enctype="multipart/form-data" novalidate>

<div class="card">
    <div class="card-body">
        <h4 class="header-title">Anamnese Completa</h4>
        <p class="text-muted font-14">Responda às perguntas abaixo sobre seu histórico de saúde geral e bucal.</p>

        <div class="tab-content">
            <div class="tab-pane show active" id="input-types-preview">
                <div class="row">
                    <a href="<?= $pdfUrl ?>" target="_blank">download amneses</a>
                   
                </div>
            </div>
        </div>
    </div>
</div>



    <!-- Botões -->
    <div class="tab-content">
        <div class="tab-pane show active" id="input-types-preview">
            <div class="row">
                <div class="col-lg-4">
                    <button type="button" onclick="window.history.back()" class="btn btn-danger">
                        <i class="mdi mdi-keyboard-backspace me-1"></i> <span>Voltar</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>




                </div><!-- end col -->
            </div><!-- end row -->
        </div>
        <!-- ------- ABA ANAMNESE -------- -->   
    </div> <!-- close content -->   
</div> <!-- close fluid -->   
<br>

    <script>
        function buscarEndereco() {
            var cep = document.getElementById('cep').value.replace(/\D/g, '');
        
            if (cep.length !== 8) {
                alert('CEP inválido!');
                return;
            }
        
            fetch('https://viacep.com.br/ws/' + cep + '/json/')
            .then(response => response.json())
            .then(data => {
                if (data.erro) {
                    alert('CEP não encontrado!');
                } else {
                    document.getElementById('endereco').value = data.logradouro;
                    document.getElementById('bairro').value = data.bairro;
                    document.getElementById('cidade').value = data.localidade;
                    document.getElementById('estado').value = data.uf;
                }
            })
            .catch(error => {
                console.error('Erro ao buscar o CEP:', error);
                alert('Erro ao buscar o CEP!');
            });
        }
    </script>
