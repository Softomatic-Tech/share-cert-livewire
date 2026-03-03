<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Appendix - 16</title>
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
    APPENDIX – 16 <br>
    [Under the Bye-law No. 34]
</div>

<div class="center mt-10">
    The Form of Notice, inviting claims or objections to the transfer of the shares and the interest of the Deceased Member in the Capital/ Property of the society.<br />
    <b>(To be published in two local newspapers having large publication)</b><br />
    <b>NOTICE</b>
</div>

<div class="mt-10">
    Shri/Shrimati <strong><u>{{ strtoupper($byelaws->applicant_name ?? '_______') }}</u></strong> a Member of the <strong><u>{{ strtoupper($society->society_name ?? '_______') }}</u></strong> Co-operative Housing Society Ltd. having, address at <strong><u>{{ strtoupper($society->address_1 ?? '_______') }}, {{ strtoupper($society->state->name ?? '_______') }}, {{ strtoupper($society->city->name ?? '_______') }}</u></strong>. and holding flat/ tenement No <strong><u>{{ $apartment->apartment_number ?? '_______' }}</u></strong> in the building of the society, died on <strong><u>{{ strtoupper($byelaws->date_of_death ?? '_______') }}</u></strong> without making any nomination.<br />
    The society hereby invites claims or objections from the heir or heirs or other claimants/ objector or objectors to the transfer of the said shares and interest of the deceased member in the capital/ property of the society within a period of days from the publication of this notice, with copies of such documents and other proofs in support of his/her/their claims/ objections for transfer of shares and interest of the deceased member in the capital/ property of the society. If no claims/ objections are received within the period prescribed above, the society shall be free to deal with the shares and interest of the deceased member in the capital/ property of the society in such manner as is provided under the bye-laws of the society. The claims/ objections, if any, received by the society for transfer of shares and interest of the deceased member in the capital/ property of the society shall be dealt with in the manner provided under the bye-laws of the society. A copy of the registered bye-laws of the society is available for inspection by the claimants/ objectors, in the office of the society/ with the secretary of the society between
    <strong><u>{{ $byelaws->inspection_time_from ?? '_______' }}</u></strong> A. M.I P. M. to <strong><u>{{ $byelaws->inspection_time_to ?? '_______' }}</u></strong> A.M. /P.M. from the date of publication of the notice till the date of expiry of its period. 
</div>
<div class="mt-10">
    Place: _____________ <br>
    Date: ______________
</div>

<div class="mt-10 text-right">
    For and on behalf of <br>
    The <strong><u>{{ strtoupper($society->society_name ?? '_______') }}</u></strong> Co-op.Housing Society Ltd <br>
    Hon. Secretary
</div>
</body>
</html>