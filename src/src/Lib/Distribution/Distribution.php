<?php

namespace App\Lib\Distribution;

use Cake\ORM\TableRegistry;

class Distribution
{
	/**
	* Quais os pré requisitos levados em conta no momento da distribuição?
	* - É possível que dois prof. tenha a mesma disciplina ? se sim como isto
	* é explicitado ? (algum lugar diz que a disciplina pode ser dada por 2 porfs.)
	* - É levado em conta o Knowledge do prof. ? se sim como sabemos o Knowledge 
	* necessário para a disciplina ?
	* - é levado em conta somente o indice de prioridade ? (como é calculado)
	* - a distribuição é feita somente com as disciplinas sem prof. interessado ?
	* - quais os quesitos de desempate ? existe ? Em que ordem (ex: knowledge -> indice de prioridade)
	* - qual o tempo máximo de lecionamento da disciplina ? (onde esta informação esta disponivel)
	* - qual a carga horária máxima que um prof. pode ter ?
	*/
    public static function generateDistribution($clazzes, $teachers)
    {
        return $clazzes;
    }
}