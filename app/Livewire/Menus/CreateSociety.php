<?php

namespace App\Livewire\Menus;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;
use App\Models\Society;
use App\Models\SocietyDetail;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Response;
class CreateSociety extends Component
{
    use WithFileUploads;
    public $currentStep = 1;
    public $csv_file;
    public $agreement_copy;
    public $membership_form;
    public $allotment_letter;
    public $possesion_letter;
    public $societyId = null;
    public $csvUploaded = false;

    public $formData = [
        'society_name' => '',
        'total_flats'=>'',
        'address_1' => '',
        'address_2' => '',
        'pincode' => '',
        'state' => '',
        'city' => '',
    ];

    public $verification_document;
    // Validation rules for each step
    protected $rules = [
        1 => [
            'formData.society_name' => 'required|string|max:255',
            'formData.address_1' => 'required|string|max:255',
            'formData.address_2' => 'nullable|string|max:255',
            'formData.pincode' => 'required|numeric|digits:6',
            'formData.state' => 'required|string|max:255',
            'formData.city' => 'required|string|max:255',
            'formData.total_flats' => 'required|numeric',
        ],
        2=>[],
        3=>[]
    ];

    public function render()
    {
        return view('livewire.menus.create-society');
    }

    public function getUploadedDetailsProperty()
    {
        if ($this->societyId) {
            return SocietyDetail::where('society_id', $this->societyId)->get();
        }
        return collect();
    }

    public function getSocietyNameProperty()
    {
        if ($this->societyId) {
            return Society::find($this->societyId)?->society_name;
        }
        return '';
    }

    public function nextStep()
    {
        if ($this->currentStep == 1) {
            $this->validate($this->rules[$this->currentStep] ?? []);
            $this->save(); // Save Step 1 before moving
            if (!$this->societyId) {
                return; // Do not move if society not saved
            }
        }

        if ($this->currentStep == 2) {
            if (SocietyDetail::where('society_id', $this->societyId)->count() === 0) {
                session()->flash('error', 'Please upload society details CSV before proceeding.');
                return;
            }
        }
        $this->currentStep++;
    }

    public function prevStep()
    {
        $this->currentStep--;
    }

    public function save()
    {
        $this->validate($this->rules[$this->currentStep] ?? []);
        if ($this->societyId) {
            $society = Society::find($this->societyId);
            $society->update($this->formData);
        }else{
            $society=Society::create($this->formData);
            $this->societyId = $society->id;
        }
        if($society){
            session()->flash('success', 'Society information saved successfully!');
            $this->currentStep = 1; // Reset to first step
        }else{
            session()->flash('error', 'Society information could not be saved due to some error!');
        }
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

    public function csvImport()
    {
        $this->validate([
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);
        $path = $this->csv_file->store('temp');
        $fullPath = Storage::path($path);
        $file = fopen($fullPath, 'r');  
        $header = fgetcsv($file); 
        $requiredHeaders = ['building_name', 'apartment_number','owner1_first_name', 'owner1_mobile'];   
        $headerMap = array_map('trim', $header);
        foreach ($requiredHeaders as $required) {
            if (!in_array($required, $headerMap)) {
                session()->flash('error', "CSV is missing required column: {$required}");
                fclose($file);
                return;
            }
        }
        $indexes = array_flip($headerMap);
        $dataRows = [];
        $insertedCount = 0;
        $validRows = [];
        $invalidRows = [];
        $rowNumber = 2;
        $society = Society::find($this->societyId);
        $expectedFlats = (int) $society->total_flats;
        while (($data = fgetcsv($file)) !== FALSE) {
            $buildingName = $data[$indexes['building_name']] ?? null;
            $apartmentNumber = $data[$indexes['apartment_number']] ?? null;
            $owner1First = $data[$indexes['owner1_first_name']] ?? null;
            $owner1Mobile = $data[$indexes['owner1_mobile']] ?? null;

            if (empty($buildingName) || empty($apartmentNumber) || empty($owner1First) || empty($owner1Mobile)) {
                $invalidRows[] = $rowNumber;
            } else {
                $validRows[] = $data;
            }
                $rowNumber++;
        }
        fclose($file);

        $csvFlats = count($validRows);
        if ($csvFlats !== $expectedFlats) {
            session()->flash('error', "CSV must contain exactly {$expectedFlats} valid flat entries. Found {$csvFlats}. Row(s) skipped: " . implode(', ', $invalidRows));
            return;
        }
        foreach ($validRows as $data) {
            $owner1_name = trim($data[$indexes['owner1_first_name']] . ' ' . ($data[$indexes['owner1_middle_name']] ?? '') . ' ' . ($data[$indexes['owner1_last_name']] ?? ''));
            $owner2_name = trim(($data[$indexes['owner2_first_name']] ?? '') . ' ' . ($data[$indexes['owner2_middle_name']] ?? '') . ' ' . ($data[$indexes['owner2_last_name']] ?? ''));
            $owner3_name = trim(($data[$indexes['owner3_first_name']] ?? '') . ' ' . ($data[$indexes['owner3_middle_name']] ?? '') . ' ' . ($data[$indexes['owner3_last_name']] ?? ''));
            SocietyDetail::create([
                'society_id' => $this->societyId,
                'building_name' => $data[$indexes['building_name']],
                'apartment_number' => $data[$indexes['apartment_number']],
                'owner1_name' => $owner1_name,
                'owner1_mobile' => $data[$indexes['owner1_mobile']] ?? null,
                'owner1_email' => $data[$indexes['owner1_email']] ?? null,
                'owner2_name' => $owner2_name ?: null,
                'owner2_mobile' => $data[$indexes['owner2_mobile']] ?? null,
                'owner2_email' => $data[$indexes['owner2_email']] ?? null,
                'owner3_name' => $owner3_name ?: null,
                'owner3_mobile' => $data[$indexes['owner3_mobile']] ?? null,
                'owner3_email' => $data[$indexes['owner3_email']] ?? null,
            ]);
            $insertedCount++;
        }

        session()->flash('success', "{$csvFlats} entries inserted successfully.");
    }

    public function updateStatus($id){
        $detail = SocietyDetail::find($id);
        if ($detail) {
            $detail->status = 'verified';
            $detail->save();
            session()->flash('success','Status has been verified');
        }else{
            session()->flash('error','Society details not exist');
        }
    }

    public function done()
    {
        // Optional: Clear previous form state
        $this->reset([
            'formData',
            'csv_file',
            'verification_document',
            'societyId',
            'currentStep',
        ]);

        // Reset formData with default values
        $this->formData = [
            'society_name' => '',
            'total_flats' => '',
            'address_1' => '',
            'address_2' => '',
            'pincode' => '',
            'state' => '',
            'city' => '',
        ];

        $this->currentStep = 1;

        session()->flash('success', 'Society and its details have been saved successfully!');
    }

    public function agreementCopy()
    {
        $this->validate([
            'agreement_copy' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,csv,xls,xlsx|max:2048',
        ]);

        $fileName = time().'.'.$this->agreement_copy->getClientOriginalExtension();
        $path = $this->agreement_copy->storeAs('society_docs', $fileName, 'public');

        // $society = Society::find($this->societyId);

        // if ($society) {
        //     $society->agreement_copy = $fileName;
        //     $society->save();

            session()->flash('success', 'File uploaded and saved successfully!');
        // } else {
        //     session()->flash('error', 'Society not found.');
        // }
        $this->reset('agreement_copy');
    }

    public function memberShipForm()
    {
        $this->validate([
            'membership_form' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,csv,xls,xlsx|max:2048',
        ]);

        $fileName = time().'.'.$this->membership_form->getClientOriginalExtension();
        $path = $this->membership_form->storeAs('society_docs', $fileName, 'public');

        session()->flash('success', 'File uploaded and saved successfully!');
        $this->reset('membership_form');
    }

    public function allotmentLetter()
    {
        $this->validate([
            'allotment_letter' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,csv,xls,xlsx|max:2048',
        ]);

        $fileName = time().'.'.$this->allotment_letter->getClientOriginalExtension();
        $path = $this->allotment_letter->storeAs('society_docs', $fileName, 'public');

        session()->flash('success', 'File uploaded and saved successfully!');
        $this->reset('allotment_letter');
    }

    public function possessionLetter()
    {
        $this->validate([
            'possesion_letter' => 'required|file|mimes:jpeg,png,jpg,gif,pdf,csv,xls,xlsx|max:2048',
        ]);

        $fileName = time().'.'.$this->possesion_letter->getClientOriginalExtension();
        $path = $this->possesion_letter->storeAs('society_docs', $fileName, 'public');

        session()->flash('success', 'File uploaded and saved successfully!');
        $this->reset('possesion_letter');
    }
}
