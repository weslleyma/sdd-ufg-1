<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

/**
 * Teachers Controller
 *
 * @property \App\Model\Table\TeachersTable $Teachers
 */
class TeachersController extends AppController
{
	
	public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set('teachers', $this->paginate($this->Teachers->find('all')->contain(['Users'])));
        $this->set('_serialize', ['teachers']);
    }

    /**
     * View method
     *
     * @param string|null $id Teacher id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $teacher = $this->Teachers->get($id, [
            'contain' => ['Users', 'Clazzes', 'Clazzes.Subjects'
			, 'Clazzes.Locals', 'Clazzes.Processes', 'Knowledges', 'Roles', 'Roles.Knowledges']
        ]);
        $this->set('teacher', $teacher);
        $this->set('_serialize', ['teacher']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
    		$teacher = $this->Teachers->newEntity();

    		if ($this->request->is('post')) {

    			$data = $this->request->data;
    			$data['user']['is_admin'] = isset($this->request->data['user']['is_admin']) ? 1 : 0;

    			$teacher = $this->Teachers->newEntity($data, [
    				'associated' => ['Users' => ['validate' => 'default']]
    			]);

            if ($this->Teachers->save($teacher)) {
                $this->Flash->success(__('The teacher has been saved.'));
                return $this->redirect(['action' => 'edit', $teacher->id]);
            } else {
                $this->Flash->error(__('The teacher could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('teacher'));

        $this->loadModel('Knowledges');
        $knowledges = $this->Knowledges->find('list',array('fields'=>array('id','name')));
        $this->set(compact('knowledges'));

        $this->set('_serialize', ['teacher']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Teacher id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $teacher = $this->Teachers->get($id, [
            'contain' => ['Users'
				, 'Clazzes'
				, 'Clazzes.Subjects'
				, 'Clazzes.Subjects.Knowledges'
				, 'Clazzes.Subjects.Courses'
			]
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {

			$data = $this->request->data;
			$data['user']['is_admin'] = isset($this->request->data['user']['is_admin']) ? 1 : 0;

			if (!empty($this->request->data['pwd'])) {
				$data['user']['password'] = $data['pwd'];
				unset($data['pwd']);
			}

			$teacher = $this->Teachers->patchEntity($teacher, $data, [
				'associated' => ['Users' => ['validate' => 'default']]
			]);

            if ($this->Teachers->save($teacher)) {
                $this->Flash->success(__('The teacher has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The teacher could not be saved. Please, try again.'));
            }
        }

        $this->loadModel('Knowledges');
        $knowledges = $this->Knowledges->find('list',array('fields'=>array('id','name')));
        $this->set(compact('knowledges'));

        $this->set(compact('teacher'));
        $this->set('_serialize', ['teacher']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Teacher id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $teacher = $this->Teachers->get($id);
        if ($this->Teachers->delete($teacher)) {
            $this->Flash->success(__('The teacher has been deleted.'));
        } else {
            $this->Flash->error(__('The teacher could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

	
	/**
     * Allocate Clazzes method
     *
     * @param string|null $id Teacher id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
	public function allocateClazzes($id = null, $clazz_id = null, $allocate = false)
	{
		$table_clazzes_teachers = TableRegistry::get('ClazzesTeachers');
		$table_processes = TableRegistry::get('Processes');
		
		$teacher = $this->Teachers->get($id, [
            'contain' => ['Users'
				, 'Clazzes'
				, 'Clazzes.Subjects'
				, 'Clazzes.Subjects.Knowledges'
				, 'Clazzes.Subjects.Courses'
			]
        ]);

		$processes = $table_processes->find('all')->where(['initial_date <= ' => 'CURDATE()', 'final_date >= ' => 'CURDATE()'])->orWhere(['status' => 'OPENED']); 

		$count = $processes->count();

		if ($count < 1) {

			$this->Flash->info(__('Não existe nenhum Processo de Distribuição de Disciplinas aberto.'));
			$this->set('process_exists', false);
			$this->set('_serialize', ['process_exists']);

			$this->set(compact('teacher'));
			$this->set('_serialize', ['teacher']);
			$this->set('clazzes', array());
			$this->set('_serialize', ['clazzes']);
			$this->set('processes', array());
			$this->set('_serialize', ['processes']);
			$this->set('process_options', array());
			$this->set('_serialize', ['process_options']);


		} else {

			$process_options = array();

			foreach($processes as $p) {
				$process_options[$p->id] = $p->name;
			}

			$clazzes = $this->getClazzes(current(array_keys($process_options)));
	
			if ($this->RequestHandler->accepts('ajax')) {

				$this->response->disableCache();
				
				if ($id != null && $clazz_id != null && $allocate == 'allocate') {

					$query = $table_clazzes_teachers->query();
					$query->delete()->where([
							'clazz_id' => $clazz_id,
							'teacher_id' => $id
					])->execute();
					
					$query = $table_clazzes_teachers->query();
					$query->insert(['clazz_id', 'teacher_id'])->values([
							'clazz_id' => $clazz_id,
							'teacher_id' => $id
						])->execute();
					
					if ($query) {
						echo 'success';						
					} else {
						echo 'error';	
					}
					
					die();
				} else if ($id != null && $clazz_id != null && $allocate == 'deallocate') {
					
					$query = $table_clazzes_teachers->query();
					$query->delete()->where([
							'clazz_id' => $clazz_id,
							'teacher_id' => $id
					])->execute();
					
					if ($query) {
						echo 'success';						
					} else {
						echo 'error';	
					}
					die();			
				}

			}

			/* Filters */
			if ($this->request->is('post')) {
				$data = $this->request->data;
				$clazzes = $this->getClazzes($data['process'], $data);
				echo json_encode($clazzes);
				die();
			}

			$this->set(compact('teacher'));
			$this->set('_serialize', ['teacher']);
			$this->set('clazzes', $clazzes);
			$this->set('_serialize', ['clazzes']);
			$this->set('processes', $process_options);
			$this->set('_serialize', ['processes']);
			$this->set('process_exists', true);
			$this->set('_serialize', ['process_exists']);
		}


	}

	
	/**
     * Get Clazzes method
     *
     * @param array|null $params Filters.
     * @return paginated data.
     */

    private function getClazzes($process_id, $params = null) 
    {	

		$this->loadModel('Clazzes');
		$this->loadModel('ClazzesLocalsSchedules');
		
		if ($params === null) {

			return $this->paginate($this->Clazzes->find()
				->where(['process_id' => $process_id])
				->contain(['Subjects', 'Subjects.Knowledges', 'Subjects.Courses', 'Locals', 'Schedules', 'ClazzesSchedulesLocals.Locals', 'ClazzesSchedulesLocals.Schedules'])
			);
		
		} else {
			
			return $this->paginate($this->Clazzes->find('all')
				->where(['process_id' => $process_id])
				->contain(['Subjects', 'Subjects.Knowledges', 'Subjects.Courses', 'Locals', 'Schedules', 'ClazzesSchedulesLocals.Locals', 'ClazzesSchedulesLocals.Schedules'])
				->matching('Subjects', function ($q) {
						return $q->where(['Subjects.name LIKE ' => '%' . $params['subject_name'] . '%']);
				})
				->matching('Subjects.Knowledges', function ($q) {
						return $q->where(['Knowledges.name LIKE ' => '%' . $params['knowledge_name'] . '%']);
				})
				->matching('Subjects.Courses', function ($q) {
						return $q->where(['Courses.name LIKE ' => '%' . $params['course_name'] . '%']);
				})
				->matching('Locals', function ($q) {
						return $q->where(['Locals.name LIKE ' => '%' . $params['local_name'] . '%'])->orWhere([['Locals.address LIKE ' => '%' . $params['local_name'] . '%']]);;
				})
				->matching('Schedules', function ($q) {
						return $q->where(['Schedules.start_time >= ' => 'CAST(' . (int)$params['start_time']['hour'] . ':' . (int)$params['start_time']['minute'] . ' as TIME)'
										, 'Schedules.end_time <= ' => 'CAST(' . (int)$params['end_time']['hour'] . ':' . (int)$params['end_time']['minute'] . ' as TIME)']);
				})
			);
		}
	}
}