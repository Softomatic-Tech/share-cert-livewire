<?php

namespace App\Livewire\Menus;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Society;
use App\Models\SocietyDetail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CreateApartment extends Component
{
    use WithFileUploads;
    public $csv_file,$society_id;
    public $society = [];

    public function render()
    {
        return view('livewire.menus.create-apartment');
    }

    public function redirectToApartmentPage()
    {
        return redirect()->route('admin.view-apartments');
    }

    public function mount()
    {
        $this->society =Society::all();
    }

    public function csvExport(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sample_society_details.csv"',
        ];
    
        $columns = [
            'building_name',
            'apartment_number',
            'certificate_no',
            'no_of_shares',
            'owner1_first_name', 'owner1_middle_name', 'owner1_last_name',
            'owner1_mobile', 'owner1_email',
            'owner2_first_name', 'owner2_middle_name', 'owner2_last_name',
            'owner2_mobile', 'owner2_email',
            'owner3_first_name', 'owner3_middle_name', 'owner3_last_name',
            'owner3_mobile', 'owner3_email',
        ];
    
        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns); // write headers only
            fclose($file);
        };
    
        return response()->stream($callback, 200, $headers);
    }

    public function saveApartment()
    {
        $this->validate([
            'society_id' => 'required|exists:societies,id',
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);
        $path = $this->csv_file->store('temp');
        $fullPath = Storage::path($path);
        $file = fopen($fullPath, 'r');  
        if (!$file) {
            $this->dispatch('show-error', message: 'Unable to open the uploaded CSV file.');
            return;
        }
        $header = fgetcsv($file); 
        $requiredHeaders = ['building_name', 'apartment_number','certificate_no','no_of_shares','owner1_first_name', 'owner1_mobile'];   
        $headerMap = array_map('trim', $header);
        foreach ($requiredHeaders as $required) {
            if (!in_array($required, $headerMap)) {
                $this->dispatch('show-error', message: 'CSV is missing required column: {$required}');
                fclose($file);
                return;
            }
        }
        $indexes = array_flip($headerMap);
        $insertedCount = 0;
        $validRows = [];
        $invalidRows = [];
        $rowNumber = 2;
        $shareMismatchError ='';
        $totalCsvShares = 0;
        $society = Society::find($this->society_id);
        $expectedFlats = (int) $society->total_flats;
        $expectedShares = (float) $society->no_of_shares;
        while (($data = fgetcsv($file)) !== FALSE) {
            $buildingName = $data[$indexes['building_name']] ?? null;
            $apartmentNumber = $data[$indexes['apartment_number']] ?? null;
            $certificateNo = $data[$indexes['certificate_no']] ?? null;
            $noOfShares = $data[$indexes['no_of_shares']] ?? null;
            $owner1First = $data[$indexes['owner1_first_name']] ?? null;
            $owner1Mobile = $data[$indexes['owner1_mobile']] ?? null;

            if (empty($buildingName) || empty($apartmentNumber) || empty($certificateNo) || empty($noOfShares) || empty($owner1First) || empty($owner1Mobile)) {
                $invalidRows[] = $rowNumber;
            } else {
                if (!is_numeric($noOfShares)) {
                    $invalidRows[] = $rowNumber;
                } else {
                    $totalCsvShares += (float) $noOfShares;
                    $validRows[] = $data;
                }
            }
                $rowNumber++;
        }
        fclose($file);

        $csvFlats = count($validRows);
        if ($csvFlats !== $expectedFlats) {
            $this->dispatch('show-error', message:  "CSV must contain exactly {$expectedFlats} valid flat entries. Found {$csvFlats}. Row(s) skipped: " . implode(', ', $invalidRows));
            return;
        }
        if ($totalCsvShares != $expectedShares) {
            $diff = $totalCsvShares - $expectedShares;
            $status = $diff > 0 ? 'more' : 'less';
            $shareMismatchError = " and Total shares mismatch! Expected {$expectedShares}, but found {$totalCsvShares} in CSV ({$status} by " . abs($diff) . ").";
        }
        foreach ($validRows as $data) {
            $owner1_name = trim($data[$indexes['owner1_first_name']] . ' ' . ($data[$indexes['owner1_middle_name']] ?? '') . ' ' . ($data[$indexes['owner1_last_name']] ?? ''));
            $owner2_name = trim(($data[$indexes['owner2_first_name']] ?? '') . ' ' . ($data[$indexes['owner2_middle_name']] ?? '') . ' ' . ($data[$indexes['owner2_last_name']] ?? ''));
            $owner3_name = trim(($data[$indexes['owner3_first_name']] ?? '') . ' ' . ($data[$indexes['owner3_middle_name']] ?? '') . ' ' . ($data[$indexes['owner3_last_name']] ?? ''));
            $status=[
                "tasks" => [
                    [
                        "name" => $this->timelineValues[0],
                        "responsibilityOf"=> "ApartmentOwner",
                        "Status" => "Pending",
                        "createdBy" => "System",
                        "createDateTime" => now(),
                        "updatedBy" => null,
                        "updateDateTime" => null
                    ],
                    [
                        "name" => $this->timelineValues[1],
                        "responsibilityOf"=> "ApartmentOwner",
                        "Status" => "Pending",
                        "createdBy" => null,
                        "createDateTime" => now(),
                        "updatedBy" => null,
                        "updateDateTime" => null,
                    ],
                    [
                        "name" => $this->timelineValues[2],
                        "responsibilityOf"=> "DearSociety",
                        "Status" => "Pending",
                        "createdBy" => "System",
                        "createDateTime" => null,
                        "updatedBy" => null,
                        "updateDateTime" => null
                    ],
                    [
                        "name" => $this->timelineValues[3],
                        "responsibilityOf"=> "DearSociety",
                        "Status" => "Pending",
                        "createdBy" => null,
                        "createDateTime" => null,
                        "updatedBy" => null,
                        "updateDateTime" => null
                    ],
                    [
                        "name" => $this->timelineValues[4],
                        "responsibilityOf"=> "DearSociety",
                        "Status" => "Pending",
                        "createdBy" => null,
                        "createDateTime" => null,
                        "updatedBy" => null,
                        "updateDateTime" => null
                    ]
                ]
            ];
            SocietyDetail::create([
                'user_id'=>Auth::id(),
                'society_id' => $this->society_id,
                'building_name' => $data[$indexes['building_name']],
                'apartment_number' => $data[$indexes['apartment_number']],
                'certificate_no' => $data[$indexes['certificate_no']],
                'no_of_shares' => $data[$indexes['no_of_shares']],
                'owner1_name' => $owner1_name,
                'owner1_mobile' => $data[$indexes['owner1_mobile']] ?? null,
                'owner1_email' => $data[$indexes['owner1_email']] ?? null,
                'owner2_name' => $owner2_name ?: null,
                'owner2_mobile' => $data[$indexes['owner2_mobile']] ?? null,
                'owner2_email' => $data[$indexes['owner2_email']] ?? null,
                'owner3_name' => $owner3_name ?: null,
                'owner3_mobile' => $data[$indexes['owner3_mobile']] ?? null,
                'owner3_email' => $data[$indexes['owner3_email']] ?? null,
                'status' => json_encode($status)
            ]);
            $insertedCount++;
        }

        if($insertedCount==$csvFlats){
            $this->dispatch('show-success', message:  "{$csvFlats} entries inserted successfully{$shareMismatchError}!");
            $this->reset('csv_file','society_id');
        }else{
            $this->dispatch('show-error', message:  "Society information could not be saved due to some error!");
        }
    }
}
