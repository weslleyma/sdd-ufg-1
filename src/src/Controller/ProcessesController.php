<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use App\Lib\Distribution\PriorityIndex;
use App\Lib\Distribution\Distribution;
use Cake\Event\Event;

/**
 * Processes Controller
 *
 * @property \App\Model\Table\ProcessesTable $Processes
 */
class ProcessesController extends AppController
{

    public function isAuthorized($user)
    {
        // Need to be logged
        $loggedActions = ['index', 'view'];
        if (in_array($this->request->action, $loggedActions) && $this->loggedUser !== false) {
            return true;
        }

        //Only allowed to clone closed process
        if (in_array($this->request->action, ['reuseProcess'])) {
            $processId = (int)$this->request->params['pass'][0];

            $process = $this->Processes->get($processId);

            if ($this->loggedUser !== false && $this->loggedUser->canAdmin()
                && $process->status == 'CLOSED'
            ) {
                return true;
            }

            return false;
        }

        return parent::isAuthorized($user);
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['prototypeDistribute']);

        $redirectToHome = ['requestAccount', 'login'];
        $authUser = isset($this->request->Session()->read('Auth')['User']) ? true : false;
        if ($authUser && in_array($this->request->params['action'], $redirectToHome)) {
            return $this->redirect('/');
        }
    }

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => []
        ];
        $this->set('processes', $this->paginate($this->Processes));
        $this->set('_serialize', ['processes']);
    }

    /**
     * View method
     *
     * @param string|null $id Process id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $process = $this->Processes->get($id, [
            'contain' => [
                'ProcessConfigurations', 'Clazzes'
            ]
        ]);
        $this->set('process', $process);
        $this->set('_serialize', ['process']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $process = $this->Processes->newEntity();
        if ($this->request->is('post')) {
            $process = $this->Processes->patchEntity($process, $this->request->data);
            $process->status = 'OPENED';
            if ($this->Processes->save($process)) {
                $this->Flash->success(__('O processo foi salvo.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Não foi possível salvar o processo. Tente novamente.'));
            }
        }
        $this->set(compact('process'));
        $this->set('_serialize', ['process']);
        $this->set('processConfigurations', $this->Processes->ProcessConfigurations->find('list'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Process id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $process = $this->Processes->get($id, [
            'contain' => ['ProcessConfigurations', 'Clazzes']
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $process = $this->Processes->patchEntity($process, $this->request->data);
            if ($this->Processes->save($process)) {
                $this->Flash->success(__('O processo foi salvo!'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Não foi possível salvar o processo. Tente novamente.'));
            }
        }
        $this->set(compact('process'));
        $this->set('_serialize', ['process']);
        $this->set('processConfigurations', $this->Processes->ProcessConfigurations->find('list'));
    }

    /**
     * Cancel method
     *
     * @param string|null $id Process id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function cancel($id = null)
    {
        $this->request->allowMethod(['post', 'cancel']);
        $process = $this->Processes->get($id);
        $process->status = 'CANCELLED';
        if ($this->Processes->save($process)) {
            $this->Flash->success(__('O processo foi cancelado com sucesso.'));
        } else {
            $this->Flash->error(__('O processo nao pode ser cancelado. Por favor, tente novamente.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Close method
     *
     * @param string|null $id Process id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function close($id = null)
    {
        $this->request->allowMethod(['post', 'close']);
        $process = $this->Processes->get($id);
        $process->status = 'CLOSED';
//        $process->dirty('processConfigurations');
        if ($this->Processes->save($process)) {
            $this->Flash->success(__('O processo foi fechado com sucesso.'));
        } else {
            $this->Flash->error(__('O processo nao pode ser fechado. Por favor, tente novamente.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    /*
        Soma o prática e teórica da disciplina da turma e divide por 16
        E vê se o valor da ao menos oq ta no workload do docente que e na maioria 8
    */

    public function prototypeDistribute()
    {
        //http://localhost:8765/processes/prototype-distribute
        $this->autoRender = false;
        $this->response->type('json');
        $clazzes = $this->Processes->Clazzes->getAllClazzesWithSubjctsTeachers();
        $teachers = TableRegistry::get('Teachers')->getAllTeachersWithKnowledge()->toArray();
        $clazzes = Distribution::generateDistribution($clazzes, $teachers);
        $this->Processes->Clazzes->setTeachersAllClazzes($clazzes);
        $clazzes = $this->Processes->Clazzes->getAllClazzesWithSubjctsTeachers();
        $this->response->body(json_encode($clazzes, JSON_PRETTY_PRINT));
    }

    public function distribute()
    {
        $clazzes = $this->Processes->Clazzes->find('all')->contain(['ClazzesTeachers.Teachers.Users', 'Locals', 'Subjects']);
        $clazzes = $this->paginate($clazzes);
        $this->set('clazzes', $clazzes);

        $teachers = $this->Processes->Clazzes->ClazzesTeachers->Teachers->find('all')->contain(['Users', 'KnowledgesTeachers']);
        $teachers = $this->paginate($teachers);
        $this->set('teachers', $teachers);

        // PEGA AS TURMAS JÁ ALOCADAS E NÃO CONFLITANTES
        $allocatedAndNonConflictingClazzes = $this->getAllocatedAndNonConflictingClazzes($clazzes);
        $this->set('allocatedAndNonConflictingClazzes', $allocatedAndNonConflictingClazzes);

        // PEGA AS TURMAS NAO ALOCADAS OU CONFLITANTES
        $conflictedAndUnallocatedClazzes = $this->getUnallocatedAndConflictingClazzes($clazzes);
        $this->set('conflictedAndUnallocatedClazzes', $conflictedAndUnallocatedClazzes);

        // PEGAR CARGA HORÁRIA ATUAL
        $teachersCurrentWorkload = $this->getTeachersCurrentWorkload($teachers, $clazzes);
        $this->set('teachersCurrentWorkload', $teachersCurrentWorkload);

        // COMPARA CARGA HORÁRIA ATUAL COM A CARGA HORÁRIA PREVISTA
        $subAndSuperAllocatedTeachers = $this->getSubAndSuperAllocatedTeachers($teachers, $clazzes);
        $this->set('subAndSuperAllocatedTeachers', $subAndSuperAllocatedTeachers);

    }

    private function getAllocatedAndNonConflictingClazzes($clazzes) {
        $allocatedAndNonConflictingClazzes = [];

        foreach ($clazzes as $clazz) {
            foreach ($clazz->intents as $intent) {
                if ($intent->status == 'SELECTED') {
                    $allocatedAndNonConflictingClazzes[$clazz->id] = ['subjectName' => $clazz->subject->name,
                        'teacherRegistry' => $intent->teacher->registry, 'userName' => $intent->teacher->user->name];
                    foreach ($clazz->locals as $local) {
                        $allocatedAndNonConflictingClazzes[$clazz->id]['locals'] = $local->name . ' [' . $local->address . ']';
                        $allocatedAndNonConflictingClazzes[$clazz->id]['schedules'] = $local->_joinData->schedule_id;
                    }

                }
            }
        }

        return $allocatedAndNonConflictingClazzes;
    }

    private function getUnallocatedAndConflictingClazzes($clazzes) {
        $conflictedAndUnallocatedClazzes = [];

        foreach ($clazzes as $clazz) {
            if (empty($clazz->intents)) {
                $conflictedAndUnallocatedClazzes[$clazz->id]['clazzeName'] = $clazz->name;
                $conflictedAndUnallocatedClazzes[$clazz->id]['subjectId'] = $clazz->subject->id;
                $conflictedAndUnallocatedClazzes[$clazz->id]['subjectName'] = $clazz->subject->name;
                $conflictedAndUnallocatedClazzes[$clazz->id]['knowledgeId'] = $clazz->subject->knowledge_id;
                $conflictedAndUnallocatedClazzes[$clazz->id]['totalSubjectWorkload'] = ($clazz->subject->theoretical_workload + $clazz->subject->practical_workload);
                $conflictedAndUnallocatedClazzes[$clazz->id]['intents'] = $clazz->intents;

            }
            foreach ($clazz->intents as $intent) {
                if ($intent->status == 'PENDING') {
                    $conflictedAndUnallocatedClazzes[$clazz->id]['clazzeName'] = $clazz->name;
                    $conflictedAndUnallocatedClazzes[$clazz->id]['subjectId'] = $clazz->subject->id;
                    $conflictedAndUnallocatedClazzes[$clazz->id]['subjectName'] = $clazz->subject->name;
                    $conflictedAndUnallocatedClazzes[$clazz->id]['knowledgeId'] = $clazz->subject->knowledge_id;
                    $conflictedAndUnallocatedClazzes[$clazz->id]['totalSubjectWorkload'] = ($clazz->subject->theoretical_workload + $clazz->subject->practical_workload);
                    $conflictedAndUnallocatedClazzes[$clazz->id]['intents'] = $clazz->intents;

                }
            }
        }

        return $conflictedAndUnallocatedClazzes;
    }

    private function getTeachersCurrentWorkload($teachers, $clazzes) {
        // Hashmap com key=idProfessor e value=cargaHoráriaAtual
        $teachersCurrentWorkload = [];

        // Inicializando o hashmap de carga horária
        foreach ($teachers as $teacher) {
            $teachersCurrentWorkload[$teacher->id] = 0;
        }

        foreach ($teachers as $teacher) {
            foreach ($clazzes as $clazz) {
                foreach ($clazz->intents as $intent) {
                    // Se o id do professor estiver em alguns dos intents da turma, soma o sumTheoreticalPratical no hashmap do professor
                    if ($teacher->id == $intent->teacher_id) {

                        // Pega a soma da carga prática e teórica da disciplina
                        $sumTheoreticalPratical = ($clazz->subject->theoretical_workload + $clazz->subject->practical_workload)/16;

                        // Pega a quantidade atual de horas que o professor já dá de aulas
                        $currentWorkload = $teachersCurrentWorkload[$teacher->id];

                        // Soma a quantidade de horas desta nova disciplina à quantidade atual já ministrada pelo professor
                        $teachersCurrentWorkload[$teacher->id] = ($currentWorkload + $sumTheoreticalPratical);
                    }
                }
            }
        }

        return $teachersCurrentWorkload;
    }

    private function getSubAndSuperAllocatedTeachers($teachers, $clazzes) {
        $subAndSuperAllocatedTeachers = [];
        $teachersCurrentWorkload = $this->getTeachersCurrentWorkload($teachers, $clazzes);

        foreach ($teachers as $teacher) {
            if ($teachersCurrentWorkload[$teacher->id] > $teacher->workload || $teachersCurrentWorkload[$teacher->id] < $teacher->workload) {
                if ($teachersCurrentWorkload[$teacher->id] > $teacher->workload) {
                    $subAndSuperAllocatedTeachers[$teacher->id]['status'] = 'SUPERALOCADO';
                } else if ($teachersCurrentWorkload[$teacher->id] < $teacher->workload) {
                    $subAndSuperAllocatedTeachers[$teacher->id]['status'] = 'SUBALOCADO';
                } else {
                    $subAndSuperAllocatedTeachers[$teacher->id]['status'] = 'NA RISCA';
                }
                $subAndSuperAllocatedTeachers[$teacher->id]['registry'] = $teacher->registry;
                $subAndSuperAllocatedTeachers[$teacher->id]['userName'] = $teacher->user->name;
                foreach ($teacher->knowledges_teachers as $knowledges) {
                    /* Chave = knowledge_id e Valor = level */
                    $subAndSuperAllocatedTeachers[$teacher->id]['knowledges'][$knowledges->knowledge_id] = $knowledges->level;
                }
                $subAndSuperAllocatedTeachers[$teacher->id]['entryDate'] = $teacher->entry_date;
            }
        }

        return $subAndSuperAllocatedTeachers;
    }

    public function simulate()
    {
        $clazzes = $this->Processes->Clazzes->find('all')->contain(['ClazzesTeachers.Teachers.Users', 'Locals', 'Subjects']);
        $clazzes = $this->paginate($clazzes);

        $teachers = $this->Processes->Clazzes->ClazzesTeachers->Teachers->find('all')->contain(['Users', 'KnowledgesTeachers']);
        $teachers = $this->paginate($teachers);

        // NÃO SOFRERAM A DISTRIBUIÇÃO AUTOMÁTICA
        $allocatedAndNonConflictingClazzes = $this->getAllocatedAndNonConflictingClazzes($clazzes);
        $this->set('allocatedAndNonConflictingClazzes', $allocatedAndNonConflictingClazzes);

        $conflictedAndUnallocatedClazzes = $this->getUnallocatedAndConflictingClazzes($clazzes);
        $teachersCurrentWorkload = $this->getTeachersCurrentWorkload($teachers, $clazzes);
        $subAndSuperAllocatedTeachers = $this->getSubAndSuperAllocatedTeachers($teachers, $clazzes);

        // Hashmap com key=ClazzId e Value=array de teacherId com os possíveis cadidatos para aquela turma
        $candidateTeachers = [];

        foreach ($conflictedAndUnallocatedClazzes as $clazzeId => $clazzeInfo) {

            // PASSO 1 - TRATANDO AS TURMAS QUE AINDA NÃO POSSUEM NENHUM INTENT
            if (empty($clazzeInfo['intents'])) {
                $aux = array();

                foreach ($subAndSuperAllocatedTeachers as $teacherId => $teacherInfo) {
                    foreach ($teacherInfo['knowledges'] as $knowledgeId => $knowledgeLevel) {
                        // Se o núcleo de conhecimento da turma for igual ao núcleo de conhecimento do professor e o mesmo não estiver superalocado, ele pode ministrar
                        if (($clazzeInfo['knowledgeId'] == $knowledgeId) && ($teacherInfo['status'] != 'SUPERALOCADO')){

                            array_push($aux, $teacherId);

                        }
                    }
                }

                // Setando o mapa $candidateTeachers
                $candidateTeachers[$clazzeId] = $aux;

            }
            else // PASSO 2 - TRATANDO AS TURMAS COM MAIS DE UMA INTENT (CONFLITO)
            {

            }

        }

        // DEFININDO A PRIORIDADE ENTRE OS CANDIDATOS - A PARTIR DO LEVEL E DA DATA DE ENTRADA DO DOCENTE
        foreach ($candidateTeachers as $clazzeId => $teacherInfo){
            if (empty($teacherInfo)) {

            } else if (count($teacherInfo) == 1) {
                // PERSISTE COMO ACEITO PARA A TURMA
            } else {
                $selectedTeacherId = $this->calculateTeacherPriorityForTheClazz($clazzeId, $teacherInfo, $teachersCurrentWorkload, $conflictedAndUnallocatedClazzes, $subAndSuperAllocatedTeachers);
                $this->set('selectedTeacherId', $selectedTeacherId);

                // PERSISTE O DOCENTE COM MÍNIMUMWORKLOAD COMO ACEITO PARA A TURMA
            }
        }


        $this->set('candidateTeachers', $candidateTeachers);
    }

    private function calculateTeacherPriorityForTheClazz($clazzeId, $teacherInfo, $teachersCurrentWorkload, $conflictedAndUnallocatedClazzes, $subAndSuperAllocatedTeachers) {

        // Professor com maior ponto, é alocado pra turma
        // Key = teacherId e Value=Priority for the clazz
        $priority = [];

        // INICIALIZANDO O VETOR DE PRIORIDADES
        foreach ($teacherInfo as $teacherId) {
            $priority[$teacherId] = 0;
        }

        // Valores de prioridade:
        //      MinimumWorkload = 1 ponto
        //      BestLevel = 1 ponto
        //      OldestEntryDate = 1 ponto

        // RECEBE O ID DO PROFESSOR
        $minWorkloadTeacherId = null;
        $minWorkload = 9999;

        $bestLevelTeacherId = null;
        $bestLevel = -1;

        $oldestEntryDateTeacherId = null;
        $oldestEntryDate = new Time('now', 'UTC');

        $knowledgeId = null;

        foreach ($teacherInfo as $teacherId) {
            if ($teachersCurrentWorkload[$teacherId] < $minWorkload) {
                $minWorkload = $teachersCurrentWorkload[$teacherId];
                $minWorkloadTeacherId = $teacherId;
            }

            $knowledgeId = $conflictedAndUnallocatedClazzes[$clazzeId]['knowledgeId'];
            if ($subAndSuperAllocatedTeachers[$teacherId]['knowledges'][$knowledgeId] > $bestLevel) {
                $bestLevel = $subAndSuperAllocatedTeachers[$teacherId]['knowledges'][$knowledgeId];
                $bestLevelTeacherId = $teacherId;
            }

            if ($subAndSuperAllocatedTeachers[$teacherId]['entryDate'] < $oldestEntryDate) {
                $oldestEntryDate = $subAndSuperAllocatedTeachers[$teacherId]['entryDate'];
                $oldestEntryDateTeacherId = $teacherId;
            }
        }

        // Vasculhando hashmap de prioridades e selecionando o professor a ser retornado
        $priority[$minWorkloadTeacherId] += 1;
        $priority[$bestLevelTeacherId] += 1;
        $priority[$oldestEntryDateTeacherId] += 1;

        $selectedTeacherId = -1;
        $maximumPoints = -1;

        foreach ($priority as $teacherId => $teacherPoints) {
            if ($teacherPoints > $maximumPoints) {
                $selectedTeacherId = $teacherId;
                $maximumPoints = $teacherPoints;
            }
        }

        $this->set('priority', $priority);
        $this->set('workload', $minWorkloadTeacherId);
        $this->set('level', $bestLevelTeacherId);
        $this->set('oldestEntryDate', $oldestEntryDate);

        return $selectedTeacherId;
    }

    public function revert()
    {
        $this->paginate = [
            'contain' => []
        ];
        $this->set('processes', $this->paginate($this->Processes));
        $this->set('_serialize', ['processes']);
    }


    public function cloneProcess($id = null)
    {
        $clonedProcess = $this->Processes->get($id, [
            'contain' => ['Clazzes']
        ]);

        if ($this->request->is('post')) {
            $process = $this->Processes->patchEntity($process, $this->request->data);
            $process->status = 'OPENED';
            if ($this->Processes->save($process)) {
                $this->Flash->success(__('O processo foi clonado.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Não foi possível clonar o processo. Tente novamente.'));
            }
        } else {
            $process = $this->Processes->newEntity();
            $process->name = $clonedProcess->name;
            $process->initial_date = $clonedProcess->initial_date;
            $process->teacher_intent_date = $clonedProcess->teacher_intent_date;
            $process->primary_distribution_date = $clonedProcess->primary_distribution_date;
            $process->substitute_intent_date = $clonedProcess->substitute_intent_date;
            $process->secondary_distribution_date = $clonedProcess->secondary_distribution_date;
            $process->final_date = $clonedProcess->final_date;
            foreach ($clonedProcess->clazzes as $clonedClazze) {
                $clazze = $this->Clazzes->newEntity();
                $clazze->name = $clonedClazze->name;
                $clazze->$vacancies = $clonedClazze->name;
                $clazze->$subject = $clonedClazze->name;
                $clazze->$schedule = $clonedClazze->name;
                $clazze->$local = $clonedClazze->name;
                $clazze->$process = $process;
                $clazze->$teachers = $clonedClazze->$teachers;

                $process->clazzes->push($clazze);
            }
            $process->clazzes = debug($clonedProcess->clazzes);
        }

        $this->set(compact('$process'));
        $this->set('_serialize', ['$process']);
    }


    public function reuseProcess($id)
    {
        $originalProcess = $this->Processes->get($id, [
            'contain' => ['Clazzes', 'Clazzes.ClazzesTeachers', 'Clazzes.ClazzesSchedulesLocals', 'ProcessesProcessConfigurations'],
        ]);

        unset($originalProcess->id);
        $originalProcess->status = 'OPENED';
        $originalProcess->name = $originalProcess->name . '(Clonado)';

        foreach ($originalProcess->clazzes as $item => $value) {

            unset($originalProcess->clazzes[$item]->id);
            unset($originalProcess->clazzes[$item]->process_id);
            $originalProcess->clazzes[$item]->isNew(true);

            foreach ($value->intents as $i => $v) {
                unset($originalProcess->clazzes[$item]->intents[$i]->clazz_id);
                $originalProcess->clazzes[$item]->intents[$i]->isNew(true);
            }

            foreach ($value->scheduleLocals as $i => $v) {
                unset($originalProcess->clazzes[$item]->scheduleLocals[$i]->clazz_id);
                $originalProcess->clazzes[$item]->scheduleLocals[$i]->isNew(true);
            }
        }

        foreach ($originalProcess->processes_process_configurations as $item => $value) {
            unset($originalProcess->processes_process_configurations[$item]->process_id);
        }

        $clonedProcess = $this->Processes->newEntity($originalProcess->toArray(),
            ['associated' => ['Clazzes', 'Clazzes.ClazzesTeachers', 'Clazzes.ClazzesSchedulesLocals', 'ProcessesProcessConfigurations']]);

        $clonedProcess->clazzes = $originalProcess->clazzes;

        if ($this->Processes->save($clonedProcess)) {
            $this->Flash->success(__('Processo clonado com sucesso!'));
        } else {
            $this->Flash->error(__('Ocorreu um erro ao tentar clonar o Processo. Por favor, tente novamente.'));
        }
        return $this->redirect(['action' => 'index']);

    }

    private function sub_or_super_allocated_teachers()
    {

    }

}
