<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Appendix - 21</title>
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
    APPENDIX – 21 <br>
    [Under the Bye-law No. 38(e) (i)]
</div>

<div class="center mt-10">
    Form of application for transfer of shares and interest in the capital/ property of the society by the transferor (being an individual)
</div>

<div class="mt-20">
    To,<br />
    The Secretary,<br />
    <strong><u>{{ strtoupper($society->society_name ?? '_______') }}</u></strong> Co-operative Housing Society Ltd,
</div>
<div class="mt-10">
    Sir,<br />
    1. I Shri/Shrimati <strong><u>{{ strtoupper($byelaws->transferee_name ?? '_______') }}</u></strong> am the member of <strong><u>{{ strtoupper($society->society_name ?? '_______') }}</u></strong> Cooperative Housing Society Ltd., having address at <strong><u>{{ strtoupper($society->address_1 ?? '_______') }} , {{ strtoupper($society->state->name ?? '_______') }} , {{ strtoupper($society->city->name ?? '_______') }}</u></strong> and holding share certificate No <strong><u>{{ $apartment->certificate_no ?? '_______' }}</u></strong> for ten fully, paid up shares of Rupees fifty each, bearing distinctive numbers from <strong><u>{{ $byelaws->distinctive_no_from ?? '_______' }}</u></strong> to <strong><u>{{ $byelaws->distinctive_no_to ?? '_______' }}</u></strong> (both inclusive) and holding Flat / Tenement No <strong><u>{{ strtoupper($apartment->apartment_number ?? '_______') }}</u></strong> admeasuring <strong><u>{{ $byelaws->flat_area_sq_meters ?? '_______' }}</u></strong> sq. meters in the building of the society, numbered / known as <strong><u>{{ strtoupper($apartment->building_name ?? '_______') }}</u></strong><br />
    2.	I had given you notice of my intention to transfer the said shares and my interest in the capital / property of the society on as required under Rule 24 (1) (b) of Maharashtra Co-operative Societies Rules, 1961 along with the consent of the proposed transferee Shri / Shrimati / Ms <strong><u>{{ strtoupper($byelaws->transferee_name ?? '_______') }}</u></strong>.<br />
    3.	I enclose herewith the application in the prescribed form for membership of the said society by the said proposed transferee.<br />
    4.	I remit here with the transfer fee of Rs. 500/- (Rupees Five Hundred only ) I also remit herewith the amount of the premium of Rs <strong><u>{{ $byelaws->transfer_premium_amount ?? '_______' }}</u></strong> (Rupees <strong><u>{{ $byelaws->transfer_premium_amount ?? '_______' }}</u></strong> only) as provided under bye-law No. 38 (e) (ix) of the bye laws of the society.<br />
    5.	I state that the said shares and interest in the capital /property of the said society have been held by me for a period of not less than a year.<br />
    6.	I further state that the liabilities due to the said society by me as on the date of this application have been fully paid by me. I also undertake to pay the liabilities which may become due till the transfer application is approved by the society.<br />
    7.	I hereby undertake to discharge any liabilities to the said society which related to the period of my membership with the said society and have before payable by me after cessation of my membership of the society due to any demand made by the local authority, Government or by any other Authority on any account, after cessation of my membership.<br />
    8.	I propose to transfer the said shares and my interest in the capital / property of the said society on the following grounds.<br />
    i)	<strong><u>{{ $byelaws->transfer_ground_1 ?? '_______' }}</u></strong><br />
    ii)	<strong><u>{{ $byelaws->transfer_ground_2 ?? '_______' }}</u></strong><br />
    iii)	<strong><u>{{ $byelaws->transfer_ground_3 ?? '_______' }}</u></strong><br />
    9.	I also furnish herewith the undertaking in the prescribed form on One Hundred Rupees stamp paper about the registration of the transfer as required under section 269-A-B of Income Tax Act<br />
    10.	I request you to approve the proposed transfer and inform me accordingly.
</div>

<div class="mt-10">
    Place: _____________ <br />
    Date: ______________
</div>

<div class="mt-10 text-right">
    Yours Faithfully,<br />
    <strong><u>{{ strtoupper($byelaws->transferor_name) ?? '_______' }}</u></strong><br />
    (Signature of the Transferor)
</div>
</body>
</html>