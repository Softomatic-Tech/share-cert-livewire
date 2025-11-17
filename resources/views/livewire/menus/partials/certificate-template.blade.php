<div class="watermark">TENTATIVE</div>

<div class="header-line">
  <span>Share Certificate No. <span class="underline">{{ $details->certificate_no ?? '01' }}</span></span>
  <span>Member's Regn. No. <span class="underline">{{ $details->member_reg_no ?? '01' }}</span></span>
  <span>No. of Shares <span class="underline">{{ $details->society->no_of_shares ?? '0' }}</span></span>
</div>

<div class="title">SHARE CERTIFICATE</div>
<div class="section" style="text-align:center;">
  Authorised Share Capital of Rs.
  @php 
    $count=0;
    $details->divided_into=$details->share_to=$details->society->total_flats;
  @endphp
  <span class="underline">{{ $details->share_capital_amount ?? '0' }}</span>
  Divided into
  <span class="underline">{{ $details->divided_into ?? '0' }}</span>
  shares of Rs. {{ $details->society->share_value ?? '0' }}/- each
</div>

<div class="society-name">{{ $details->society->society_name }}</div>
<div class="society-address">
  Sr. No. 9 to 14, @if($details->society->address_1){{ $details->society->address_1 }},@endif @if($details->society->city?->name){{ $details->society->city?->name  }},@endif @if($details->society->state?->name){{ $details->society->state?->name  }},@endif @if($details->society->pincode){{ $details->society->pincode }}@endif<br>
  Reg No. @if($details->society->registration_no) {{ $details->society->registration_no ?? '' }} @endif Dt. {{ date('d-m-Y') }}
</div>

<div class="section">
  This is to certify that Shri/Smt./M/s:
  <br><strong>{{ $details->owner1_name ?? ' ' }}</strong>
  <br><strong>{{ $details->owner2_name ?? ' ' }}</strong>
  <br><br>
  is/are the registered holder/s of
  <span class="underline">{{ $details->society->no_of_shares ?? '0' }}</span>
  fully paid-up shares of Rs. {{ $details->society->share_value }}/- each, numbered from
  <span class="underline">{{ $details->share_from ?? '01' }}</span>
  to <span class="underline">{{ str_pad($details->share_to, 2, '0', STR_PAD_LEFT) ?? '0' }}</span>
  both inclusive, in Flat No.
  <span class="underline">{{ $details->apartment_number ?? ' ' }}</span>
  Tower No.
  <span class="underline">{{ $details->building_name ?? ' ' }}</span>
  of <span class="society-name">{{ $details->society->society_name }}</span>, subject to the Bye-laws of the said Society.
</div>

<div class="section">
  Given under the Common Seal of the Society at Pune on this
  <span class="underline" style="width:60px;"></span> day of
  <span class="underline" style="width:80px;"></span> 20<span class="underline" style="width:20px;"></span>.
</div>
<table style="width: 100%;">
    <tr>
        <td style="width: 25%;"></td>
        <td style="width: 25%;">Secretary</td>
        <td style="width: 25%;">Chairman</td>
        <td style="width: 25%;">Authorised<br>M/c Member</td>
    </tr>
</table>
