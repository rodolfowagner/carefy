<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Repositories\HospitalizationRepository;
use App\Repositories\PatientRepository;

use App\Services\ValidatePatientTableService;

use App\Helpers\FunctionHelper;
use App\Helpers\FormatHelper;

use App\Models\Patient;
use App\Models\Hospitalization;

class UploadController extends Controller
{
    private $patientRepository,
            $hospitalizationRepository;

    private $patients = [];

    public function __construct(
        PatientRepository $patientRepository,
        HospitalizationRepository $hospitalizationRepository
    ) {
        $this->patientRepository = $patientRepository;    
        $this->hospitalizationRepository = $hospitalizationRepository;    
    }

    public function upload(Request $request)
    {
        if ($request->id_auto)
        {
            foreach ($request->id_auto as $id_auto)
            {
                $this->patients[] = [
                    'is_valid' => isset($request->input['is_valid'][$id_auto]) ? true : false,
                    'id_auto' => $id_auto,
                    'nome' => trim($request->input['nome'][$id_auto]),
                    'nascimento' => trim($request->input['nascimento'][$id_auto]),
                    'codigo' => trim($request->input['codigo'][$id_auto]),
                    'guia' => trim($request->input['guia'][$id_auto]),
                    'entrada' => trim($request->input['entrada'][$id_auto]),
                    'saida' => trim($request->input['saida'][$id_auto]),
                    'nascimento_carbon' => Carbon::createFromFormat('d/m/Y', trim($request->input['nascimento'][$id_auto])),
                    'entrada_carbon' => Carbon::createFromFormat('d/m/Y', trim($request->input['entrada'][$id_auto])),
                    'saida_carbon' => Carbon::createFromFormat('d/m/Y', trim($request->input['saida'][$id_auto])),
                ];
            }
        }

        switch ($request->type)
        {
            // case 'verify_table':
            //     $service = new ValidatePatientTableService;
            //     $service->setPatients($this->patients);
            //     $result = $service->validate();
            //     break;
            // case 'verify_database':
            //     $result = $this->validateDataBase();
            //     break;

            case 'verify_table':
            case 'verify_database':
                $service = new ValidatePatientTableService;
                $service->setPatients($this->patients);
                $result = $service->validate();
                $result = $this->validateDataBase();

                $service = new ValidatePatientTableService;
                $service->setPatients($this->patients);
                $result = [
                    'table' => $service->validate(),
                    'database' => $this->validateDataBase(),
                ];

                break;
            case 'insert':
                $result = $this->insert();
                $total = isset($result['total']) ? $result['total'] : 0;
                Session::flash('success', 'Total de cadastros inseridos: ' . $total);
                break;
        }

        return $result;
    }

    private function validateDataBase()
    {
        $resultError = [];
        $resultSuccess = [];

        foreach($this->patients as $patient)
        {
            // Database
                $patientByCode = $this->patientRepository->getByCode($patient['codigo']);
                $patientByNameBirth = $this->patientRepository->getByNameBirth($patient['nome'], $patient['nascimento_carbon']->format('Y/m/d'));
                $hospitalizationByCode = $this->hospitalizationRepository->getByCode($patient['guia']);

            // Validating requirements
                
                $errors = [];

                // RN02-01: Pacientes com o mesmo NOME e NASCIMENTO com CODIGO divergente de um cadastrado previamente;
                    if ($patientByNameBirth AND $patientByNameBirth->code != $patient['codigo'])
                        $errors[] = "Paciente já cadastrado com o código: $patientByNameBirth->code";
            
                // RN02-02: Internações com o mesmo código da GUIA de internação;
                    if ($hospitalizationByCode)
                        $errors[] = "O código da GUIA de internação já existe";
                    
                // RN02-03: Internações com a data de ENTRADA inferior a data de NASCIMENTO do paciente;
                    if ($patient['entrada_carbon']->lt($patient['nascimento_carbon']))
                        $errors[] = "Data de ENTRADA inferior a data de NASCIMENTO";

                // RN02-04: Internações com a data de SAIDA inferior ou igual a data de ENTRADA
                    if ($patient['saida_carbon']->lte($patient['entrada_carbon']))
                        $errors[] = "Data de SAÍDA inferior ou igual a data de ENTRADA";

                // RN02-05: Internações, do mesmo paciente, cujo período de internação (data de ENTRADA até a data de SAIDA) conflite com o período de uma internação cadastrada previamente. 
                    if ( $patientByNameBirth AND $patientByNameBirth->hospitalizations )
                    {
                        foreach ($patientByNameBirth->hospitalizations as $hospitalizations)
                        {
                            $hospitalizationDateIn = Carbon::createFromFormat('d/m/Y', FormatHelper::invertDate($hospitalizations->date_in));
                            $hospitalizationDateOut = Carbon::createFromFormat('d/m/Y', FormatHelper::invertDate($hospitalizations->date_out));
                            
                            if ($patient['entrada_carbon']->between($hospitalizationDateIn, $hospitalizationDateOut))
                                $errors[] = "Conflito com a data de ENTRADA";

                            if ($patient['saida_carbon']->between($hospitalizationDateIn, $hospitalizationDateOut))
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
    
    private function insert()
    {
        $total = 0;

        foreach($this->patients as $patient)
        {
            
            /* toDo: Revalidar a cada nova inserção */
                // $this->validateDataBase();
            
            if ($patient['is_valid'])
            {
                try
                {
                    DB::beginTransaction();
                
                    $newPatient = new Patient();
                    $newPatient->name = $patient['nome'];
                    $newPatient->birth = $patient['nascimento_carbon']->format('Y-m-d');
                    $newPatient->code = $patient['codigo'];
                
                    $newHospitalization = new Hospitalization();
                    $newHospitalization->code = $patient['guia'];
                    $newHospitalization->date_in = $patient['entrada_carbon']->format('Y-m-d');
                    $newHospitalization->date_out = $patient['saida_carbon']->format('Y-m-d');
                
                    $newPatient->save();
                    $newPatient->hospitalizations()->save($newHospitalization);
                
                    DB::commit();
                    $total++;
                } catch (\Exception $e)
                {
                    DB::rollBack();
                    // throw new \Exception('Erro: ' . $e->getMessage());
                }
            }
        }

        return [
            'total' => $total,
        ];
    }

    public function deleteAll()
    {
        Patient::query()->forceDelete();
        Hospitalization::query()->forceDelete();

        return redirect()->route('home')->with('danger', 'Todos os registros foram apagados!');   
    }

}
