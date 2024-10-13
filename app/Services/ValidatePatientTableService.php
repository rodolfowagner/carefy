<?php

namespace App\Services;

class ValidatePatientTableService
{

    private $patients;

    public function setPatients($patients)
    {
        $this->patients = $patients;
    }
    
    private function getSamePatient($patient)
    {
        foreach ($this->patients as $patientLoop)
        {
            if ($patient['nome'] == $patientLoop['nome'] 
                AND $patient['nascimento'] == $patientLoop['nascimento']
                AND $patient['codigo'] != $patientLoop['codigo'] 
                AND $patient['id_auto'] > $patientLoop['id_auto'] // Similar: id WRERE != sameId
            )
                return $patientLoop;
        }
        return null;
    }

    private function getSameGuide($patient)
    {
        foreach ($this->patients as $patientLoop)
            if ($patient['guia'] == $patientLoop['guia'] 
                AND $patient['id_auto'] > $patientLoop['id_auto'] // Similar: id WRERE != sameId
            )
                return $patientLoop;
        return null;
    }
    
    private function getPatientHospitalizations($patient)
    {
        $hospitalizations = [];

        foreach ($this->patients as $patientLoop)
            if ($patient['nome'] == $patientLoop['nome'] 
                AND $patient['nascimento'] == $patientLoop['nascimento']
                AND $patient['codigo'] != $patientLoop['codigo'] 
                AND $patient['id_auto'] > $patientLoop['id_auto'] // Similar: id WRERE != sameId
            )
                $hospitalizations[] = $patientLoop;

        return count($hospitalizations) ? $hospitalizations : null;
    }

    public function validate()
    {
        $resultError = [];
        $resultSuccess = [];
        
        foreach ($this->patients as $patient)
        {
            $errors = [];
                
            // RN02-01: Pacientes com o mesmo NOME e NASCIMENTO com CODIGO divergente de um cadastrado previamente;
            $getSamePatient = $this->getSamePatient($patient);
            
            if ($getSamePatient)
                $errors[] = "Paciente possui outro código: " . $getSamePatient['codigo'];

            // RN02-02: Internações com o mesmo código da GUIA de internação;
            $getSameGuide = $this->getSameGuide($patient);
            if ($getSameGuide)
                $errors[] = "GUIA já existe";
                
            // RN02-03: Internações com a data de ENTRADA inferior a data de NASCIMENTO do paciente;
            if ($patient['entrada_carbon']->lt($patient['nascimento_carbon']))
                $errors[] = "Data de ENTRADA inferior a data de NASCIMENTO";

            // RN02-04: Internações com a data de SAIDA inferior ou igual a data de ENTRADA
            if ($patient['saida_carbon']->lte($patient['entrada_carbon']))
                $errors[] = "Data de SAÍDA inferior ou igual a data de ENTRADA";

            // RN02-05: Internações, do mesmo paciente, cujo período de internação (data de ENTRADA até a data de SAIDA) conflite com o período de uma internação cadastrada previamente. 
            $patientHospitalizations = $this->getPatientHospitalizations($patient);
            if ($patientHospitalizations) {
                foreach( $patientHospitalizations as $patientHospitalization) {
                    if ($patient['entrada_carbon']->between($patientHospitalization['entrada_carbon'], $patientHospitalization['saida_carbon']))
                        $errors[] = "Conflito com a data de ENTRADA";

                    if ($patient['saida_carbon']->between($patientHospitalization['entrada_carbon'], $patientHospitalization['saida_carbon']))
                        $errors[] = "Conflito com a data de SAÍDA";
                }
            }
            
            // Result
            if (count($errors))
                $resultError[] = [
                    'id_auto' => $patient['id_auto'],
                    'errors' => $errors,
                ];
            else
                $resultSuccess[] = $patient['id_auto'];
        }

        return [
            'validate' => true,
            'result_success' => count($resultSuccess) ? $resultSuccess : null,
            'result_success_total' => count($resultSuccess),
            'result_error' => count($resultError) ? $resultError : null,
            'result_error_total' => count($resultError),
        ];
    }

}