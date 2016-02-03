<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Clazzes Controller
 *
 * @property \App\Model\Table\ClazzesTable $Clazzes
 */
class ClazzesController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Processes', 'Subjects', 'ClazzesTeachers.Teachers.Users']
        ];
        $this->set('clazzes', $this->paginate($this->Clazzes));
        $this->set('_serialize', ['clazzes']);
    }

    /**
     * View method
     *
     * @param string|null $id Clazze id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $clazz = $this->Clazzes->get($id, [
            'contain' => [
                'Processes', 'Subjects', 'ClazzesTeachers.Teachers.Users',
                'ClazzesSchedulesLocals.Locals', 'ClazzesSchedulesLocals.Schedules'
            ]
        ]);
        $this->set('clazz', $clazz);
        $this->set('_serialize', ['clazz']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $clazz = $this->Clazzes->newEntity();
        if ($this->request->is('post')) {
            $clazz = $this->Clazzes->patchEntity($clazz, $this->request->data);
            if ($this->Clazzes->save($clazz)) {
                $this->Flash->success(__('Turma adicionada com sucesso.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Não foi possível adicionar a turma, tente novamente.'));
            }
        }

        $this->set('subjects', array_replace([0 => _('[Selecione]')],
            $this->Clazzes->Subjects->find('list')->toArray()));
        $this->set('processes', array_replace([0 => _('[Selecione]')],
            $this->Clazzes->Processes->find('list')->toArray()));
        $this->set(compact('clazz'));
        $this->set('_serialize', ['clazz']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Clazze id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $clazz = $this->Clazzes->get($id, [
            'contain' => ['Processes', 'Subjects']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $clazz = $this->Clazzes->patchEntity($clazz, $this->request->data);
            if ($this->Clazzes->save($clazz)) {
                $this->Flash->success(__('Turma modificada com sucesso.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Não foi possível modificar a turma, tente novamente.'));
            }
        }

        $this->set('subjects', array_replace([0 => _('[Selecione]')],
            $this->Clazzes->Subjects->find('list')->toArray()));
        $this->set('processes', array_replace([0 => _('[Selecione]')],
            $this->Clazzes->Processes->find('list')->toArray()));
        $this->set(compact('clazz'));
        $this->set('_serialize', ['clazz']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Clazze id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $clazze = $this->Clazzes->get($id);
        if ($this->Clazzes->delete($clazze)) {
            $this->Flash->success(__('Turma removida com sucesso.'));
        } else {
            $this->Flash->error(__('Não foi possível remover a turma, tente novamente.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
