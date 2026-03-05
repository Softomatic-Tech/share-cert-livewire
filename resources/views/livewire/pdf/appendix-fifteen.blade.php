<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Appendix - 15</title>
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
    APPENDIX – 15 <br>
    [Under the Bye-law No. 34]
</div>

<div class="center mt-10">
    The Form of application for Membership by the Nominee/Nominees
</div>

<div class="mt-20">
    To,<br>
    The Secretary<br>
    <strong><u>{{ strtoupper($society->society_name ?? '_______') }}</u></strong> Co-operative Housing Society Ltd,<br />
    <strong><u>{{ strtoupper($society->address_1 ?? '_______') }},{{ strtoupper($society->state->name ?? '_______') }},{{ strtoupper($society->city->name ?? '_______') }}</u></strong>
</div>

<div class="mt-10">
    Sir,
    I/We Shri/Smt./Messrs <strong><u>{{ strtoupper($byelaws->applicant_name ?? '_______') }}</u></strong> hereby make an application for membership of the <strong><u>{{ strtoupper($society->society_name ?? '_______') }}</u></strong> Co-operative Housing Society Ltd. <strong><u>{{ strtoupper($society->address_1 ?? '_______') }},{{ strtoupper($society->state->name ?? '_______') }},{{ strtoupper($society->city->name ?? '_______') }}</u></strong> and for transfer of shares and interest of Shri/Shrimati <strong><u>{{ strtoupper($byelaws->deceased_member_name ?? '_______') }}</u></strong> Deceased member of the society, in the capital/ property of the society.<br />
    Shri/Shrimati <strong><u>{{ strtoupper($byelaws->deceased_member_name ?? '_______') }}</u></strong> was a member of the society holding <strong><u>{{ $byelaws->society_shares ?? '_______' }}</u></strong> shares of Rs. fifty each and Flat No <strong><u>{{ $apartment->apartment_number ?? '_______' }}</u></strong> in the society’s building.<br />
    Shri/Shrimati <strong><u>{{ strtoupper($byelaws->deceased_member_name ?? '_______') }}</u></strong> the deceased member of the society died on <strong><u>{{ strtoupper($byelaws->date_of_death ?? '_______') }}</u></strong>.<br />
    A copy of the death certificate of the said member is enclosed.<br />
    The late Shri/Shrimati <strong><u>{{ strtoupper($byelaws->deceased_member_name ?? '_______') }}</u></strong> the deceased member of the society had nominated me/us under Rule 25 of the Maharashtra Co-operative Societies Rules, 1961.<br />
    Being the only nominee/ authorised nominees as per nomination filed with the society by the deceased member, I/We am /are entitled to make an application for membership of the society and from transfer of shares and interest of the deceased member in the capital/property of eth society to my/ our name.<br />
    I/We have executed the Indemnity Bond in favour of the society indemnifying it against any claim made at any subsequent time by other nominee/ nominees to the shares and interest of the deceased member in the capital / property of the society. The said Indemnity Bond is enclosed herewith.[Appendix 18]<br />
    I/We remit herewith an amount of Rs. 100 as entrance fee My particulars for the purpose of consideration of my application for membership of the society are as under:<br />
    Age	:<strong><u>{{ strtoupper($byelaws->age ?? '_______') }}</u></strong><br />
    Occupation	:<strong><u>{{ strtoupper($byelaws->occupation ?? '_______') }}</u></strong><br />
    Monthly Income	:<strong><u>Rs {{ strtoupper($byelaws->monthly_income ?? '_______') }}</u></strong><br />
    Office Address	:<strong><u>{{ strtoupper($byelaws->office_addr ?? '_______') }}</u></strong> <br />
    Residential Address	:<strong><u>{{ strtoupper($byelaws->residential_addr ?? '_______') }}</u></strong> <br />
    I/We enclose herewith the undertaking and declaration, in the prescribed forms, in respect of the registration of transfer of the resignation of transfer of the flat to my/our name under Section 269AB of the Income-Tax Act.<br />
    I/We undertake to discharge the present and future liabilities to the society/ As I have no independent source of income, I enclose herewith the undertaking in the prescribed form from the person, on whom I am dependent to the effect that he will discharge all the present and future liabilities to the society on my behalf. I/We have got understood one through the bye-laws of the society and undertake to abide by the same and any modifications that the Registering Authority may in them. I/We request you to please admit me/us a member of the society and transfer the shares and interest of the deceased member in the capital / property of the society to my/ our name. The share certificate held by the, deceased member is enclosed herewith.
</div>
<div class="mt-10">
    Place: _____________ <br>
    Date: ______________
</div>

<div class="mt-10 text-right">
    Yours faithfully,<br />
    <strong><u>{{ strtoupper($byelaws->applicant_name ?? '_______') }}</u></strong><br />
    (Signature of the Applicant)
</div>
<div class="mt-10">
    <b>Notes:</b><br />
    1.	The expression “a member of a family” means as defined under bye- law 3(xxv).<br/>
    2.	The undertaking about registration of the flat is not necessary if the nominee is related to the deceased member within the meaning of Section 2(41) of the Income-tax Act.
</div>
</body>
</html>