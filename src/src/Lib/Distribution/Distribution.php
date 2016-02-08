<?php

namespace App\Lib\Distribution;

use Cake\ORM\TableRegistry;

class Distribution
{
    public static function generateDistribution($clazzes, $teachers)
    {
		foreach($clazzes as $clazz){
			if(!empty($clazz->teachers)){
				continue;
			}
			$teachersByKnowledge = self::getTeacherByKnowledge($clazz['subject']['knowledge_id'], $teachers);
			if(count($teachersByKnowledge) > 0){
				//Pega o primeiro porque não implementamos uma seleção baseado em critérios 
				//no futuro podemos implementar porém o stakeholder disse que não era ecenssial.
				$clazz->teachers =[];
				$clazz->teachers[] = $teachersByKnowledge[0];
			}else{
				$clazz->teachers =[];
				$clazz->teachers[] = self::randomTeacher($teachers->toArray());
			}
		}
		
        return $clazzes;
    }

	public static function getTeacherByKnowledge($knowledgeId, $teachers){
		$teachersByKnowledge = [];
		foreach($teachers as $teacher){
			foreach($teacher->knowledges as $knowledge){
				if($knowledge->id == $knowledgeId){
					$teachersByKnowledge[] = $teacher;
				}
			}
		}
		return $teachersByKnowledge;
	}
	
	public static function randomTeacher($teachers){
		return $teachers[rand(0, count($teachers) - 1)];
	}
}