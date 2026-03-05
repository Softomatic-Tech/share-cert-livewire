<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Appendix - 20(1)</title>
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
    </style>
</head>
<body>

<div class="center bold">
    APPENDIX – 20(1) <br>
    [Under the Bye-law No. 38(a)]
</div>

<div class="center mt-10">
    A form of Notice of, intension of a member to transfer his shares and interest in the capital/property of the Society
</div>

<div class="mt-20">
    To,<br />
    The Secretary,<br />
    <strong><u>{{ strtoupper($society->society_name ?? '_______') }}</u></strong> Co-operative Housing Society Ltd,<br />
    <strong><u>{{ strtoupper($society->address_1 ?? '_______') }},{{ strtoupper($society->state->name ?? '_______') }},{{ strtoupper($society->city->name ?? '_______') }}</u></strong> 
</div>
<div class="mt-10">
    Sir,<br />
    Shri/Shrimati/ M/s <strong><u>{{ strtoupper($apartment->owner1_name ?? '_______') }} , {{ strtoupper($apartment->owner2_name ?? '_______') }} , {{ strtoupper($apartment->owner3_name ?? '_______') }}</u></strong> are the members of <strong><u>{{ strtoupper($society->society_name ?? '_______') }}</u></strong> Co-operative Housing Society Ltd. Having address at <strong><u>{{ strtoupper($society->address_1 ?? '_______') }},{{ strtoupper($society->state->name ?? '_______') }},{{ strtoupper($society->city->name ?? '_______') }}</u></strong>  and are holding Ten/Twenty fully paid up shares of Rs.50/- bearing distinctive numbers from <strong><u>{{ strtoupper($byelaws->distinctive_no_from ?? '_______') }}</u></strong> To <strong><u>{{ ($byelaws->distinctive_no_to ?? '_______') }}</u></strong> (both) inclusive and are holding
    the flat / tenement no. <strong><u>{{ $apartment->apartment_number ?? '_______' }}</u></strong> in the building no <strong><u>{{ ($byelaws->building_no ?? '_______') }}</u></strong> Of the society, and hereby give you notice as required under Rule 24 of the Maharashtra Co-operative Societies Rules, 1961, as under.<br />
    I/We Shri/ Shrimati/M/s <strong><u>{{ strtoupper($apartment->owner1_name ?? '_______') }} , {{ strtoupper($apartment->owner2_name ?? '_______') }} , {{ strtoupper($apartment->owner3_name ?? '_______') }}</u></strong> Intend to transfer my/our shares and my/our ownership right, title and interest in the capital in the flat / tenement in the building of the Society, and My/our interest in the property of the society to Shri. / Shrimati / M/s <strong><u>{{ strtoupper($byelaws->transferee_name ?? '_______') }}</u></strong> For a consideration of Rs. <strong><u>{{ strtoupper($byelaws->transfer_fee ?? '_______') }}</u></strong>
    The consent of the transferee is enclosed.
</div>

<div class="mt-10">
    Place: _____________ <br>
    Date: ______________
</div>

<div class="mt-10 text-right">
    Yours Faithfully,<br />
    <strong><u>{{ strtoupper($apartment->owner1_name ?? '_______') }}</u></strong><br />
    (Transferor)
</div>
<div class="mt-10">
    Enclosed: Consent letter from the transferee
</div>
</body>
</html>