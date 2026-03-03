<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Appendix - 3</title>
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
    APPENDIX – 3 <br>
    [Undertaking the Bye-law No. 19(a) (iv)]
</div>

<div class="center mt-10">
    A Form of undertaking to be furnished by the Prospective Member to use the flat for the purpose for which it is purchased.
</div>

<div class="mt-10">
    I / We, Shri/ Smt./ Messer’s <strong><u>{{ strtoupper($byelaws->applicant_name ?? '_______') }}</u></strong> Of	,<strong><u>{{ strtoupper($byelaws->father_husband_name ?? '_______') }}</u></strong> Indian Inhabitant,residing at <strong><u>{{ strtoupper($society->society_name ?? '_______') }}</u></strong> Co-operative Housing Society ltd.,<strong><u>{{ strtoupper($society->address_1 ?? '_______') }},{{ strtoupper($society->state->name ?? '_______') }},{{ strtoupper($society->city->name ?? '_______') }}</u></strong>, Mumbai ,<strong><u>{{ $society->pincode ?? '_______' }}</u></strong>, intending Member of the <strong><u>{{ strtoupper($society->society_name ?? '_______') }}</u></strong> Co-op Housing Limited having address at, .,<strong><u>{{ strtoupper($society->address_1 ?? '_______') }},{{ strtoupper($society->state->name ?? '_______') }},{{ strtoupper($society->city->name ?? '_______') }}</u></strong> , Mumbai , <strong><u>{{ $society->pincode ?? '_______' }}</u></strong> hereby give the undertaking that I will use the Flat No <strong><u>{{ $apartment->apartment_number ?? '_______' }}</u></strong> inherited by me, on demise of my late husband Shri <strong><u>{{ strtoupper($byelaws->deceased_member_name) ?? '_______' }}</u></strong>. the earlier member, under the bye-laws of the society for the purpose mentioned in the letter, which will be issued under bye-law No. 75(a) of the bye-laws of the society, registered.<br />
    I further give the undertaking that no change of user will be made by me without the previous permission, in writing of the committee of the society.
</div>
<div class="mt-10">
    Place: _____________ <br/>
    Date: ______________ <br/>

    <p class="text-right">
        Signature<br /> <strong><u>{{ strtoupper($byelaws->applicant_name ?? '_______') }}</u></strong>
    </p>
</div>
</body>
</html>