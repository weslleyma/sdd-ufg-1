<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;


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
            if(isset($this->request->data['schedules'])) {
                $schedules = json_decode($this->request->data['schedules'], true);
                $scheduleLocals = [];
                foreach($schedules as $weekDay) {
                    if($weekDay == null) {
                        continue;
                    }
                    foreach($weekDay as $schedule) {
                        $scheduleLocals[] = [
                            "schedule_id" => $schedule['schedule_id'],
                            "local_id" => $schedule['local_id'],
                            "week_day" => $schedule['week_day']
                        ];
                    }
                }
                unset($this->request->data['schedules']);
                $this->request->data['scheduleLocals'] = $scheduleLocals;
            }

            $clazz = $this->Clazzes->patchEntity($clazz, $this->request->data, [
                'associated' => ['ClazzesSchedulesLocals']
            ]);

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

        $this->set('locals', array_replace([0 => _('[Selecione]')],
            $this->Clazzes->Locals->find('list')->toArray()));
        $this->set('schedules', array_replace([0 => _('[Selecione]')],
            $this->Clazzes->Schedules->find('list')->toArray()));

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
            'contain' => ['Processes', 'Subjects', 'ClazzesSchedulesLocals']
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            if(isset($this->request->data['schedules'])) {
                $schedules = json_decode($this->request->data['schedules'], true);
                $scheduleLocals = [];

                foreach($schedules as $weekDay) {
                    if($weekDay == null) {
                        continue;
                    }
                    foreach($weekDay as $schedule) {
                        $scheduleLocalEnt = $this->Clazzes->ClazzesSchedulesLocals->newEntity([
                            "clazz_id" => $clazz->id,
                            "schedule_id" => $schedule['schedule_id'],
                            "local_id" => $schedule['local_id'],
                            "week_day" => $schedule['week_day']
                        ]);

                        $scheduleLocals[] = $scheduleLocalEnt;
                    }
                }
                unset($this->request->data['schedules']);
            }

            $clazz = $this->Clazzes->patchEntity($clazz, $this->request->data);
            if(isset($scheduleLocals)) {
                $this->Clazzes->ClazzesSchedulesLocals->deleteAll(['clazz_id' => $clazz->id]);
                $clazz->scheduleLocals = $scheduleLocals;
                $clazz->dirty('scheduleLocals', true);
            }

            if ($this->Clazzes->save($clazz)) {
                $this->Flash->success(__('Turma modificada com sucesso.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('Não foi possível modificar a turma, tente novamente.'));
            }
        }

        $schedules = [[], [], [], [], [], [], [], []];
        $schedulesIndex = 0;
        foreach($clazz->scheduleLocals as $scheduledLocal) {
            $schedules[$scheduledLocal->week_day][] = [
                "id" => $schedulesIndex++,
                "week_day" => $scheduledLocal->week_day,
                "schedule_id" => $scheduledLocal->schedule_id,
                "local_id" => $scheduledLocal->local_id
            ];
        }

        $this->set('scheduledLocals', json_encode($schedules));

        $this->set('subjects', array_replace([0 => _('[Selecione]')],
            $this->Clazzes->Subjects->find('list')->toArray()));
        $this->set('processes', array_replace([0 => _('[Selecione]')],
            $this->Clazzes->Processes->find('list')->toArray()));

        $this->set('locals', array_replace([0 => _('[Selecione]')],
            $this->Clazzes->Locals->find('list')->toArray()));
        $this->set('schedules', array_replace([0 => _('[Selecione]')],
            $this->Clazzes->Schedules->find('list')->toArray()));

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
	
	/**
	* Allocate Teacher method
	*
	*/
	public function allocateTeacher($clazz_id, $teacher_id = null, $allocate = false)
	{

		$table_clazzes_teachers = TableRegistry::get('ClazzesTeachers');
		
		$clazzesTeachers = $table_clazzes_teachers->find('all')->where(['clazz_id' => $clazz_id]);

		$clazz = $this->Clazzes->get($clazz_id, [
            'contain' => [
				'Subjects'
				, 'Subjects.Knowledges'
				, 'Subjects.Courses'
				, 'ClazzesSchedulesLocals'
			]
        ]);
		
		$teachers = $this->getTeachers();

		if ($clazz_id != null && $teacher_id != null) {
		
			if ($this->RequestHandler->accepts('ajax')) {

				$this->response->disableCache();

				if ($teacher_id != null && $clazz_id != null && $allocate == 'allocate') {

					$query = $table_clazzes_teachers->query();
					$query->update()
							->set(['status' => 'PENDING'])
							->where([
							'clazz_id' => $clazz_id,
							'teacher_id != ' => $teacher_id
					])->execute();					
				
					$query = $table_clazzes_teachers->query();
					$query->delete()->where([
							'clazz_id' => $clazz_id,
							'teacher_id' => $teacher_id
					])->execute();

					$query = $table_clazzes_teachers->query();
					$query->insert(['clazz_id', 'teacher_id', 'status'])->values([
							'clazz_id' => $clazz_id,
							'teacher_id' => $teacher_id,
							'status' => 'ACTIVE'
						])->execute();

					if ($query) {
						echo 'success';
					} else {
						echo 'error';
					}

					die();
				} else if ($teacher_id != null && $clazz_id != null && $allocate == 'deallocate') {

					$query = $table_clazzes_teachers->query();
					$query->update()
							->set(['status' => 'PENDING'])
							->where([
							'clazz_id' => $clazz_id,
							'teacher_id' => $teacher_id
					])->execute();

					if ($query) {
						echo 'success';
					} else {
						echo 'error';
					}
					die();

					if ($query) {
						echo 'success';
					} else {
						echo 'error';
					}
					die();
				}
			}
		}

		/* Filters */
		if ($this->request->is('post')) {
			$data = $this->request->data;
			$teachers = $this->getTeachers($data);
			echo json_encode($teachers);
			die();
		}

		$this->set(compact('clazz'));
		$this->set('_serialize', ['clazz']);
		$this->set('teachers', $teachers);
		$this->set('_serialize', ['teachers']);
		$this->set('clazzesTeachers', $clazzesTeachers);
		$this->set('_serialize', ['clazzesTeachers']);
	}
	
	private function getTeachers($params = null) {
		
		$this->loadModel('Teachers');

		if ($params == null) {
			return $this->paginate(
				$this->Teachers->find('all')->contain(['Users', 'Clazzes', 'Clazzes.Subjects'
				, 'Clazzes.Processes', 'Knowledges', 'Roles', 'Roles.Knowledges'])
			);
			
		} else {

			$clazz = $this->Clazzes->get($params['clazz_id']);
		
			$query = $this->Teachers->find('all')
						->where([
								'COALESCE(Teachers.registry, "") LIKE ' => '%' . $params['registry'] . '%',
								'COALESCE(Teachers.workload, "") LIKE ' => '%' . $params['workload'] . '%',
								'COALESCE(Teachers.formation, "") LIKE ' => '%' . $params['formation'] . '%',
								'COALESCE(Teachers.situation, "") LIKE ' => '%' . $params['situation'] . '%'
								])	
						->contain(['Users', 'Knowledges',
							'Clazzes', 'Clazzes.Subjects',
							'Clazzes.Processes', 'Roles', 'Roles.Knowledges'])
						->matching('Users', function($q) use ($params) {
							return $q->where(['Users.name LIKE ' => '%' . $params['name'] . '%']);
						});
			
			
			
			if ($params['only_clazz'] == 1) {
				$query->matching('ClazzesTeachers', function($q) use ($clazz) {
					return $q->where(['ClazzesTeachers.clazz_id' => $clazz->id]);
				});
			}
			
			if ($params['knowledge'] == '') {
				
				if ($params['only_knowledge'] == 0) {
					$query->leftJoinWith('Knowledges', function($q) use ($params) {
						return $q->where(['COALESCE(Knowledges.name, "") LIKE ' => '%' . $params['knowledge'] . '%']);
					});
				} else {
					$params_clazz = array();
					$params_clazz[] = $params;
					$params_clazz[] = $clazz;
					
					$query->leftJoinWith('Knowledges', function($q) use ($params_clazz) {
						return $q->where(['COALESCE(Knowledges.name, "") LIKE ' => '%' . $params_clazz[0]['knowledge'] . '%'])
							->where(['Knowledges.id' => $params_clazz[1]->knowledge]);
					});
				}
			
			
			} else {
				if ($params['only_knowledge'] == 0) {
					$query->matching('Knowledges', function($q) use ($params) {
						return $q->where(['COALESCE(Knowledges.name, "") LIKE ' => '%' . $params['knowledge'] . '%']);
					});
				} else {
					
					$query->matching('Knowledges', function($q) use ($params) {
						return $q->where(['COALESCE(Knowledges.name, "") LIKE ' => '%' . $params['knowledge'] . '%'])
							->where(['Knowledges.id' => $clazz->knowledge]);
					});
				}
			}
			
			return $query->all();
			
		}
		
	}
	
	public function listOpenedClazzes()
	{
		$table_processes = TableRegistry::get('Processes');

		$processes = $table_processes->find('all')->where(['initial_date <= ' => 'CURDATE()', 'final_date >= ' => 'CURDATE()'])->orWhere(['status' => 'OPENED']);

		$count = $processes->count();

		if ($count < 1) {

			$this->Flash->info(__('Não existe nenhum Processo de Distribuição de Disciplinas aberto.'));
			$this->set('process_exists', false);
			$this->set('_serialize', ['process_exists']);
			$this->set('clazzes', array());
			$this->set('_serialize', ['clazzes']);
			$this->set('processes', array());
			$this->set('_serialize', ['processes']);
			$this->set('process_options', array());
			$this->set('_serialize', ['process_options']);

		} else {

			$process_options = array();

			$process_options[''] = '[Selecione]';
			
			foreach($processes as $p) {
				$process_options[$p->id] = $p->name;
			}

			$clazzes = $this->getClazzes();

			/* Filters */
			if ($this->request->is('post')) {
				$data = $this->request->data;
				$clazzes = $this->getClazzes($data);
				echo json_encode($clazzes);
				die();
			}

			$this->set('clazzes', $clazzes);
			$this->set('_serialize', ['clazzes']);
			$this->set('processes', $process_options);
			$this->set('_serialize', ['processes']);
			$this->set('process_exists', true);
			$this->set('_serialize', ['process_exists']);
		}
	}
	
	
	public function getClazzes($params = null)
	{		
		$connection = ConnectionManager::get('default');
		
		if ($params === null) {

			$sql = 'SELECT 
				Clazzes.id AS `Clazzes__id`,
				Clazzes.name AS `Clazzes__name`,
				Clazzes.vacancies AS `Clazzes__vacancies`,
				Clazzes.subject_id AS `Clazzes__subject_id`,
				Clazzes.process_id AS `Clazzes__process_id`,
				Subjects.id AS `Subjects__id`,
				Subjects.name AS `Subjects__name`,
				Subjects.theoretical_workload AS `Subjects__theoretical_workload`,
				Subjects.practical_workload AS `Subjects__practical_workload`,
				Subjects.knowledge_id AS `Subjects__knowledge_id`,
				Subjects.course_id AS `Subjects__course_id`,
				Knowledges.id AS `Knowledges__id`,
				Knowledges.name AS `Knowledges__name`,
				Courses.id AS `Courses__id`,
				Courses.name AS `Courses__name`,
				Locals.id AS `Locals__id`,
				Locals.name AS `Locals__name`,
				Locals.address AS `Locals__address`,
				Locals.capacity AS `Locals__capacity`,
				ClazzesSchedulesLocals.clazz_id AS `ClazzesSchedulesLocals__clazz_id`,
				ClazzesSchedulesLocals.schedule_id AS `ClazzesSchedulesLocals__schedule_id`,
				ClazzesSchedulesLocals.local_id AS `ClazzesSchedulesLocals__local_id`,
				ClazzesSchedulesLocals.week_day AS `ClazzesSchedulesLocals__week_day`,
				Schedules.id AS `Schedules__id`,
				Schedules.start_time AS `Schedules__start_time`,
				Schedules.end_time AS `Schedules__end_time`
			FROM
				clazzes Clazzes
					INNER JOIN
				processes Processes ON (Clazzes.process_id = Processes.id AND Processes.status = "OPENED")
					INNER JOIN
				subjects Subjects ON (Subjects.id = (Clazzes.subject_id))
					INNER JOIN
				knowledges Knowledges ON (Knowledges.id = (Subjects.knowledge_id))
					INNER JOIN
				courses Courses ON (Courses.id = (Subjects.course_id))
					INNER JOIN
				clazzes_schedules_locals ClazzesSchedulesLocals ON (Clazzes.id = (ClazzesSchedulesLocals.clazz_id))
					INNER JOIN
				locals Locals ON (Locals.id = ClazzesSchedulesLocals.local_id)
					INNER JOIN
				schedules Schedules ON (Schedules.id = ClazzesSchedulesLocals.schedule_id)';
										
					
			$results = $connection->execute($sql)->fetchAll('assoc');
		
		} else {
					
			$sql = 'SELECT 
				Clazzes.id AS `Clazzes__id`,
				Clazzes.name AS `Clazzes__name`,
				Clazzes.vacancies AS `Clazzes__vacancies`,
				Clazzes.subject_id AS `Clazzes__subject_id`,
				Clazzes.process_id AS `Clazzes__process_id`,
				Subjects.id AS `Subjects__id`,
				Subjects.name AS `Subjects__name`,
				Subjects.theoretical_workload AS `Subjects__theoretical_workload`,
				Subjects.practical_workload AS `Subjects__practical_workload`,
				Subjects.knowledge_id AS `Subjects__knowledge_id`,
				Subjects.course_id AS `Subjects__course_id`,
				Knowledges.id AS `Knowledges__id`,
				Knowledges.name AS `Knowledges__name`,
				Courses.id AS `Courses__id`,
				Courses.name AS `Courses__name`,
				Locals.id AS `Locals__id`,
				Locals.name AS `Locals__name`,
				Locals.address AS `Locals__address`,
				Locals.capacity AS `Locals__capacity`,
				ClazzesSchedulesLocals.clazz_id AS `ClazzesSchedulesLocals__clazz_id`,
				ClazzesSchedulesLocals.schedule_id AS `ClazzesSchedulesLocals__schedule_id`,
				ClazzesSchedulesLocals.local_id AS `ClazzesSchedulesLocals__local_id`,
				ClazzesSchedulesLocals.week_day AS `ClazzesSchedulesLocals__week_day`,
				Schedules.id AS `Schedules__id`,
				Schedules.start_time AS `Schedules__start_time`,
				Schedules.end_time AS `Schedules__end_time`
			FROM
				clazzes Clazzes
					INNER JOIN
				processes Processes ON (Clazzes.process_id = Processes.id)
					INNER JOIN
				subjects Subjects ON (Subjects.id = (Clazzes.subject_id))
					INNER JOIN
				knowledges Knowledges ON (Knowledges.name LIKE ?
					AND Knowledges.id = (Subjects.knowledge_id))
					INNER JOIN
				courses Courses ON (Courses.id = (Subjects.course_id))
					INNER JOIN
				clazzes_schedules_locals ClazzesSchedulesLocals ON (Clazzes.id = (ClazzesSchedulesLocals.clazz_id))
					INNER JOIN
				locals Locals ON (Locals.id = ClazzesSchedulesLocals.local_id)
					INNER JOIN
				schedules Schedules ON (Schedules.id = ClazzesSchedulesLocals.schedule_id)
			WHERE
				Clazzes.process_id LIKE ?';

			$results = $connection->execute($sql, ['%' . $params['knowledge_name'] . '%', '%' . $params['process'] . '%'], ['string', 'string'])->fetchAll('assoc');

		}
		
		$formatted_results = array();
			
		$joins = array('locals' => array('Locals__id' => 'id', 'Locals__name' => 'name', 'Locals__address' => 'address'), 
						'schedules' => array('Schedules__id' => 'id', 'Schedules__start_time' => 'start_time', 'Schedules__end_time' => 'end_time'),
						'SchedulesLocals' => array('ClazzesSchedulesLocals__week_day' => 'week_day'));
		
		$formatted_results = $this->create_join_array($results, $joins);
		
		return $formatted_results;
	}

	function create_join_array($rows, $joins){

		$out = array();

		foreach((array)$rows as $row){
			if (!isset($out[$row['Clazzes__id']])) {
				$out[$row['Clazzes__id']] = $row;
			}

			foreach($joins as $name => $item){
				unset($newitem);
				foreach($item as $field => $newfield){
					unset($out[$row['Clazzes__id']][$field]);
					if (!empty($row[$field]))
						$newitem[$newfield] = $row[$field];
				}
				if (!empty($newitem))
					$out[$row['Clazzes__id']][$name][$newitem[key($newitem)]] = $newitem;
			}
		}

		return $out;

	}
}
