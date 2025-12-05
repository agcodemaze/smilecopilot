<?php
    date_default_timezone_set('America/Sao_Paulo');
    $dataHoraServidor = date('Y-m-d H:i:s'); // hora atual do servidor

    $profissionalId = $_SESSION['PROFISSIONAL_ID'] ?? 'all';

    $lang = $_SESSION['lang'] ?? 'pt';

    $consultasCalendario = \App\Controller\Pages\Home::getConsultasToCalendar($profissionalId);

    $events = [];

    foreach ($consultasCalendario as $consulta) { 

        $start = $consulta['CON_DTCONSULTA'] . 'T' . $consulta['CON_HORACONSULTA'];

        $horaFim = date('H:i:s', strtotime($consulta['CON_HORACONSULTA'] . ' + ' . $consulta['CON_NUMDURACAO'] . ' minutes'));
        $end = $consulta['CON_DTCONSULTA'] . 'T' . $horaFim;

        $status = strtoupper($consulta['CON_ENSTATUS']);
        switch ($status) {
            case 'CONFIRMADA':
                $className = 'bg-success';
                break;
            case 'AGENDADA':
                $className = 'bg-info';
                break;
            case 'CANCELADA':
                $className = 'bg-warning';
                break;
            case 'FALTA':
                $className = 'bg-danger';
                break;
            default:
                $className = 'bg-secondary';
        }

        $events[] = [
            'title' => $consulta['PAC_DCNOME'],
            'status' => $consulta['CON_ENSTATUS'],
            'start' => $start,
            'end' => $end,
            'id' => $consulta['CON_IDCONSULTA'],
            'className' => $className
        ];
    }
?>
<!-- Start Content-->
<div class="container-fluid" style="max-width:100% !important; padding-left:0px; padding-right:0px;">

    <div class="row">
        <div class="col-12" style="max-width:100% !important; padding-left:0px; padding-right:0px;">
            <!-- Seção: Anamnese Médica -->
            <div class="card" style="max-width:100% !important; padding-left:0px; padding-right:0px;">
                <div class="card-body">


                    <div class="tab-content" >
                        <div class="tab-pane show active" id="input-types-preview">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">

                                        <script>
                                            function ajustarAgenda() {
                                                const pc = document.getElementById('agenda-pc');
                                                const mobile = document.getElementById('agenda-mobile');
                                            
                                                if (window.innerWidth < 768) {
                                                    // Mobile
                                                    pc.style.display = 'none';
                                                    mobile.style.display = 'block';
                                                } else {
                                                    // Desktop
                                                    pc.style.display = 'block';
                                                    mobile.style.display = 'none';
                                                }
                                            }

                                            ajustarAgenda();

                                            window.addEventListener('resize', ajustarAgenda);
                                        </script>


                                            <div class="row d-none d-md-block" id="agenda-pc">
                                                <div class="col-lg-9">
                                                    <div class="mt-4 mt-lg-0">  
                                                        <?php if ($profissionalId != "all"): ?>                                      
                                                        <div id="calendar"></div>
                                                        <?php endif; ?>
                                                        <?php if ($profissionalId == "all"): ?>                                      
                                                        <div>Selecione um dentista para exibir a agenda.</div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div> <!-- end col -->
                                            </div> <!-- end row -->

                                            <!-- AGENDA MOBILE -->
                                            <div class="agenda-mobile d-md-none p-3" id="agenda-mobile">
                                                <!-- 🔹 Controle de semana -->
                                                <div class="semana-controle d-flex justify-content-between align-items-center mb-2">
                                                    <button id="prev-week" class="btn btn-sm btn-outline-secondary">&lt;</button>
                                                    <span class="fw-bold" id="semana-label"></span>
                                                    <button id="next-week" class="btn btn-sm btn-outline-secondary">&gt;</button>
                                                </div>

                                                <!-- 🔹 Barra de dias -->
                                                <div class="dias-scroll mb-3 d-flex" id="dias-scroll"></div>

                                                <!-- 🔹 Lista de eventos -->
                                                <div id="eventos-container">
                                                <?php
                                                if (!empty($events)) {
                                                    foreach ($events as $e) {
                                                        $start = new DateTime($e['start']);
                                                        $end   = isset($e['end']) && $e['end'] ? new DateTime($e['end']) : null;
                                                    
                                                        $horaInicio = $start->format('H:i');
                                                        $horaFim = $end ? $end->format('H:i') : '';
                                                        $dataEvento = $start->format('Y-m-d');
                                                    
                                                        $nome = htmlspecialchars($e['title'] ?? 'Sem nome');
                                                        $status = strtolower($e['status'] ?? 'pendente');
                                                        $foto = !empty($e['foto']) ? htmlspecialchars($e['foto']) : '/img/avatar-default.png';
                                                    
                                                        $corStatus = match($status) {
                                                            'confirmado', 'confirmada' => '#62db5eff',
                                                            'agendado', 'agendada' => '#29d2fcff',
                                                            'cancelada' => '#ff7707ff',
                                                            'falta' => '#ff0f07ff',
                                                            default => '#6c757d'
                                                        };
                                                    
                                                        echo '
                                                        <div class="consulta-card" data-dia="'.$dataEvento.'">
                                                            <div class="linha-status" style="background: '.$corStatus.';"></div>
                                                            <div class="conteudo">
                                                                <div class="info">
                                                                    <div class="nome">'.$nome.'</div>
                                                                    <div class="detalhes">
                                                                        <i class="bi bi-clock me-1"></i> '.$horaInicio.' - '.$horaFim.'
                                                                        <span class="status" style="color: '.$corStatus.';">'.ucfirst($status).'</span>
                                                                    </div>
                                                                </div>
                                                                <div class="avatar-xs d-table">
                                                                    <span class="avatar-title bg-info-lighten rounded-circle text-info" style="border: 1px solid #4d55c5ff;">
                                                                        <i class="uil uil-user font-16"></i>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>';
                                                    }
                                                } else {
                                                    echo '<p class="text-muted text-center">Nenhuma consulta encontrada.</p>';
                                                }
                                                ?>
                                                </div>
                                            </div>

<style>
    .semana-controle button {
        padding: 0 8px;
    }
    .dias-scroll {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        gap: 5px;
        padding-bottom: 5px;
    }
    .dia {
        flex: 0 0 auto;
        min-width: 60px;
        border-radius: 8px;
        background: #f8f9fa;
        text-align: center;
        padding: 8px 5px;
        cursor: pointer;
        border: 1px solid #ddd;
        transition: background 0.2s;
    }
    .dia:hover {
        background: #e2e6ea;
    }
    .nome-dia { font-size: 12px; color: #666; }
    .numero-dia { font-size: 16px; font-weight: 700; color: #222; }
    .mes { font-size: 12px; color: #666; }

    .consulta-card { background: #fff; border-radius: 12px; display: flex; align-items: stretch; margin-bottom: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; }
    .linha-status { width: 4px; border-radius: 12px 0 0 12px; }
    .conteudo { display: flex; align-items: center; justify-content: space-between; flex: 1; padding: 10px 12px; }
    .info { display: flex; flex-direction: column; justify-content: center; }
    .nome { font-weight: 700; font-size: 15px; color: #222; margin-bottom: 2px; }
    .detalhes { font-size: 13px; color: #666; display: flex; align-items: center; gap: 5px; }
    .status { font-weight: 600; margin-left: 8px; }
    .foto img { width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 1px solid #eee; }

    @media (max-width: 576px) { .consulta-card { margin-bottom: 12px; } }
</style>

<script>
    let weekOffset = 0; // semanas em relação a hoje

    // Função para formatar data no padrão YYYY-MM-DD local
    function formatDateLocal(dia) {
        const yyyy = dia.getFullYear();
        const mm = String(dia.getMonth() + 1).padStart(2, '0'); // meses começam em 0
        const dd = String(dia.getDate()).padStart(2, '0');
        return `${yyyy}-${mm}-${dd}`;
    }

    function gerarDias() {
        const diasScroll = document.getElementById('dias-scroll');
        const semanaLabel = document.getElementById('semana-label');
        diasScroll.innerHTML = '';

        const hoje = new Date();
        hoje.setDate(hoje.getDate() + weekOffset * 7);

        // Calcula a segunda-feira da semana
        const inicioSemana = new Date(hoje);
        inicioSemana.setDate(hoje.getDate() - (hoje.getDay() === 0 ? 6 : hoje.getDay() - 1)); // segunda-feira
        const fimSemana = new Date(inicioSemana);
        fimSemana.setDate(inicioSemana.getDate() + 6); // domingo

        // Atualiza label da semana
        semanaLabel.textContent = `${inicioSemana.toLocaleDateString('pt-BR')} - ${fimSemana.toLocaleDateString('pt-BR')}`;

        for (let i = 0; i < 7; i++) {
            const dia = new Date(inicioSemana);
            dia.setDate(inicioSemana.getDate() + i);

            const nomeDia = dia.toLocaleDateString('pt-BR', { weekday: 'short' });
            const numeroDia = dia.getDate();
            const mes = dia.toLocaleDateString('pt-BR', { month: 'short' });
            const dataFull = formatDateLocal(dia);

            const div = document.createElement('div');
            div.className = 'dia';
            div.dataset.dia = dataFull;
            div.innerHTML = `<div class="nome-dia">${nomeDia}</div>
                             <div class="numero-dia">${numeroDia}</div>
                             <div class="mes">${mes}</div>`;

            div.addEventListener('click', () => {
                // Remove destaque dos dias anteriores
                document.querySelectorAll('.dia').forEach(d => d.classList.remove('dia-selecionado'));
                div.classList.add('dia-selecionado');

                // Filtra eventos pelo dia selecionado
                document.querySelectorAll('.consulta-card').forEach(card => {
                    card.style.display = card.dataset.dia === dataFull ? 'flex' : 'none';
                });
            });

            diasScroll.appendChild(div);
        }
    }

    // Navegação entre semanas
    document.getElementById('prev-week').addEventListener('click', () => { 
        weekOffset--; 
        gerarDias(); 
    });
    document.getElementById('next-week').addEventListener('click', () => { 
        weekOffset++; 
        gerarDias(); 
    });

    // Gera dias ao carregar
    gerarDias();
                                            </script>






                                        </div> <!-- end card body-->
                                    </div> <!-- end card -->
                                    <!-- Add New Event MODAL -->
                                    <div class="modal fade" id="event-modal" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form class="needs-validation" name="event-form" id="form-event" novalidate>
                                                    <div class="modal-header py-3 px-4 border-bottom-0">
                                                        <h5 class="modal-title" id="modal-title">Event</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body px-4 pb-4 pt-0">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="mb-3">
                                                                    <label class="control-label form-label">Event Name</label>
                                                                    <input class="form-control" placeholder="Insert Event Name" type="text" name="title" id="event-title" required />
                                                                    <div class="invalid-feedback">Please provide a valid event name</div>
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="mb-3">
                                                                    <label class="control-label form-label">Category</label>
                                                                    <select class="form-select" name="category" id="event-category" required>
                                                                        <option value="bg-danger" selected>Danger</option>
                                                                        <option value="bg-success">Success</option>
                                                                        <option value="bg-primary">Primary</option>
                                                                        <option value="bg-info">Info</option>
                                                                        <option value="bg-dark">Dark</option>
                                                                        <option value="bg-warning">Warning</option>
                                                                    </select>
                                                                    <div class="invalid-feedback">Please select a valid event category</div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-6">
                                                                <button type="button" class="btn btn-danger" id="btn-delete-event">Delete</button>
                                                            </div>
                                                            <div class="col-6 text-end">
                                                                <button type="button" class="btn btn-light me-1" data-bs-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-success" id="btn-save-event">Save</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div> <!-- end modal-content-->
                                        </div> <!-- end modal dialog-->
                                    </div>
                                    <!-- end modal-->

                                </div> <!-- end col-12 -->
                            </div> <!-- end row -->
                        </div> <!-- end preview-->
                    </div> <!-- end tab-content-->
                </div> <!-- end card-body -->
            </div> <!-- end card -->
        </div><!-- end col -->
    </div><!-- end row -->
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {

        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            locale: '<?= $lang; ?>',
            timeZone: 'local',
            initialView: 'timeGridWeek',
            nowIndicator: true,
            slotDuration: '00:30:00',
            slotMinTime: "07:00:00",
            slotMaxTime: "22:00:00",
            allDaySlot: false,
            editable: true,
            selectable: true,
            expandRows: true,
            height: 1800,

            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },

            buttonText: {
                today: '<?= \App\Core\Language::get("hoje"); ?>',
                month: '<?= \App\Core\Language::get("mes"); ?>',
                week: '<?= \App\Core\Language::get("semana"); ?>',
                day: '<?= \App\Core\Language::get("dia"); ?>',
                list: '<?= \App\Core\Language::get("agenda"); ?>'
            },

            events: <?= json_encode($events) ?>,

            // 🔹 Impede que dois eventos se sobreponham
            eventOverlap: false,

            // 🔹 Ao arrastar o evento
            eventDrop: function(info) {
                if (!validarDuracao(info.event)) {
                    info.revert(); // volta ao lugar original
                    alert('⏱️ A duração máxima é de 1 hora.');
                    return;
                }
                if (verificaConflito(info.event)) {
                    info.revert();
                    alert('⚠️ Já existe um evento nesse horário.');
                    return;
                }
                atualizarEvento(info.event);
            },

            // 🔹 Ao redimensionar o evento
            eventResize: function(info) {
                if (!validarDuracao(info.event)) {
                    info.revert();
                    alert('⏱️ A duração máxima é de 1 hora.');
                    return;
                }
                if (verificaConflito(info.event)) {
                    info.revert();
                    alert('⚠️ Já existe um evento nesse horário.');
                    return;
                }
                atualizarEvento(info.event);
            }
        });

        calendar.render();

        // 🔹 Função que limita a 1h
        function validarDuracao(event) {
            const start = event.start.getTime();
            const end = event.end ? event.end.getTime() : start;
            const diffMin = (end - start) / (1000 * 60);
            return diffMin <= 60;
        }

        // 🔹 Verifica se o evento entra em conflito com outro
        function verificaConflito(eventoAtual) {
            const eventos = calendar.getEvents();
            const startA = eventoAtual.start.getTime();
            const endA = eventoAtual.end ? eventoAtual.end.getTime() : startA;

            for (let ev of eventos) {
                if (ev.id === eventoAtual.id) continue; // ignora o próprio evento
                const startB = ev.start.getTime();
                const endB = ev.end ? ev.end.getTime() : startB;

                // Verifica sobreposição
                if (startA < endB && endA > startB) {
                    return true; // há conflito
                }
            }
            return false;
        }

        // 🔹 Formata data local
        function formatarDataLocal(date) {
            const pad = n => n < 10 ? '0' + n : n;
            return date.getFullYear() + '-' +
                   pad(date.getMonth() + 1) + '-' +
                   pad(date.getDate()) + ' ' +
                   pad(date.getHours()) + ':' +
                   pad(date.getMinutes()) + ':' +
                   pad(date.getSeconds());
        }

        // 🔹 Atualiza evento no servidor
        function atualizarEvento(event) {
            const dados = {
                id: event.id,
                start: formatarDataLocal(event.start),
                end: event.end ? formatarDataLocal(event.end) : ''
            };

            fetch('/updateagenda', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dados)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('✅ ' + data.message);
                } else {
                    console.error('❌ Erro ao atualizar:', data.message);
                    alert('Erro ao atualizar evento: ' + data.message);
                }
            })
            .catch(err => {
                console.error('🚫 Erro de comunicação:', err);
                alert('Erro ao comunicar com o servidor.');
            });
        }

    });
</script>

<style>
    /* Mantém altura dos slots */
    .fc-timegrid-slot {
        height: 100px; 
    }

    /* Linha lateral esquerda (coluna de horários) */
    .fc-timegrid-axis {
        border-right: 3px solid #80b5eeff; 
        padding-right: 4px;
        font-weight: 500;
        font-size: 14px;
    }

    /* Aumenta a altura mínima de cada evento */
    .fc-event-main {
        min-height: 30px;
        padding: 2px 4px;
        font-size: 12px;
        display: flex;
        align-items: center;
    }

    /* Alinha o texto do evento à esquerda */
    .fc-event-title, 
    .fc-event-time {
        text-align: left !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Reduz a distância entre hora e título */
    .fc-event-time {
        margin-right: 2px;
    }

    /* Opcional: aumenta fonte do cabeçalho */
    .fc-col-header-cell-cushion {
        font-size: 16px;
    }
    

</style>

<style>
    #calendar .fc-button {
        background-color: #17a2b8 !important; 
        border-color: #17a2b8 !important;
        color: #fff !important;
    }
    
    #calendar .fc-button:hover,
    #calendar .fc-button:focus {
        background-color: #138496 !important;
        border-color: #117a8b !important;
        color: #fff !important;
    }
    
    #calendar .fc-button.fc-button-active {
        background-color: #138496 !important;
        border-color: #117a8b !important;
        color: #fff !important;
    }
</style>