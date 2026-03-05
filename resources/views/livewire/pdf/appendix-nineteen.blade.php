<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Appendix - 19</title>
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
    APPENDIX – 19 <br>
    [Under the Bye-law No. 35]
</div>

<div class="center mt-10">
    <b>FORM OF INDEMNITY BOND</b><br />
    Application for Membership by the Heir of the Deceased Member of the Society.<br />
    (To be given on Stamp Paper of Rs. 200 or to be affixed with adhesive stamps of the same denomination)<br />
    <b>(To be given where there is no nomination)</b>
</div>

<div class="mt-20">
    1.	I, Shri/Shrimati <strong><u>{{ strtoupper($byelaws->applicant_name ?? '_______') }}</u></strong> of <strong><u>{{ strtoupper($byelaws->father_husband_name ?? '_______') }}</u></strong> Indian inhabitant
    state as under.<br />
    2.	Shri/Shrimati <strong><u>{{ strtoupper($byelaws->deceased_member_name ?? '_______') }}</u></strong> residing at who was the member of the Cooperative Housing Society Ltd. having address at <strong><u>{{ strtoupper($byelaws->residential_addr ?? '_______') }}</u></strong> died on or about<br />
    3.	The said Shri/ Shrimati <strong><u>{{ strtoupper($byelaws->deceased_member_name ?? '_______') }}</u></strong> had not made a nomination as provided under Rule 25 of the Maharashtra Co-operative Societies Rules 1961.<br />
    4.	The said Shri/Shrimati <strong><u>{{ strtoupper($byelaws->deceased_member_name ?? '_______') }}</u></strong> was holding the share certificate No. <strong><u>{{ $apartment->certificate_no ?? '_______' }}</u></strong> for ten fully paid up shares of Rupees Fifty each, bearing distinctive number from <strong><u>{{ $byelaws->distinctive_no_from ?? '_______' }}</u></strong> to <strong><u>{{ $byelaws->distinctive_no_to ?? '_______' }}</u></strong> (both inclusive)<br />
    5.	The said Shri/ Shrimati <strong><u>{{ strtoupper($byelaws->deceased_member_name ?? '_______') }}</u></strong>  was holding the flat / tenement no. <strong><u>{{ strtoupper($apartment->apartment_number ?? '_______') }}</u></strong>  on <strong><u>{{ $byelaws->floor_no ?? '_______' }}</u></strong> floor, in the building of the society known as <strong><u>{{ strtoupper($apartment->building_name ?? '_______') }}</u></strong> or in the building No constructed on the plot of land bearing no <strong><u>{{ $byelaws->flat_bearing_no ?? '_______' }}</u></strong> situated at <strong><u>{{ strtoupper($society->address_1 ?? '_______') }}, {{ strtoupper($society->state->name ?? '_______') }} ,{{ strtoupper($society->city->name ?? '_______') }}</u></strong><br />
    6.	The said Shri/Shrimati <strong><u>{{ strtoupper($byelaws->deceased_member_name ?? '_______') }}</u></strong> has left behind me as his/her only heir/the following heirs :<br />
    i)	Shri/Shrimati <strong><u>{{ strtoupper($byelaws->heir_1_name ?? '_______') }}</u></strong><br />
    ii)	Shri/Shrimati <strong><u>{{ strtoupper($byelaws->heir_2_name ?? '_______') }}</u></strong>.<br />
    iii)	Shri/Shrimati <strong><u>{{ strtoupper($byelaws->heir_3_name ?? '_______') }}</u></strong><br />
    iv)	Shri/Shrimati <strong><u>{{ strtoupper($byelaws->heir_4_name ?? '_______') }}</u></strong>
</div>
<div class="mt-10">
    As I am the only heir of the deceased Shri/Shrimati <strong><u>{{ strtoupper($byelaws->deceased_member_name ?? '_______') }}</u></strong> I inherit his/her shares, and his/her
    interest in the said tenement. According to the bye-law No. 35 of the bye-laws of the said society, I am entitled to make an application for membership of the said society and for transfer of the said shares and interest of the said deceased member in the said flat / tenement to my name. Accordingly, I have made an application for transfer of the said shares and the interest of the said deceased member in the said flat to my name.<br />
    <b class="text-center">OR</b><br />
    According to the bye-law No 35 of the bye laws' of the society, all the above heirs have jointly made an affidavit, and have suggested my name to make an application for membership of the said society and for transfer of the said shares and the interest of the said deceased member in the said flat / tenement/ plot of land to my name. Accordingly I have made application for membership of the said society and for transfer of the said shares and the interest of the deceased member in the said flat / tenement to my name.
    <br />
    7.	I hereby indemnify and keep indemnified the said society and its office-bearers against any claim demand, suit or other legal proceedings by any other heir/heirs, either lawfully and/or equitably through the said deceased Shri/Shrimati <strong><u>{{ strtoupper($byelaws->deceased_member_name ?? '_______') }}</u></strong> and shall see that the said society/heirs either lawfully and/or equitably claiming through the said deceased member of the society.<br />
    8.	I am fully aware of the fact that the society admits me as its member in place of the said deceased member of the society only on the basis of the indemnity and undertaking furnished by me. Signatures
</div>
<div class="mt-20">
    Place: _____________ <br>
    Date: ______________
</div>

<div class="mt-20 text-right">
    Signature of Applicant<br />
    <strong><u>{{ strtoupper($byelaws->applicant_name ?? '_______') }}</u></strong><br />
    Signature of nominees other than applicant <br />
    1)………………………………………………………<br />
    2)	………………………………………………………<br />
    3)	………………………………………………………
</div>
<div>
    Witnesses:- <br />
    1)	Name <strong><u>{{ strtoupper($byelaws->witness_name ?? '_______') }}</u></strong> <br />	
    2)	Signature of the Witness <strong><u>{{ strtoupper($byelaws->witness_name ?? '_______') }}</u></strong>.<br />
    3) Address <strong><u>{{ strtoupper($byelaws->witness_address ?? '_______') }}</u></strong>

</div>
</body>
</html>