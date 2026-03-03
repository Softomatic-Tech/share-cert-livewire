<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Appendix - 2</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.6;
        }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .mt-20 { margin-top: 20px; }
        .mt-10 { margin-top: 10px; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 5px;
            text-align: left;
        }
    </style>
</head>
<body>

<div class="center bold">
    APPENDIX – 2 <br>
    [Under the Bye-law No. 19(a)]
</div>

<div class="center mt-10">
    The Form of application for membership of the Society by an individual
</div>

<div class="mt-20">
    To,<br>
    The Chief Promoter/Secretary<br>
    <strong><u>{{ strtoupper($society->society_name ?? '_______') }}</u></strong> Co-operative Housing Society ltd
</div>

<div class="mt-10">
    Sir,<br />
    I, Shri/Smt. <strong><u>{{ strtoupper($byelaws->applicant_name ?? '_______') }}</u></strong> hereby make an application 
    for membership of the <strong><u>{{ strtoupper($society->society_name ?? '_______') }}</u></strong> Co-operative Housing Society ltd.<br />
    I intend to settle down and reside in the area of operation of the society<br />
    My particulars for the purpose of consideration of this application are as under:<br />
    Age:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong><u>{{ $byelaws->age ?? '_______' }} Years</u></strong> <br>
    Occupation:&nbsp;&nbsp;&nbsp;<strong><u>{{ strtoupper($byelaws->occupation) ?? '_______' }}</u></strong> <br>
    Monthly Income:&nbsp;&nbsp;&nbsp;<strong><u>Rs. {{ $byelaws->monthly_income ?? '_______' }}</u></strong> <br>
    Office Address:&nbsp;<strong><u>{{ strtoupper($byelaws->office_addr) ?? '_______' }}</u></strong> <br>
    Residential Address:&nbsp;<strong><u>{{ strtoupper($byelaws->residential_addr) ?? '_______' }}</u></strong><br>
    I have purchased the flat No <strong><u>{{ $apartment->apartment_number ?? '_______' }}</u></strong> in the building, named numbered as <strong><u>{{ strtoupper($apartment->building_name) ?? '_______' }}</u></strong> admeasuring <strong><u>{{ $byelaws->flat_area_sq_meters ?? '_______' }}</u></strong> sq. meters from the Promoters (Builders) or Shri / Shrimati / Messrs <strong><u>{{ strtoupper($byelaws->builder_name) ?? '_______' }}</u></strong> under an agreement under Section 4 of the Ownership Flats, Act a copy of which, duly attested is enclosed.<br/> I declare that the said agreement is duly stamped as required under Bombay Stamp Act-19 as to registration, the copy of which is enclosed.
    <p class="text-center"><b>OR</b></p>
    I give below the particulars of the Plot/ flat/ house owned by me or by any of the members of my family or the person dependent on me in the area of operation of the society:
</div>

<table>
    <tr>
        <th>Sr. No.</th>
        <th>Name of the person</th>
        <th>Particulars of the plot / Flat / house owned by the applicant or any of the members of his family or the person dependent in the area of operation of the Society.</th>
        <th>Location of	the plot/flat house</th>
        <th>Reason as to why it is necessary to have a flat in this society.</th>
    </tr>
    <tr>
        <td>1</td>
        <td>{{ strtoupper($byelaws->other_person_name1 ?? '_______') }}</td>
        <td>{{ $byelaws->other_property_particulars1 ?? '_______' }}</td>
        <td>{{ $byelaws->other_property_location1 ?? '_______' }}</td>
        <td>{{ $byelaws->reason_for_flat1 ?? '_______' }}</td>
    </tr>
    <tr>
        <td>2</td>
        <td>{{ strtoupper($byelaws->other_person_name2 ?? '_______') }}</td>
        <td>{{ $byelaws->other_property_particulars2 ?? '_______' }}</td>
        <td>{{ $byelaws->other_property_location2 ?? '_______' }}</td>
        <td>{{ $byelaws->reason_for_flat2 ?? '_______' }}</td>
    </tr>
</table>

<div class="mt-20">
    I remit herewith a sum of Rs. 500 /- towards value of 10 shares of Rs. 50 each and Rs. 100 for entrance fee.<br />I undertake to discharge all the present and future liabilities to the society
    <p class="text-center"><b>OR</b></p>
    As I have no independent source of income, I enclose herewith the undertaking, in the prescribed form the person, on whom I am dependent to the effect that he will discharge all the present and future liabilities to the society on my behalf.<br />
    I also enclose the undertaking and the declaration in the prescribed forms about registration of the proposed acquisition of right over the flat under Section 269-AB, under the Income Tax Act.<br />
    I undertake to use the flat for the purpose for which it is purchased by me and that any change of user will be made with the prior approval of the Society. An undertaking to that effect in the prescribed form is enclose herewith.<br />
    I have gone through the registered Bye-law of the society and undertake to abide by the same and any modification the Registering Authority may make in them.<br/>
    I request you to please admit me as the member of the society
</div>
<div class="mt-20">
    Place: _____________ <br>
    Date: ______________
</div>

<div class="mt-20 text-right">
    Yours faithfully,<br><br />
    <strong><u>{{ strtoupper($byelaws->applicant_name ?? '_______') }}</u></strong><br />
    (Signature of the Applicant)
</div>
<div class="mt-10">
        Attested by,<br/>
        Chief Promoters / Chairman<br/>
        The expression “a member of family” means as defined under bye-law No. 3 (xxv).
    </p>
</div>
</body>
</html>