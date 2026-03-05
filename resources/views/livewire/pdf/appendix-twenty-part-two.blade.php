<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Appendix - 20(2)</title>
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
    APPENDIX – 20(2) <br>
    [Under the Bye-law No. 38(a)]
</div>

<div class="center mt-10">
    A form of the letter of consent of the proposed Transferee for the transfer of the shares and interest of the member (Transferor) to him (Transferee).
</div>

<div class="mt-20">
    To,<br />
    The Secretary,<br />
    <strong><u>{{ strtoupper($society->society_name ?? '_______') }}</u></strong> Co-operative Housing Society Ltd,
</div>
<div class="mt-10">
    Sir,<br />
    Shri/Shrimati/ M/s <strong><u>@if($apartment->owner1_name){{ strtoupper($apartment->owner1_name ?? '_______') }} @endif @if($apartment->owner2_name), {{ strtoupper($apartment->owner2_name ?? '_______') }}@endif @if($apartment->owner3_name), {{ strtoupper($apartment->owner3_name ?? '_______') }} @endif</u></strong> are the members of <strong><u>{{ strtoupper($society->society_name ?? '_______') }}</u></strong> Co-operative Housing Society Ltd. Proposes to transfer his / her / their shares and interest in the capital/property of the society to me/us. I/We hereby give my/our consent for the transfer of shares and interest of Shri/ Shrimati./M/s. <strong><u>{{ strtoupper($byelaws->transferee_name ?? '_______') }}</u></strong> in the capital/property of the society to me/us as required under Rule 24(1) (b) of the Maharashtra Co-operative Societies Rules, 1961. My/our name and address is as under.<br />
    My/our name and address is as under.<br />
    @if($apartment->owner1_name)
        <strong><u>{{ strtoupper($apartment->owner1_name ?? '_______') }} , {{ strtoupper($apartment->owner1_email ?? '_______') }} , {{ strtoupper($apartment->owner1_mobile ?? '_______') }}</u></strong><br />
    @endif
    @if($apartment->owner2_name)
    <strong><u>{{ strtoupper($apartment->owner2_name ?? '_______') }} , {{ strtoupper($apartment->owner2_email ?? '_______') }} , {{ strtoupper($apartment->owner2_mobile ?? '_______') }}</u></strong><br />
    @endif
    @if($apartment->owner3_name)
    <strong><u>{{ strtoupper($apartment->owner3_name ?? '_______') }} , {{ strtoupper($apartment->owner3_email ?? '_______') }} , {{ strtoupper($apartment->owner3_mobile ?? '_______') }}</u></strong>
    @endif

</div>

<div class="mt-10">
    Place: _____________ <br />
    Date: ______________
</div>

<div class="mt-10 text-right">
    Yours Faithfully,<br />
    <strong><u>{{ strtoupper($byelaws->transferee_name ?? '_______') }}</u></strong><br />
    (Transferee)
</div>
</body>
</html>