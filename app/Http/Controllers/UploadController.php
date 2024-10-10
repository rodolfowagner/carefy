<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Repositories\PatientRepository;
use App\Repositories\HospitalizationRepository;

use App\Helpers\FormatHelper;

class UploadController extends Controller
{
    private $patientRepository,
            $hospitalizationRepository;

    public function __construct(PatientRepository $patientRepository, HospitalizationRepository $hospitalizationRepository)
    {
        $this->patientRepository = $patientRepository;    
        $this->hospitalizationRepository = $hospitalizationRepository;    
    }

    public function validateData(Request $request)
    {
        $items = $request->items;

        $validItems = [];
        $invalidItems = [];

        $validTotal = 0;
        $invalidTotal = 0;
        
        foreach($items as $item)
        {
            $data = json_decode($item, true);
            $error = [];
            
            // Get data
                $uuid = $data['uuid'];
                $name = $data['nome'];
                $birth = $data['nascimento'];
                $code = $data['codigo'];
                $guide = $data['guia'];
                $dateIn = $data['entrada'];
                $dateOut = $data['saida'];

            // Parse
                $birthSql = FormatHelper::invertDate($birth);
                $birthCarbon = Carbon::createFromFormat('d/m/Y', $birth);
                $dateInCarbon = Carbon::createFromFormat('d/m/Y', $dateIn);
                $dateOutCarbon = Carbon::createFromFormat('d/m/Y', $dateOut);

            // Database
                $patientByCode = $this->patientRepository->getByCode($code);
                $patientByNameBirth = $this->patientRepository->getByNameBirth($name, $birthSql);
                $hospitalizationByCode = $this->hospitalizationRepository->getByCode($guide);

            // Validating requirements

                // RN02-01: Pacientes com o mesmo NOME e NASCIMENTO com CODIGO divergente de um cadastrado previamente;
                    if ($patientByNameBirth AND $patientByNameBirth->code != $code)
                        $error[] = "Paciente já cadastrado com o código: $patientByNameBirth->code";
            
                // RN02-02: Internações com o mesmo código da GUIA de internação;
                    if ($hospitalizationByCode)
                        $error[] = "O código da GUIA de internação já existe";
                    
                // RN02-03: Internações com a data de ENTRADA inferior a data de NASCIMENTO do paciente;
                    if ($dateInCarbon->lt($birthCarbon))
                        $error[] = "Data de ENTRADA inferior a data de NASCIMENTO " . $dateInCarbon->format('d/m/Y') . ' # ' . $birthCarbon->format('d/m/Y');

                // RN02-04: Internações com a data de SAIDA inferior ou igual a data de ENTRADA
                    if ($dateOutCarbon->lte($dateInCarbon))
                        $error[] = "Data de SAÍDA inferior ou igual a data de ENTRADA";

                // RN02-05: Internações, do mesmo paciente, cujo período de internação (data de ENTRADA até a data de SAIDA) conflite com o período de uma internação cadastrada previamente. 
                    if ( $patientByNameBirth AND $patientByNameBirth->hospitalizations )
                    {
                        foreach ($patientByNameBirth->hospitalizations as $hospitalizations)
                        {
                            $hospitalizationDateIn = Carbon::createFromFormat('d/m/Y', FormatHelper::invertDate($hospitalizations->date_in));
                            $hospitalizationDateOut = Carbon::createFromFormat('d/m/Y', FormatHelper::invertDate($hospitalizations->date_out));
                            
                            if ($dateInCarbon->between($hospitalizationDateIn, $hospitalizationDateOut))
                                $error[] = "Conflito com a data de ENTRADA";

                            if ($dateOutCarbon->between($hospitalizationDateIn, $hospitalizationDateOut))
                                $error[] = "Conflito com a data de SAÍDA";
                        }
                    }

            // Status
                if (count($error)) {
                    $invalidTotal++;
                    $error['uuid'] = $uuid;
                    $invalidItems[] = $error;
                } else {
                    $validTotal++;
                    $ok['uuid'] = $uuid;
                    $validItems[] = $ok;
                }
        }

        return [
            'valid_items' => count($validItems) ? $validItems : null,
            'invalid_items' => count($invalidItems) ? $invalidItems : null,
            'valid_total' => $validTotal,
            'invalid_total' => $invalidTotal,
        ];
    }

}
